<?php

declare(strict_types=1);

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use Modules\Product\Models\Product;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Throwable;

class ImportRataplamCatalogCommand extends Command
{
    protected $signature = 'rataplam:import-catalog
                            {--dry-run : Simula a importacao sem alterar produtos}
                            {--skip-images : Nao tenta importar imagens}
                            {--skip-descriptions : Nao atualiza resumo e descricao}
                            {--offset=0 : Posicao inicial do lote de produtos}
                            {--limit=0 : Quantidade maxima de produtos no lote; 0 processa todos}';

    protected $description = 'Importa imagens e descricoes dos produtos da loja antiga Rataplam.';

    private const OLD_SITEMAP = 'https://www.rataplam.com.br/store-products-sitemap.xml';

    /**
     * Ajustes manuais para produtos cuja URL antiga usa slug diferente do cadastro novo.
     *
     * @var array<string, string>
     */
    private const SLUG_ALIASES = [
        'macacao-cogumelo' => 'mcacão-cogumelo',
        'bermuda-positive' => 'bermuda-positiv-1',
        'bermuda-posi' => 'bermuda-positiv',
        'biquini-uv-primavera' => 'biquini-uv-primavera-1',
        'biquini-uv-borboletas' => 'biquini-uv-borboletas-ii',
    ];

    public function handle(): int
    {
        date_default_timezone_set(config('app.timezone', 'America/Sao_Paulo'));

        $dryRun = (bool) $this->option('dry-run');
        $updateImages = ! (bool) $this->option('skip-images');
        $updateDescriptions = ! (bool) $this->option('skip-descriptions');

        $this->info('Lendo sitemap antigo da Rataplam...');
        $oldProducts = $this->loadOldProducts();

        if ($oldProducts === []) {
            $this->error('Nenhum produto antigo foi encontrado.');

            return Command::FAILURE;
        }

        $offset = max(0, (int) $this->option('offset'));
        $limit = max(0, (int) $this->option('limit'));
        $totalProducts = Product::count();

        $productQuery = Product::with(['categories', 'media'])->orderBy('slug');

        if ($offset > 0) {
            $productQuery->skip($offset);
        }

        if ($limit > 0) {
            $productQuery->take($limit);
        }

        $products = $productQuery->get();

        $this->info('Produtos novos encontrados no lote: '.$products->count().' de '.$totalProducts);

        if (! $dryRun && $updateDescriptions) {
            $this->writeBackup($products);
        }

        $stats = [
            'matched' => 0,
            'description_updated' => 0,
            'images_added' => 0,
            'image_errors' => 0,
            'image_skipped' => 0,
            'unmatched' => [],
            'blocked_images' => [],
        ];

        foreach ($products as $product) {
            $oldProduct = $this->findOldProduct($product, $oldProducts);

            if (! $oldProduct) {
                $stats['unmatched'][] = $product->slug;
                $this->warn('Sem correspondencia antiga: '.$product->slug);
                continue;
            }

            $stats['matched']++;
            $oldProduct = $this->hydrateProductPage($oldProduct);

            if ($updateDescriptions) {
                $content = $this->buildProductContent($product, $oldProduct);

                if (! $dryRun) {
                    $product->forceFill([
                        'summary' => $content['summary'],
                        'description' => $content['description'],
                    ])->save();
                }

                $stats['description_updated']++;
            }

            if ($updateImages) {
                $imageResult = $this->syncProductImages($product, $oldProduct, $dryRun);
                $stats['images_added'] += $imageResult['added'];
                $stats['image_errors'] += count($imageResult['errors']);
                $stats['image_skipped'] += $imageResult['skipped'];

                foreach ($imageResult['errors'] as $error) {
                    $stats['blocked_images'][] = $error;
                }
            }
        }

        if (! $dryRun) {
            $this->callSilent('optimize:clear');
        }

        $this->newLine();
        $this->info('Produtos mapeados: '.$stats['matched'].'/'.$products->count());
        $this->info('Descricoes atualizadas: '.$stats['description_updated']);
        $this->info('Imagens adicionadas: '.$stats['images_added']);
        $this->info('Imagens ja existentes: '.$stats['image_skipped']);
        $this->info('Falhas de imagem: '.$stats['image_errors']);

        if ($stats['unmatched'] !== []) {
            $this->warn('Produtos sem correspondencia: '.implode(', ', $stats['unmatched']));
        }

        if ($stats['blocked_images'] !== []) {
            $this->warn('Imagens indisponiveis na origem antiga:');
            foreach ($stats['blocked_images'] as $error) {
                $this->line('- '.$error);
            }
        }

        return Command::SUCCESS;
    }

    /**
     * @return array<string, array<string, mixed>>
     */
    private function loadOldProducts(): array
    {
        $response = Http::timeout(60)
            ->withHeaders($this->browserHeaders())
            ->get(self::OLD_SITEMAP);

        if (! $response->successful()) {
            return [];
        }

        $xml = @simplexml_load_string($response->body());
        if (! $xml) {
            return [];
        }

        $xml->registerXPathNamespace('image', 'http://www.google.com/schemas/sitemap-image/1.1');

        $products = [];
        foreach ($xml->url as $url) {
            $loc = (string) $url->loc;
            if ($loc === '' || ! str_contains($loc, '/pagina-de-produto/')) {
                continue;
            }

            $slug = urldecode((string) Str::of(parse_url($loc, PHP_URL_PATH) ?: '')->afterLast('/'));
            $images = [];

            foreach ($url->children('http://www.google.com/schemas/sitemap-image/1.1')->image as $image) {
                $imageUrl = (string) $image->loc;
                if ($imageUrl !== '') {
                    $images[] = $imageUrl;
                }
            }

            $products[$this->normalizeSlug($slug)] = [
                'slug' => $slug,
                'loc' => $loc,
                'images' => array_values(array_unique($images)),
                'page_loaded' => false,
                'old_name' => null,
                'old_description' => null,
            ];
        }

        return $products;
    }

    /**
     * @param array<string, array<string, mixed>> $oldProducts
     *
     * @return array<string, mixed>|null
     */
    private function findOldProduct(Product $product, array $oldProducts): ?array
    {
        $normalized = $this->normalizeSlug($product->slug);
        if (isset($oldProducts[$normalized])) {
            return $oldProducts[$normalized];
        }

        $alias = self::SLUG_ALIASES[$product->slug] ?? null;
        if ($alias && isset($oldProducts[$this->normalizeSlug($alias)])) {
            return $oldProducts[$this->normalizeSlug($alias)];
        }

        return null;
    }

    /**
     * @param array<string, mixed> $oldProduct
     *
     * @return array<string, mixed>
     */
    private function hydrateProductPage(array $oldProduct): array
    {
        if (($oldProduct['page_loaded'] ?? false) === true) {
            return $oldProduct;
        }

        try {
            $response = Http::timeout(60)
                ->withHeaders($this->browserHeaders())
                ->get((string) $oldProduct['loc']);

            if (! $response->successful()) {
                return $oldProduct;
            }

            $structuredData = $this->extractProductStructuredData($response->body());
            if ($structuredData !== []) {
                $oldProduct['old_name'] = $structuredData['name'] ?? null;
                $oldProduct['old_description'] = $structuredData['description'] ?? null;
                $oldProduct['images'] = $this->mergeImages(
                    $this->extractStructuredImages($structuredData),
                    $oldProduct['images'] ?? []
                );
            }

            $oldProduct['page_loaded'] = true;
        } catch (Throwable) {
            return $oldProduct;
        }

        return $oldProduct;
    }

    /**
     * @return array<string, mixed>
     */
    private function extractProductStructuredData(string $html): array
    {
        if (! preg_match_all('/<script[^>]+type=["\']application\/ld\+json["\'][^>]*>(.*?)<\/script>/is', $html, $matches)) {
            return [];
        }

        foreach ($matches[1] as $json) {
            $decoded = json_decode(html_entity_decode(trim($json), ENT_QUOTES | ENT_HTML5, 'UTF-8'), true);
            if (! is_array($decoded)) {
                continue;
            }

            if (($decoded['@type'] ?? null) === 'Product') {
                return $decoded;
            }

            foreach ($decoded['@graph'] ?? [] as $item) {
                if (is_array($item) && ($item['@type'] ?? null) === 'Product') {
                    return $item;
                }
            }
        }

        return [];
    }

    /**
     * @param array<string, mixed> $structuredData
     *
     * @return array<int, string>
     */
    private function extractStructuredImages(array $structuredData): array
    {
        $images = [];
        $source = $structuredData['image'] ?? [];
        $source = is_array($source) ? $source : [$source];

        foreach ($source as $image) {
            if (is_string($image) && $image !== '') {
                $images[] = $image;
                continue;
            }

            if (! is_array($image)) {
                continue;
            }

            $contentUrl = $image['contentUrl'] ?? null;
            if (is_string($contentUrl) && $contentUrl !== '') {
                $images[] = $contentUrl;
            }
        }

        return array_values(array_unique($images));
    }

    /**
     * @param array<int, string> $primary
     * @param array<int, string> $fallback
     *
     * @return array<int, string>
     */
    private function mergeImages(array $primary, array $fallback): array
    {
        return $this->uniqueImagesByIdentity([...$primary, ...$fallback]);
    }

    /**
     * @param array<string, mixed> $oldProduct
     *
     * @return array{summary: string, description: string}
     */
    private function buildProductContent(Product $product, array $oldProduct): array
    {
        $title = trim((string) $product->title);
        $categories = $product->categories->pluck('title')->filter()->values()->all();
        $categoryText = $categories !== [] ? implode(', ', $categories) : 'Infantil';
        $oldDescription = $this->cleanText((string) ($oldProduct['old_description'] ?? ''));
        $pieceType = $this->pieceType($title, $categoryText);
        $audience = $this->audience($categoryText);
        $use = $this->recommendedUse($title, $categoryText, $oldDescription);
        $composition = $this->composition($oldDescription);
        $feature = $this->mainFeature($title, $categoryText, $oldDescription);

        $detail = $oldDescription !== ''
            ? $oldDescription
            : 'Peça infantil Rataplam selecionada para vestir com conforto, bom acabamento e estilo no dia a dia.';

        $summary = $this->limitText(sprintf(
            '%s: %s Rataplam para %s, com %s. Indicada para %s.',
            $title,
            $pieceType,
            $audience,
            Str::lower($feature),
            $use
        ), 210);

        $items = [
            '<li><strong>Tipo de peça:</strong> '.$this->escape($pieceType).'</li>',
            '<li><strong>Público indicado:</strong> '.$this->escape($audience).'</li>',
            '<li><strong>Categoria:</strong> '.$this->escape($categoryText).'</li>',
            '<li><strong>Detalhes da peça:</strong> '.$this->escape($detail).'</li>',
        ];

        if ($composition !== null) {
            $items[] = '<li><strong>Composição informada:</strong> '.$this->escape($composition).'</li>';
        }

        $items[] = '<li><strong>Indicação de uso:</strong> '.$this->escape($use).'</li>';
        $items[] = '<li><strong>Acabamento:</strong> modelagem confortável para facilitar o movimento da criança.</li>';

        $description = '<h4>Sobre a peça</h4>'
            .'<p><strong>'.$this->escape($title).'</strong> é '.$this->escape($pieceType).' Rataplam para '.$this->escape($audience)
            .', pensada para unir conforto, estilo e praticidade no uso infantil.</p>'
            .'<h4>Detalhes corretos do produto</h4>'
            .'<ul>'.implode('', $items).'</ul>'
            .'<p>As cores podem variar levemente conforme a tela do dispositivo. Consulte a disponibilidade de tamanho antes de finalizar o pedido.</p>';

        return [
            'summary' => $summary,
            'description' => $description,
        ];
    }

    /**
     * @param array<string, mixed> $oldProduct
     *
     * @return array{added: int, skipped: int, errors: array<int, string>}
     */
    private function syncProductImages(Product $product, array $oldProduct, bool $dryRun): array
    {
        $result = [
            'added' => 0,
            'skipped' => 0,
            'errors' => [],
        ];

        $imageUrls = $this->uniqueImagesByIdentity($oldProduct['images'] ?? []);
        $existingFiles = $product->getMedia('product')
            ->map(fn (Media $media): string => $media->file_name)
            ->all();

        foreach ($imageUrls as $index => $imageUrl) {
            $fileName = $this->imageFileName($product->slug, $index + 1, (string) $imageUrl);

            if (in_array($fileName, $existingFiles, true)) {
                $result['skipped']++;
                continue;
            }

            if ($dryRun) {
                $result['added']++;
                continue;
            }

            try {
                $product->addMediaFromUrl((string) $imageUrl)
                    ->usingFileName($fileName)
                    ->toMediaCollection('product');

                $result['added']++;
                $existingFiles[] = $fileName;
            } catch (Throwable $exception) {
                $result['errors'][] = $product->slug.' -> '.$fileName.' -> '.$exception->getMessage();
            }
        }

        return $result;
    }

    private function imageFileName(string $slug, int $position, string $url): string
    {
        $extension = pathinfo(parse_url($url, PHP_URL_PATH) ?: '', PATHINFO_EXTENSION);
        $extension = strtolower($extension ?: 'jpg');

        if (! in_array($extension, ['jpg', 'jpeg', 'png', 'webp', 'gif'], true)) {
            $extension = 'jpg';
        }

        return Str::slug($slug).'-'.$position.'.'.$extension;
    }

    private function imageIdentity(string $url): string
    {
        if (preg_match('/\/media\/([^\/?]+)/', $url, $matches)) {
            return urldecode($matches[1]);
        }

        return $url;
    }

    /**
     * @param array<int, mixed> $imageUrls
     *
     * @return array<int, string>
     */
    private function uniqueImagesByIdentity(array $imageUrls): array
    {
        $images = [];

        foreach (array_filter($imageUrls) as $imageUrl) {
            $imageUrl = (string) $imageUrl;
            $key = $this->imageIdentity($imageUrl);
            if (! isset($images[$key])) {
                $images[$key] = $imageUrl;
            }
        }

        return array_values($images);
    }

    private function pieceType(string $title, string $categories): string
    {
        $text = $this->normalizeText($title.' '.$categories);

        return match (true) {
            str_contains($text, 'bermuda') => 'bermuda infantil',
            str_contains($text, 'biquini') => 'biquíni infantil',
            str_contains($text, 'blusa') => str_contains($text, 'uv') ? 'blusa infantil com proteção UV' : 'blusa infantil',
            str_contains($text, 'body') => 'body de bebê',
            str_contains($text, 'calca') => 'calça infantil',
            str_contains($text, 'colete') => 'colete infantil',
            str_contains($text, 'conjunto') => 'conjunto infantil',
            str_contains($text, 'macacao') => 'macacão de bebê',
            str_contains($text, 'sapatinho') => 'sapatinho de bebê',
            str_contains($text, 'toalha') => 'toalha infantil',
            str_contains($text, 'vestido') => 'vestido infantil',
            default => 'peça infantil',
        };
    }

    private function audience(string $categories): string
    {
        $text = $this->normalizeText($categories);

        return match (true) {
            str_contains($text, 'bebe') && str_contains($text, 'meninas') => 'bebês e meninas',
            str_contains($text, 'bebe') && str_contains($text, 'meninos') => 'bebês e meninos',
            str_contains($text, 'bebe') => 'bebês',
            str_contains($text, 'meninas') && str_contains($text, 'meninos') => 'meninas e meninos',
            str_contains($text, 'meninas') => 'meninas',
            str_contains($text, 'meninos') => 'meninos',
            default => 'crianças',
        };
    }

    private function recommendedUse(string $title, string $categories, string $description): string
    {
        $text = $this->normalizeText($title.' '.$categories.' '.$description);

        return match (true) {
            str_contains($text, 'uv') || str_contains($text, 'praia') || str_contains($text, 'piscina') => 'praia, piscina e atividades ao ar livre',
            str_contains($text, 'trico') || str_contains($text, 'batizado') => 'enxoval, momentos especiais e composições delicadas',
            str_contains($text, 'bebe') || str_contains($text, 'macacao') || str_contains($text, 'body') => 'rotina do bebê, passeios e momentos de descanso',
            str_contains($text, 'moletom') || str_contains($text, 'felpado') => 'dias mais frescos, rotina escolar e passeios',
            default => 'rotina, passeios e momentos de lazer',
        };
    }

    private function mainFeature(string $title, string $categories, string $description): string
    {
        $text = $this->normalizeText($title.' '.$categories.' '.$description);

        return match (true) {
            str_contains($text, 'uv') => 'proteção UV e modelagem confortável',
            str_contains($text, 'moletom') || str_contains($text, 'felpado') => 'toque macio e conforto térmico',
            str_contains($text, 'trico') => 'trabalho em tricô e acabamento delicado',
            str_contains($text, 'algodao') => 'toque de algodão e conforto no vestir',
            str_contains($text, 'banho') || str_contains($text, 'batizado') => 'acabamento delicado para ocasião especial',
            str_contains($text, 'biquini') => 'conjunto leve para brincar com liberdade',
            str_contains($text, 'bermuda') => 'modelagem prática para movimento',
            default => 'acabamento confortável e visual infantil',
        };
    }

    private function composition(string $description): ?string
    {
        if ($description === '') {
            return null;
        }

        if (preg_match('/(?:tecido|composição|composicao)\s*:?\s*([^.;]+(?:algodão|algodao|poli[eé]ster|elastano|viscose|linho|tricô|trico)[^.;]*)/iu', $description, $matches)) {
            return $this->cleanText($matches[1]);
        }

        if (preg_match('/\d{1,3}\s*%\s*[^.;]+/u', $description, $matches)) {
            return $this->cleanText($matches[0]);
        }

        return null;
    }

    private function cleanText(string $text): string
    {
        $text = html_entity_decode(strip_tags($text), ENT_QUOTES | ENT_HTML5, 'UTF-8');
        $text = preg_replace('/\s+/u', ' ', $text) ?? $text;
        $text = preg_replace('/\s+([,.!?;:])/u', '$1', $text) ?? $text;
        $text = preg_replace('/([.!?])(?=\p{L})/u', '$1 ', $text) ?? $text;
        $text = str_replace([' :', ' .'], [':', '.'], $text);

        return trim($text);
    }

    private function limitText(string $text, int $limit): string
    {
        $text = $this->cleanText($text);

        return Str::limit($text, $limit, '.');
    }

    private function escape(string $value): string
    {
        return e($this->cleanText($value));
    }

    private function normalizeSlug(string $value): string
    {
        return Str::slug(Str::ascii($value));
    }

    private function normalizeText(string $value): string
    {
        return Str::lower(Str::ascii($value));
    }

    /**
     * @return array<string, string>
     */
    private function browserHeaders(): array
    {
        return [
            'Accept' => 'text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8',
            'Accept-Language' => 'pt-BR,pt;q=0.9,en-US;q=0.8,en;q=0.7',
            'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36',
        ];
    }

    private function writeBackup($products): void
    {
        $directory = storage_path('app/codex-backups');
        if (! is_dir($directory)) {
            mkdir($directory, 0755, true);
        }

        $payload = $products->map(fn (Product $product): array => [
            'id' => $product->id,
            'slug' => $product->slug,
            'summary' => $product->summary,
            'description' => $product->description,
            'media_count' => $product->getMedia('product')->count(),
            'media_names' => $product->getMedia('product')->pluck('file_name')->values()->all(),
        ])->values()->all();

        file_put_contents(
            $directory.'/rataplam-catalog-update-'.now()->format('Ymd-His').'.json',
            json_encode($payload, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES)
        );
    }
}
