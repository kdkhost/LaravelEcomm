<?php

declare(strict_types=1);

namespace Modules\Shipping\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\Core\Helpers\Helper;
use Modules\Product\Models\Product;
use Modules\Shipping\Services\CorreiosShippingService;
use Throwable;

class PublicShippingQuoteController extends Controller
{
    public function __invoke(Request $request, CorreiosShippingService $shippingService): JsonResponse
    {
        $validated = $request->validate([
            'context' => 'nullable|in:product,cart',
            'product_slug' => 'nullable|string|max:255',
            'quantity' => 'nullable|integer|min:1|max:999',
            'postal_code' => 'required|string|min:8|max:10',
        ]);

        try {
            if (($validated['context'] ?? 'cart') === 'product') {
                $product = Product::with('attributeValues.attribute')
                    ->where('slug', $validated['product_slug'] ?? '')
                    ->firstOrFail();

                $result = $shippingService->quoteProduct(
                    $product,
                    $validated['postal_code'],
                    (int) ($validated['quantity'] ?? 1)
                );
            } else {
                $cartItems = Helper::getAllProductFromCart();

                $result = $shippingService->quoteCart(
                    $cartItems,
                    $validated['postal_code'],
                    (float) Helper::totalCartPrice()
                );
            }

            return response()->json([
                'success' => ! empty($result['methods']),
                'message' => ! empty($result['methods'])
                    ? 'Frete calculado com sucesso.'
                    : 'Nenhuma opcao de frete foi encontrada para o CEP informado.',
                'source' => $result['source'] ?? 'shipping_rules',
                'methods' => $result['methods'] ?? [],
            ]);
        } catch (Throwable $exception) {
            return response()->json([
                'success' => false,
                'message' => $exception->getMessage() ?: 'Nao foi possivel calcular o frete agora.',
                'methods' => [],
            ], 422);
        }
    }
}
