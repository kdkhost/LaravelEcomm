<?php

declare(strict_types=1);

namespace Modules\Front\Services;

use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use InvalidArgumentException;
use Modules\Product\Models\Product;
use RuntimeException;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Throwable;

class VirtualTryOnAiService
{
    public function isConfigured(): bool
    {
        return trim((string) config('services.replicate.token')) !== '';
    }

    /**
     * @return array<string, mixed>
     */
    public function status(): array
    {
        return [
            'configurado' => $this->isConfigured(),
            'provedor' => 'Replicate',
            'modelo' => $this->model(),
            'modo' => 'IA generativa real',
            'gd_disponivel' => function_exists('imagecreatefromstring') && function_exists('imagejpeg'),
            'limite_mb' => $this->maxInputMb(),
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public function process(Product $product, string $photoDataUri, string $style = 'realista'): array
    {
        if (! $this->isConfigured()) {
            throw new RuntimeException('Servico de IA nao configurado. Configure REPLICATE_API_TOKEN no arquivo .env.');
        }

        [$photoBytes, $photoMime] = $this->decodeImageDataUri($photoDataUri);
        $productBytes = $this->productImageBytes($product);
        $inputImage = $this->buildInputImageDataUri($photoBytes, $photoMime, $productBytes);
        $prompt = $this->buildPrompt($product, $style, $productBytes !== null);
        $prediction = $this->createPrediction($inputImage, $prompt);
        $completed = $this->waitForCompletion($prediction);
        $imageUrl = $this->extractOutputUrl($completed['output'] ?? null);

        if (! $imageUrl) {
            throw new RuntimeException('A IA concluiu o processamento, mas nao retornou uma imagem valida.');
        }

        return [
            'sucesso' => true,
            'imagem_url' => $imageUrl,
            'prediction_id' => $completed['id'] ?? $prediction['id'] ?? null,
            'status' => $completed['status'] ?? $prediction['status'] ?? null,
            'provedor' => 'Replicate',
            'modelo' => $this->model(),
            'estilo' => $style,
            'entrada' => $productBytes !== null ? 'foto_da_crianca_mais_referencia_visual_da_peca' : 'foto_da_crianca_mais_prompt_textual',
            'privacidade' => 'A loja nao salva a foto enviada. A imagem e enviada temporariamente ao provedor de IA para gerar o resultado.',
        ];
    }

    /**
     * @return array{0: string, 1: string}
     */
    private function decodeImageDataUri(string $dataUri): array
    {
        $dataUri = trim($dataUri);

        if (! preg_match('/^data:image\/(jpeg|jpg|png|webp);base64,([a-zA-Z0-9+\/=\r\n]+)$/', $dataUri, $matches)) {
            throw new InvalidArgumentException('Envie uma foto valida em JPG, PNG ou WEBP.');
        }

        $mime = strtolower($matches[1]) === 'jpg' ? 'jpeg' : strtolower($matches[1]);
        $base64 = str_replace(["\r", "\n"], '', $matches[2]);
        $bytes = base64_decode($base64, true);

        if ($bytes === false || $bytes === '') {
            throw new InvalidArgumentException('Nao foi possivel ler a foto enviada.');
        }

        if (strlen($bytes) > ($this->maxInputMb() * 1024 * 1024)) {
            throw new InvalidArgumentException('A foto precisa ter ate '.$this->maxInputMb().' MB.');
        }

        if (@getimagesizefromstring($bytes) === false) {
            throw new InvalidArgumentException('O arquivo enviado nao parece ser uma imagem valida.');
        }

        return [$bytes, 'image/'.$mime];
    }

    private function productImageBytes(Product $product): ?string
    {
        $media = $product->getFirstMedia('product');

        if ($media instanceof Media) {
            $path = $media->getPath();
            if (is_string($path) && is_file($path)) {
                $bytes = @file_get_contents($path);
                if (is_string($bytes) && $bytes !== '' && @getimagesizefromstring($bytes) !== false) {
                    return $bytes;
                }
            }

            $bytes = $this->downloadImage((string) $media->getUrl());
            if ($bytes !== null) {
                return $bytes;
            }
        }

        return $this->downloadImage((string) $product->imageUrl);
    }

    private function downloadImage(string $url): ?string
    {
        if ($url === '' || str_starts_with($url, 'data:')) {
            return null;
        }

        try {
            $response = Http::timeout(15)->get($url);
        } catch (Throwable) {
            return null;
        }

        if (! $response->successful()) {
            return null;
        }

        $bytes = $response->body();

        return is_string($bytes) && $bytes !== '' && @getimagesizefromstring($bytes) !== false
            ? $bytes
            : null;
    }

    private function buildInputImageDataUri(string $photoBytes, string $photoMime, ?string $productBytes): string
    {
        if (! function_exists('imagecreatefromstring') || ! function_exists('imagejpeg')) {
            return 'data:'.$photoMime.';base64,'.base64_encode($photoBytes);
        }

        if ($productBytes === null) {
            return 'data:image/jpeg;base64,'.base64_encode($this->normalizeToJpeg($photoBytes));
        }

        $child = @imagecreatefromstring($photoBytes);
        $garment = @imagecreatefromstring($productBytes);

        if (! $child || ! $garment) {
            return 'data:image/jpeg;base64,'.base64_encode($this->normalizeToJpeg($photoBytes));
        }

        $canvasWidth = 1280;
        $canvasHeight = 960;
        $canvas = imagecreatetruecolor($canvasWidth, $canvasHeight);
        $white = imagecolorallocate($canvas, 255, 255, 255);
        $divider = imagecolorallocate($canvas, 226, 232, 240);
        imagefill($canvas, 0, 0, $white);

        $this->copyContain($canvas, $child, 24, 24, 760, $canvasHeight - 48, $white);
        imageline($canvas, 808, 40, 808, $canvasHeight - 40, $divider);
        $this->copyContain($canvas, $garment, 832, 92, 424, $canvasHeight - 184, $white);

        $dataUri = $this->gdToJpegDataUri($canvas);

        imagedestroy($child);
        imagedestroy($garment);
        imagedestroy($canvas);

        return $dataUri;
    }

    private function normalizeToJpeg(string $bytes): string
    {
        if (! function_exists('imagecreatefromstring') || ! function_exists('imagejpeg')) {
            return $bytes;
        }

        $image = @imagecreatefromstring($bytes);
        if (! $image) {
            return $bytes;
        }

        ob_start();
        imagejpeg($image, null, 86);
        $jpeg = (string) ob_get_clean();
        imagedestroy($image);

        return $jpeg !== '' ? $jpeg : $bytes;
    }

    /**
     * @param mixed $target
     * @param mixed $source
     */
    private function copyContain($target, $source, int $x, int $y, int $width, int $height, int $fillColor): void
    {
        $sourceWidth = imagesx($source);
        $sourceHeight = imagesy($source);

        if ($sourceWidth <= 0 || $sourceHeight <= 0) {
            return;
        }

        imagefilledrectangle($target, $x, $y, $x + $width, $y + $height, $fillColor);

        $ratio = min($width / $sourceWidth, $height / $sourceHeight);
        $drawWidth = (int) floor($sourceWidth * $ratio);
        $drawHeight = (int) floor($sourceHeight * $ratio);
        $drawX = $x + (int) floor(($width - $drawWidth) / 2);
        $drawY = $y + (int) floor(($height - $drawHeight) / 2);

        imagecopyresampled($target, $source, $drawX, $drawY, 0, 0, $drawWidth, $drawHeight, $sourceWidth, $sourceHeight);
    }

    /**
     * @param mixed $image
     */
    private function gdToJpegDataUri($image): string
    {
        $working = $image;
        $quality = 86;
        $maxBytes = 900 * 1024;
        $ownsWorking = false;

        for ($attempt = 0; $attempt < 7; $attempt++) {
            ob_start();
            imagejpeg($working, null, $quality);
            $jpeg = (string) ob_get_clean();

            if (strlen($jpeg) <= $maxBytes || $attempt === 6) {
                if ($ownsWorking) {
                    imagedestroy($working);
                }

                return 'data:image/jpeg;base64,'.base64_encode($jpeg);
            }

            if ($quality > 66) {
                $quality -= 8;
                continue;
            }

            $resized = $this->resizeGd($working, 0.86);
            if ($ownsWorking) {
                imagedestroy($working);
            }
            $working = $resized;
            $ownsWorking = true;
            $quality = 76;
        }

        return 'data:image/jpeg;base64,';
    }

    /**
     * @param mixed $image
     * @return mixed
     */
    private function resizeGd($image, float $factor)
    {
        $width = max(320, (int) floor(imagesx($image) * $factor));
        $height = max(320, (int) floor(imagesy($image) * $factor));
        $resized = imagecreatetruecolor($width, $height);
        $white = imagecolorallocate($resized, 255, 255, 255);
        imagefill($resized, 0, 0, $white);
        imagecopyresampled($resized, $image, 0, 0, 0, 0, $width, $height, imagesx($image), imagesy($image));

        return $resized;
    }

    private function buildPrompt(Product $product, string $style, bool $hasGarmentReference): string
    {
        $styleInstruction = match ($style) {
            'editorial' => 'Fashion editorial finish, premium children clothing campaign, controlled studio look.',
            'casual' => 'Natural everyday look, realistic family photo lighting, comfortable fit.',
            default => 'Photorealistic e-commerce preview, natural lighting, realistic fabric and fit.',
        };

        $productNotes = Str::of(strip_tags((string) ($product->summary.' '.$product->description)))
            ->replaceMatches('/\s+/', ' ')
            ->trim()
            ->limit(700, '');

        $referenceInstruction = $hasGarmentReference
            ? 'The input image is a reference board: the left side is the child full-body photo and the right side is the clothing product reference.'
            : 'The input image is the child full-body photo. Use the product title and notes as the clothing reference.';

        return trim($referenceInstruction.' Create one single full-body image of the same child wearing the clothing from the product reference. Preserve the child face, hair, skin tone, body proportions, pose, camera angle, background and lighting as much as possible. Change only the clothes. Use the garment colors, fabric, pattern, sleeve shape, pants/skirt/shorts shape and visible details from the product reference. Do not include split panels, reference board, text, watermark, mannequin, hanger, duplicate child, extra people, product-only image or distorted limbs. Output a clean 3:4 result. '.$styleInstruction.' Product: '.$product->title.'. Product notes: '.$productNotes);
    }

    /**
     * @return array<string, mixed>
     */
    private function createPrediction(string $inputImage, string $prompt): array
    {
        $payload = [
            'input' => [
                'prompt' => $prompt,
                'input_image' => $inputImage,
                'aspect_ratio' => (string) config('services.replicate.output_aspect_ratio', '3:4'),
                'output_format' => (string) config('services.replicate.output_format', 'jpg'),
                'safety_tolerance' => (int) config('services.replicate.safety_tolerance', 2),
                'prompt_upsampling' => false,
            ],
        ];

        try {
            $response = Http::withToken((string) config('services.replicate.token'))
                ->acceptJson()
                ->asJson()
                ->withHeaders([
                    'Prefer' => 'wait='.$this->waitSeconds(),
                    'Cancel-After' => ((int) config('services.replicate.timeout', 120)).'s',
                ])
                ->timeout((int) config('services.replicate.timeout', 120))
                ->post($this->predictionEndpoint(), $payload)
                ->throw();
        } catch (RequestException $exception) {
            $message = $exception->response?->json('detail')
                ?? $exception->response?->json('error')
                ?? $exception->getMessage();

            throw new RuntimeException('Erro na API de IA: '.$message, previous: $exception);
        } catch (Throwable $exception) {
            throw new RuntimeException('Erro de conexao com a API de IA: '.$exception->getMessage(), previous: $exception);
        }

        $data = $response->json();

        if (! is_array($data)) {
            throw new RuntimeException('Resposta invalida da API de IA.');
        }

        return $data;
    }

    /**
     * @param array<string, mixed> $prediction
     * @return array<string, mixed>
     */
    private function waitForCompletion(array $prediction): array
    {
        $status = (string) ($prediction['status'] ?? '');

        if ($status === 'succeeded') {
            return $prediction;
        }

        if (in_array($status, ['failed', 'canceled'], true)) {
            throw new RuntimeException('Processamento da IA falhou: '.(string) ($prediction['error'] ?? 'erro desconhecido'));
        }

        $predictionId = (string) ($prediction['id'] ?? '');
        if ($predictionId === '') {
            throw new RuntimeException('A API de IA nao retornou o identificador do processamento.');
        }

        $startedAt = time();

        while ((time() - $startedAt) < (int) config('services.replicate.max_poll_seconds', 90)) {
            sleep((int) config('services.replicate.poll_seconds', 3));

            try {
                $response = Http::withToken((string) config('services.replicate.token'))
                    ->acceptJson()
                    ->timeout(20)
                    ->get($this->baseUrl().'/predictions/'.$predictionId)
                    ->throw();
            } catch (RequestException $exception) {
                throw new RuntimeException('Erro ao consultar resultado da IA: '.$exception->getMessage(), previous: $exception);
            } catch (Throwable $exception) {
                throw new RuntimeException('Erro de conexao ao consultar a IA: '.$exception->getMessage(), previous: $exception);
            }

            $data = $response->json();
            if (! is_array($data)) {
                continue;
            }

            $status = (string) ($data['status'] ?? '');
            if ($status === 'succeeded') {
                return $data;
            }

            if (in_array($status, ['failed', 'canceled'], true)) {
                throw new RuntimeException('Processamento da IA falhou: '.(string) ($data['error'] ?? 'erro desconhecido'));
            }
        }

        throw new RuntimeException('Tempo limite aguardando o resultado da IA.');
    }

    private function extractOutputUrl(mixed $output): ?string
    {
        if (is_string($output) && filter_var($output, FILTER_VALIDATE_URL)) {
            return $output;
        }

        if (is_array($output)) {
            foreach ($output as $item) {
                if (is_string($item) && filter_var($item, FILTER_VALIDATE_URL)) {
                    return $item;
                }

                if (is_array($item)) {
                    $url = $item['url'] ?? $item['src'] ?? null;
                    if (is_string($url) && filter_var($url, FILTER_VALIDATE_URL)) {
                        return $url;
                    }
                }
            }
        }

        return null;
    }

    private function predictionEndpoint(): string
    {
        return $this->baseUrl().'/models/'.$this->model().'/predictions';
    }

    private function baseUrl(): string
    {
        return rtrim((string) config('services.replicate.base_url', 'https://api.replicate.com/v1'), '/');
    }

    private function model(): string
    {
        return trim((string) config('services.replicate.model', 'black-forest-labs/flux-kontext-pro'));
    }

    private function waitSeconds(): int
    {
        return min(60, max(1, (int) config('services.replicate.wait_seconds', 60)));
    }

    private function maxInputMb(): int
    {
        return max(1, (int) config('services.replicate.max_input_mb', 8));
    }
}
