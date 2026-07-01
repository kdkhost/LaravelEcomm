@extends($themePath . '.layouts.master')

@section('title', 'Meus favoritos - ' . config('app.name'))

@section('content')
<div class="container mx-auto px-4 py-8">
    <!-- Header -->
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900 mb-4">Meus favoritos</h1>
        <p class="text-gray-600">Salve produtos favoritos e acompanhe quedas de preço e ofertas especiais.</p>
    </div>

    <!-- Estatísticas dos favoritos -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
        <div class="bg-white rounded-lg shadow-md p-6 text-center">
            <i class="fas fa-heart text-red-500 text-3xl mb-3"></i>
            <h3 class="text-lg font-semibold text-gray-900">{{ $statistics['total_items'] }}</h3>
            <p class="text-gray-600 text-sm">Total de itens</p>
        </div>

        <div class="bg-white rounded-lg shadow-md p-6 text-center">
            <i class="fas fa-money-bill-wave text-green-500 text-3xl mb-3"></i>
            <h3 class="text-lg font-semibold text-gray-900">{{ format_currency((float) ($statistics['total_value'])) }}</h3>
            <p class="text-gray-600 text-sm">Valor total</p>
        </div>

        <div class="bg-white rounded-lg shadow-md p-6 text-center">
            <i class="fas fa-tags text-blue-500 text-3xl mb-3"></i>
            <h3 class="text-lg font-semibold text-gray-900">{{ $statistics['categories'] }}</h3>
            <p class="text-gray-600 text-sm">Categorias</p>
        </div>

        <div class="bg-white rounded-lg shadow-md p-6 text-center">
            <i class="fas fa-crown text-purple-500 text-3xl mb-3"></i>
            <h3 class="text-lg font-semibold text-gray-900">{{ $statistics['brands'] }}</h3>
            <p class="text-gray-600 text-sm">Marcas</p>
        </div>
    </div>

    <!-- Alertas de preço Toggle -->
    <div class="bg-white rounded-lg shadow-md p-6 mb-8">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-xl font-semibold text-gray-900 mb-2">Alertas de preço</h2>
                <p class="text-gray-600">Acompanhe mudanças de preço e receba avisos sobre as melhores ofertas</p>
            </div>

            <div class="flex items-center space-x-4">
                <a href="{{ route('front.enhanced-wishlist', ['with_price_alerts' => true]) }}"
                   class="px-4 py-2 rounded-lg {{ $withPriceAlerts ? 'bg-blue-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }} transition-colors">
                    <i class="fas fa-bell mr-2"></i>
                    With Alertas de preço
                </a>

                <a href="{{ route('front.enhanced-wishlist') }}"
                   class="px-4 py-2 rounded-lg {{ !$withPriceAlerts ? 'bg-blue-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }} transition-colors">
                    <i class="fas fa-list mr-2"></i>
                    Visualização padrão
                </a>
            </div>
        </div>
    </div>

    <!-- Itens favoritos -->
    @if($wishlist->count() > 0)
        <div class="bg-white rounded-lg shadow-md p-6 mb-8">
            <div class="flex justify-between items-center mb-6">
                <h2 class="text-xl font-semibold text-gray-900">Itens favoritos</h2>

                <div class="flex space-x-2">
                    <button onclick="bulkAddToCart()"
                            class="bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700 transition-colors">
                        <i class="fas fa-shopping-cart mr-2"></i>
                        Adicionar tudo ao carrinho
                    </button>

                    <button onclick="bulkRemove()"
                            class="bg-red-600 text-white px-4 py-2 rounded-lg hover:bg-red-700 transition-colors">
                        <i class="fas fa-trash mr-2"></i>
                        Remover tudo
                    </button>
                </div>
            </div>

            <!-- Favoritos Grid -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach($wishlist as $item)
                    <div class="bg-white border border-gray-200 rounded-lg overflow-hidden hover:shadow-lg transition-shadow">
                        <div class="relative">
                            @if($item->product->getFirstMediaUrl('images'))
                                <img src="{{ $item->product->getFirstMediaUrl('images') }}"
                                     alt="{{ $item->product->title }}"
                                     class="w-full h-48 object-cover">
                            @else
                                <div class="w-full h-48 bg-gray-300 flex items-center justify-center">
                                    <i class="fas fa-image text-gray-400 text-4xl"></i>
                                </div>
                            @endif

                            <!-- Preço Alert Badge -->
                            @if($withPriceAlerts && $item->price_drop)
                                <div class="absolute top-2 left-2">
                                    <span class="bg-red-500 text-white text-xs px-2 py-1 rounded-full">
                                        <i class="fas fa-arrow-down mr-1"></i>
                                        {{ $item->price_drop_percentage }}% OFF
                                    </span>
                                </div>
                            @endif

                            <!-- Quantity Badge -->
                            @if($item->quantity > 1)
                                <div class="absolute top-2 right-2">
                                    <span class="bg-blue-500 text-white text-xs px-2 py-1 rounded-full">
                                        Qty: {{ $item->quantity }}
                                    </span>
                                </div>
                            @endif
                        </div>

                        <div class="p-4">
                            <h3 class="text-lg font-semibold text-gray-900 mb-2 line-clamp-2">
                                <a href="{{ route('front.product-detail', $item->product->slug) }}"
                                   class="hover:text-blue-600 transition-colors">
                                    {{ $item->product->title }}
                                </a>
                            </h3>

                            <!-- Informações de preço -->
                            <div class="mb-3">
                                @if($withPriceAlerts && $item->price_drop)
                                    <div class="flex items-center space-x-2 mb-2">
                                        <span class="text-lg font-bold text-red-600">
                                            {{ format_currency((float) ($item->product->special_price ?? $item->product->price)) }}
                                        </span>
                                        <span class="text-sm text-gray-500 line-through">
                                            {{ format_currency((float) ($item->price)) }}
                                        </span>
                                        <span class="text-sm text-red-600 font-medium">
                                            Economize {{ format_currency((float) ($item->price_difference)) }}
                                        </span>
                                    </div>
                                @else
                                    <div class="flex items-center mb-2">
                                        @if($item->product->special_price)
                                            <span class="text-lg font-bold text-red-600">{{ format_currency((float) ($item->product->special_price)) }}</span>
                                            <span class="text-sm text-gray-500 line-through ml-2">{{ format_currency((float) ($item->product->price)) }}</span>
                                        @else
                                            <span class="text-lg font-bold text-gray-900">{{ format_currency((float) ($item->product->price)) }}</span>
                                        @endif
                                    </div>
                                @endif

                                <div class="text-sm text-gray-600">
                                    Estoque: {{ $item->product->stock > 0 ? 'Em estoque' : 'Sem estoque' }}
                                </div>
                            </div>

                            <!-- Ação Buttons -->
                            <div class="flex items-center justify-between">
                                <div class="flex space-x-2">
                                    <button onclick="moveToCart({{ $item->product->id }})"
                                            class="bg-blue-600 text-white px-3 py-2 rounded-md hover:bg-blue-700 transition-colors text-sm">
                                        <i class="fas fa-shopping-cart mr-1"></i>
                                        Mover para o carrinho
                                    </button>

                                    <button onclick="updateQuantity({{ $item->product->id }})"
                                            class="bg-gray-100 text-gray-700 px-3 py-2 rounded-md hover:bg-gray-200 transition-colors text-sm">
                                        <i class="fas fa-edit mr-1"></i>
                                        Editar
                                    </button>
                                </div>

                                <button onclick="removeFromWishlist({{ $item->product->id }})"
                                        class="text-red-500 hover:text-red-700 transition-colors">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @else
        <!-- Empty Favoritos -->
        <div class="bg-white rounded-lg shadow-md p-12 text-center">
            <i class="far fa-heart text-gray-400 text-6xl mb-4"></i>
            <h3 class="text-xl font-semibold text-gray-900 mb-2">Sua lista de favoritos está vazia</h3>
            <p class="text-gray-600 mb-6">Adicione produtos favoritos para acompanhar preços e receber avisos de ofertas.</p>

            <div class="flex justify-center space-x-4">
                <a href="{{ route('front.product-grids') }}"
                   class="bg-blue-600 text-white px-6 py-3 rounded-lg hover:bg-blue-700 transition-colors">
                    Ver produtos
                </a>

                <a href="{{ route('front.recommendations') }}"
                   class="bg-gray-100 text-gray-700 px-6 py-3 rounded-lg hover:bg-gray-200 transition-colors">
                    Ver recomendações
                </a>
            </div>
        </div>
    @endif

    <!-- Recomendações para favoritos -->
    @if($recommendations->count() > 0)
        <div class="bg-white rounded-lg shadow-md p-6 mb-8">
            <h2 class="text-xl font-semibold text-gray-900 mb-6">Você também pode gostar</h2>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach($recommendations as $product)
                    <div class="bg-white border border-gray-200 rounded-lg overflow-hidden hover:shadow-lg transition-shadow">
                        <div class="aspect-w-1 aspect-h-1 bg-gray-200">
                            @if($product->getFirstMediaUrl('images'))
                                <img src="{{ $product->getFirstMediaUrl('images') }}"
                                     alt="{{ $product->title }}"
                                     class="w-full h-48 object-cover">
                            @else
                                <div class="w-full h-48 bg-gray-300 flex items-center justify-center">
                                    <i class="fas fa-image text-gray-400 text-4xl"></i>
                                </div>
                            @endif
                        </div>

                        <div class="p-4">
                            <h3 class="text-lg font-semibold text-gray-900 mb-2 line-clamp-2">
                                <a href="{{ route('front.product-detail', $product->slug) }}"
                                   class="hover:text-blue-600 transition-colors">
                                    {{ $product->title }}
                                </a>
                            </h3>

                            <div class="flex items-center mb-2">
                                @if($product->special_price)
                                    <span class="text-lg font-bold text-red-600">{{ format_currency((float) ($product->special_price)) }}</span>
                                    <span class="text-sm text-gray-500 line-through ml-2">{{ format_currency((float) ($product->price)) }}</span>
                                @else
                                    <span class="text-lg font-bold text-gray-900">{{ format_currency((float) ($product->price)) }}</span>
                                @endif
                            </div>

                            <div class="flex items-center justify-between">
                                <span class="text-sm text-gray-600">
                                    Estoque: {{ $product->stock > 0 ? 'Em estoque' : 'Sem estoque' }}
                                </span>

                                <button onclick="addToWishlist({{ $product->id }})"
                                        class="text-gray-400 hover:text-red-500 transition-colors">
                                    <i class="far fa-heart"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    <!-- Ações dos favoritos -->
    <div class="bg-white rounded-lg shadow-md p-6">
        <h2 class="text-xl font-semibold text-gray-900 mb-4">Ações dos favoritos</h2>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <!-- Compartilhar favoritos -->
            <div class="text-center">
                <i class="fas fa-share-alt text-blue-600 text-3xl mb-3"></i>
                <h3 class="text-lg font-semibold text-gray-900 mb-2">Compartilhar favoritos</h3>
                <p class="text-gray-600 mb-4">Compartilhe sua lista de favoritos</p>
                <button onclick="shareWishlist()"
                        class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition-colors">
                    Compartilhar agora
                </button>
            </div>

            <!-- Exportar favoritos -->
            <div class="text-center">
                <i class="fas fa-download text-green-600 text-3xl mb-3"></i>
                <h3 class="text-lg font-semibold text-gray-900 mb-2">Exportar favoritos</h3>
                <p class="text-gray-600 mb-4">Baixe seus favoritos em PDF ou CSV</p>
                <button onclick="exportWishlist()"
                        class="bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700 transition-colors">
                    Exportar
                </button>
            </div>

            <!-- Alertas de preço -->
            <div class="text-center">
                <i class="fas fa-bell text-purple-600 text-3xl mb-3"></i>
                <h3 class="text-lg font-semibold text-gray-900 mb-2">Alertas de preço</h3>
                <p class="text-gray-600 mb-4">Get notified when prices drop on your wishlist items</p>
                <button onclick="managePriceAlerts()"
                        class="bg-purple-600 text-white px-4 py-2 rounded-lg hover:bg-purple-700 transition-colors">
                    Manage Alerts
                </button>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
// Bulk operations
function bulkAddToCart() {
    if (confirm('Adicionar todos os favoritos ao carrinho?')) {
        const productIds = Array.from(document.querySelectorAll('[onclick*="moveToCart"]'))
            .map(btn => btn.getAttribute('onclick').match(/\d+/)[0]);

        // Implementation for bulk add to cart
        showNotification('A adição em massa ao carrinho estará disponível em breve.', 'info');
    }
}

function bulkRemove() {
    if (confirm('Remover todos os favoritos? Esta ação não poderá ser desfeita.')) {
        const productIds = Array.from(document.querySelectorAll('[onclick*="removeFromWishlist"]'))
            .map(btn => btn.getAttribute('onclick').match(/\d+/)[0]);

        // Implementation for bulk remove
        showNotification('A remoção em massa estará disponível em breve.', 'info');
    }
}

// Individual item operations
async function moveToCart(productId) {
    try {
        const response = await fetch(`{{ route('api.wishlist.move-to-cart', ['id' => ':id']) }}`.replace(':id', productId), {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json'
            }
        });

        if (response.ok) {
            showNotification('Produto movido para o carrinho.', 'success');
            // Reload page to update wishlist
            setTimeout(() => location.reload(), 1000);
        } else {
            showNotification('Não foi possível mover o produto para o carrinho.', 'error');
        }
    } catch (error) {
        console.error('Error moving to cart:', error);
        showNotification('Erro ao mover para o carrinho.', 'error');
    }
}

async function removeFromWishlist(productId) {
    if (confirm('Remover este item dos favoritos?')) {
        try {
            const response = await fetch(`{{ route('api.wishlist.destroy', ['id' => ':id']) }}`.replace(':id', productId), {
                method: 'DELETE',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                }
            });

            if (response.ok) {
                showNotification('Produto removido dos favoritos.', 'success');
                // Reload page to update wishlist
                setTimeout(() => location.reload(), 1000);
            } else {
                showNotification('Não foi possível remover o produto dos favoritos.', 'error');
            }
        } catch (error) {
            console.error('Error removing from wishlist:', error);
            showNotification('Erro ao remover dos favoritos.', 'error');
        }
    }
}

function updateQuantity(productId) {
    const newQuantity = prompt('Informe a nova quantidade:');
    if (newQuantity && !isNaN(newQuantity) && newQuantity > 0) {
        // Implementation for updating quantity
        showNotification('A atualização de quantidade estará disponível em breve.', 'info');
    }
}

// Favoritos actions
function shareWishlist() {
    // Implementation for sharing wishlist
    showNotification('O compartilhamento estará disponível em breve.', 'info');
}

function exportWishlist() {
    // Implementation for exporting wishlist
    showNotification('A exportação estará disponível em breve.', 'info');
}

function managePriceAlerts() {
    // Implementation for managing price alerts
    showNotification('A gestão de alertas de preço estará disponível em breve.', 'info');
}

// Adicionar aos favoritos function
async function addToWishlist(productId) {
    try {
        const response = await fetch('{{ route("api.wishlist.store") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json'
            },
            body: JSON.stringify({
                product_id: productId,
                quantity: 1
            })
        });

        if (response.ok) {
            showNotification('Produto adicionado aos favoritos!', 'success');
        } else {
            showNotification('Não foi possível adicionar o produto aos favoritos.', 'error');
        }
    } catch (error) {
        console.error('Error adding to wishlist:', error);
        showNotification('Erro ao adicionar aos favoritos.', 'error');
    }
}

// Notification function
function showNotification(message, type) {
    const notification = document.createElement('div');
    notification.className = `fixed top-4 right-4 p-4 rounded-lg shadow-lg z-50 ${
        type === 'success' ? 'bg-green-500 text-white' :
        type === 'error' ? 'bg-red-500 text-white' :
        'bg-blue-500 text-white'
    }`;
    notification.textContent = message;

    document.body.appendChild(notification);

    setTimeout(() => {
        notification.remove();
    }, 3000);
}
</script>
@endpush

@push('styles')
<style>
.line-clamp-2 {
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

/* Hover effects */
.hover\:shadow-lg:hover {
    box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
}

/* Preço drop animation */
@keyframes priceDrop {
    0% { transform: scale(1); }
    50% { transform: scale(1.1); }
    100% { transform: scale(1); }
}

.bg-red-500 {
    animation: priceDrop 2s ease-in-out infinite;
}
</style>
@endpush
@endsection






