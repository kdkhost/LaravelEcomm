<?php

declare(strict_types=1);

namespace Modules\Front\Http\Controllers;

use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\Product\Models\Product;

class VirtualTryOnController extends Controller
{
    public function index(string $locale, ?string $slug = null): View
    {
        $product = $slug
            ? Product::where('slug', $slug)->first()
            : null;

        return view(theme_view('pages.virtual-try-on'), [
            'product' => $product,
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
            'M', 'G' => 'Caimento equilibrado para uso diário.',
            default => 'Caimento confortável com mais folga nas medidas principais.',
        };
    }
}
