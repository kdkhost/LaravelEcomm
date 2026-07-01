@forelse($products as $product)
    <div class="col-lg-4 col-md-6 mb-4">
        <div class="product-card h-100">
            {{-- Product Image --}}
            <div class="product-image position-relative">
                <a href="{{ route('front.product.show', $product->slug) }}">
                    @if($product->image)
                        <img src="{{ asset($product->image) }}"
                             alt="{{ $product->name }}"
                             class="img-fluid">
                    @else
                        <div class="placeholder-image">
                            <i class="fas fa-image fa-3x"></i>
                        </div>
                    @endif
                </a>

                {{-- Badges --}}
                <div class="product-badges">
                    @if($product->type === 'configurable')
                        <span class="badge badge-info">Configurable</span>
                    @endif
                    @if($product->isNew())
                        <span class="badge badge-success">New</span>
                    @endif
                    @if($product->hasSpecialPrice())
                        <span class="badge badge-danger">Sale</span>
                    @endif
                </div>

                {{-- Ações rápidas --}}
                <div class="quick-actions">
                    <button class="btn btn-light btn-sm add-to-wishlist"
                            data-product-id="{{ $product->id }}"
                            title="Adicionar aos favoritos">
                        <i class="far fa-heart"></i>
                    </button>
                    <button class="btn btn-light btn-sm quick-view"
                            data-product-id="{{ $product->id }}"
                            title="Visualização rápida">
                        <i class="far fa-eye"></i>
                    </button>
                </div>
            </div>

            {{-- Product Info --}}
            <div class="product-info p-3">
                {{-- Categoria --}}
                @if($product->categories->first())
                    <div class="product-category text-muted small mb-1">
                        {{ $product->categories->first()->name }}
                    </div>
                @endif

                {{-- Name --}}
                <h5 class="product-title">
                    <a href="{{ route('front.product.show', $product->slug) }}">
                        {{ Str::limit($product->name, 50) }}
                    </a>
                </h5>

                {{-- Rating --}}
                @if($product->reviews_count > 0)
                    <div class="product-rating mb-2">
                        @for($i = 1; $i <= 5; $i++)
                            @if($i <= $product->average_rating)
                                <i class="fas fa-star text-warning"></i>
                            @else
                                <i class="far fa-star text-warning"></i>
                            @endif
                        @endfor
                        <small class="text-muted">({{ $product->reviews_count }})</small>
                    </div>
                @endif

                {{-- Preço --}}
                <div class="product-price">
                    @if($product->hasSpecialPrice())
                        <span class="original-price text-muted text-decoration-line-through">
                            {{ format_currency((float) ($product->price)) }}
                        </span>
                        <span class="sale-price text-danger">
                            {{ format_currency((float) ($product->special_price)) }}
                        </span>
                    @else
                        <span class="regular-price">
                            {{ format_currency((float) ($product->price)) }}
                        </span>
                    @endif
                </div>

                {{-- Adicionar ao carrinho --}}
                <div class="mt-3">
                    @if($product->type === 'configurable')
                        <a href="{{ route('front.product.show', $product->slug) }}"
                           class="btn btn-outline-primary btn-block">
                            Select Options
                        </a>
                    @else
                        <button class="btn btn-primary btn-block add-to-cart"
                                data-product-id="{{ $product->id }}"
                                data-quantity="1">
                            <i class="fas fa-shopping-cart mr-2"></i>
                            Adicionar ao carrinho
                        </button>
                    @endif
                </div>
            </div>
        </div>
    </div>
@empty
    <div class="col-12 text-center py-5">
        <div class="empty-state">
            <i class="fas fa-search fa-3x text-muted mb-3"></i>
            <h4>No products found</h4>
            <p class="text-muted">Try adjusting your filters or search criteria</p>
            <button class="btn btn-outline-primary" onclick="AjaxFilter.clearAllFiltros()">
                Clear All Filtros
            </button>
        </div>
    </div>
@endforelse
