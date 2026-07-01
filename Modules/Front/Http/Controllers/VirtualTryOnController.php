<?php

declare(strict_types=1);

namespace Modules\Front\Http\Controllers;

use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Collection;
use Modules\Product\Models\Product;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class VirtualTryOnController extends Controller
{
    public function index(string $locale, ?string $slug = null): View
    {
        $product = $slug
            ? Product::with('media')->where('slug', $slug)->where('status', 'active')->first()
            : null;

        return view(theme_view('pages.virtual-try-on'), [
            'product' => $product,
            'productImages' => $product ? $this->productImages($product) : collect(),
            'products' => Product::with('media')
                ->where('status', 'active')
                ->orderBy('title')
                ->limit(48)
                ->get(['id', 'title', 'slug']),
            'sizes' => $this->sizeTable(),
        ]);
    }

    public function recommend(Request $request): JsonResponse
    {
        $data = $request->validate([
            'height' => ['required', 'numeric', 'min:80', 'max:230'],
            'chest' => ['required', 'numeric', 'min:40', 'max:180'],
            'waist' => ['required', 'numeric', 'min:35', 'max:180'],
            'hip' => ['required', 'numeric', 'min:40', 'max:190'],
            'shoulder' => ['nullable', 'numeric', 'min:20', 'max:80'],
            'product_slug' => ['nullable', 'string', 'max:255'],
        ]);

        $size = $this->recommendSize(
            (float) $data['chest'],
            (float) $data['waist'],
            (float) $data['hip']
        );

        return response()->json([
            'size' => $size,
            'message' => "Tamanho recomendado: {$size}",
            'fit' => $this->fitMessage($size),
            'body_map' => $this->bodyMap(
                (float) $data['height'],
                (float) $data['chest'],
                (float) $data['waist'],
                (float) $data['hip'],
                isset($data['shoulder']) ? (float) $data['shoulder'] : null
            ),
            'privacy' => 'A foto enviada no provador padrao e processada no navegador e nao fica salva no servidor.',
        ]);
    }

    /**
     * @return array<string, array{chest: string, waist: string, hip: string}>
     */
    private function sizeTable(): array
    {
        return [
            'PP' => ['chest' => '78-84', 'waist' => '60-66', 'hip' => '84-90'],
            'P' => ['chest' => '85-92', 'waist' => '67-74', 'hip' => '91-98'],
            'M' => ['chest' => '93-100', 'waist' => '75-82', 'hip' => '99-106'],
            'G' => ['chest' => '101-108', 'waist' => '83-90', 'hip' => '107-114'],
            'GG' => ['chest' => '109-118', 'waist' => '91-102', 'hip' => '115-124'],
            'XG' => ['chest' => '119-130', 'waist' => '103-116', 'hip' => '125-136'],
        ];
    }

    /**
     * @return Collection<int, array{url: string, thumb: string, name: string}>
     */
    private function productImages(Product $product): Collection
    {
        $mediaItems = $product->getMedia('product');

        if ($mediaItems->isEmpty()) {
            return collect([[
                'url' => (string) $product->imageUrl,
                'thumb' => (string) $product->imageThumbUrl,
                'name' => $product->title,
            ]]);
        }

        return $mediaItems->map(function (Media $media) use ($product): array {
            return [
                'url' => $media->getUrl(),
                'thumb' => $media->hasGeneratedConversion('thumb') ? $media->getUrl('thumb') : $media->getUrl(),
                'name' => $media->name ?: $product->title,
            ];
        })->values();
    }

    private function recommendSize(float $chest, float $waist, float $hip): string
    {
        $score = max($chest, $waist + 18, $hip - 6);

        return match (true) {
            $score <= 84 => 'PP',
            $score <= 92 => 'P',
            $score <= 100 => 'M',
            $score <= 108 => 'G',
            $score <= 118 => 'GG',
            default => 'XG',
        };
    }

    private function fitMessage(string $size): string
    {
        return match ($size) {
            'PP', 'P' => 'Caimento mais ajustado. Se preferir conforto, considere um tamanho acima.',
            'M', 'G' => 'Caimento equilibrado para uso diario.',
            default => 'Caimento confortavel com mais folga nas medidas principais.',
        };
    }

    /**
     * @return array<string, float|array<int, int>>
     */
    private function bodyMap(float $height, float $chest, float $waist, float $hip, ?float $shoulder): array
    {
        $shoulderRatio = $shoulder ? $shoulder / max($height, 1) : $chest / max($height * 1.65, 1);
        $chestRatio = $chest / max($height, 1);
        $waistRatio = $waist / max($height, 1);
        $hipRatio = $hip / max($height, 1);

        return [
            'torso_top' => 0.245,
            'torso_height' => 0.355,
            'shoulder_width' => round(min(0.62, max(0.34, $shoulderRatio * 1.82)), 3),
            'chest_width' => round(min(0.58, max(0.32, $chestRatio * 0.72)), 3),
            'waist_width' => round(min(0.50, max(0.26, $waistRatio * 0.68)), 3),
            'hip_width' => round(min(0.62, max(0.34, $hipRatio * 0.72)), 3),
            'garment_scale' => round(min(1.2, max(0.82, ($chest + $waist + $hip) / 270)), 3),
            'angles' => [0, 45, 90, 135, 180, 225, 270, 315],
        ];
    }
}
