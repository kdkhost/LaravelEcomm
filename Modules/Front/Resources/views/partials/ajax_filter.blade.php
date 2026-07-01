{{-- AJAX Filter Component --}}
<div id="ajax-filter-app" data-category="{{ $category->id ?? '' }}">
    {{-- Filter Sidebar --}}
    <div class="filter-sidebar">
        @include('front::partials.layered_navigation')
    </div>

    {{-- Active Filtros --}}
    <div id="active-filters" class="mb-3">
        {{-- Populated via JS --}}
    </div>

    {{-- Product Grid --}}
    <div id="product-grid-container">
        <div id="product-grid" class="row">
            @include('front::partials.product_grid_items', ['products' => $products])
        </div>

        {{-- Pagination --}}
        <div id="pagination-container">
            {{ $products->links() }}
        </div>
    </div>

    {{-- Loading Overlay --}}
    <div id="filter-loading" class="d-none">
        <div class="spinner-overlay">
            <div class="spinner-border text-primary" role="status">
                <span class="sr-only">Loading...</span>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
(function() {
    'use strict';

    const AjaxFilter = {
        container: document.getElementById('ajax-filter-app'),
        loading: document.getElementById('filter-loading'),
        productGrid: document.getElementById('product-grid'),
        paginationContainer: document.getElementById('pagination-container'),
        activeFiltrosContainer: document.getElementById('active-filters'),

        currentFiltros: {},
        categoryId: null,

        init() {
            if (!this.container) return;

            this.categoryId = this.container.dataset.category;
            this.bindEvents();
            this.parseUrlParams();
        },

        bindEvents() {
            // Filter changes
            document.querySelectorAll('.filter-option input[type="checkbox"]').forEach(checkbox => {
                checkbox.addEventListener('change', (e) => {
                    this.handleFilterChange(e);
                });
            });

            // Color swatches
            document.querySelectorAll('.color-swatch').forEach(swatch => {
                swatch.addEventListener('click', (e) => {
                    e.preventDefault();
                    this.handleSwatchClick(e);
                });
            });

            // Preco slider
            const priceSlider = document.getElementById('price-slider');
            if (priceSlider) {
                priceSlider.addEventListener('change', (e) => {
                    this.handlePriceChange(e.target.value);
                });
            }

            // Sort dropdown
            const sortSelect = document.getElementById('sort-by');
            if (sortSelect) {
                sortSelect.addEventListener('change', (e) => {
                    this.handleSortChange(e.target.value);
                });
            }

            // Pagination clicks (delegate)
            document.addEventListener('click', (e) => {
                if (e.target.matches('.pagination a')) {
                    e.preventDefault();
                    this.handlePagination(e.target.href);
                }
            });

            // Popstate (browser back/forward)
            window.addEventListener('popstate', (e) => {
                if (e.state && e.state.filters) {
                    this.currentFiltros = e.state.filters;
                    this.applyFiltros(false);
                }
            });
        },

        handleFilterChange(e) {
            const checkbox = e.target;
            const attributeCode = this.getAttributeCodeFromElement(checkbox);
            const value = checkbox.value;

            if (!this.currentFiltros[attributeCode]) {
                this.currentFiltros[attributeCode] = [];
            }

            if (checkbox.checked) {
                if (!this.currentFiltros[attributeCode].includes(value)) {
                    this.currentFiltros[attributeCode].push(value);
                }
            } else {
                this.currentFiltros[attributeCode] = this.currentFiltros[attributeCode]
                    .filter(v => v !== value);

                if (this.currentFiltros[attributeCode].length === 0) {
                    delete this.currentFiltros[attributeCode];
                }
            }

            this.applyFiltros();
        },

        handleSwatchClick(e) {
            e.preventDefault();
            const swatch = e.currentTarget;
            const attributeCode = swatch.dataset.attribute;
            const value = swatch.dataset.value;

            // Toggle active state
            document.querySelectorAll(`.color-swatch[data-attribute="${attributeCode}"]`)
                .forEach(s => s.classList.remove('active'));
            swatch.classList.add('active');

            this.currentFiltros[attributeCode] = [value];
            this.applyFiltros();
        },

        handlePriceChange(value) {
            this.currentFiltros.price_max = value;
            this.applyFiltros();
        },

        handleSortChange(sortValue) {
            this.currentFiltros.sort = sortValue;
            this.applyFiltros();
        },

        handlePagination(url) {
            const urlObj = new URL(url);
            const page = urlObj.searchParams.get('page');
            this.currentFiltros.page = page;
            this.applyFiltros();
        },

        applyFiltros(updateUrl = true) {
            this.showLoading();

            const params = new URLSearchParams();

            Object.entries(this.currentFiltros).forEach(([key, value]) => {
                if (Array.isArray(value)) {
                    params.set(key, value.join(','));
                } else if (value) {
                    params.set(key, value);
                }
            });

            if (this.categoryId) {
                params.set('category', this.categoryId);
            }

            fetch(`/api/products/filter?${params.toString()}`, {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                }
            })
            .then(response => response.json())
            .then(data => {
                this.updateProductGrid(data.products);
                this.updatePagination(data.pagination);
                this.updateActiveFiltros();
                this.updateFilterCounts(data.filter_counts);

                if (updateUrl) {
                    this.updateBrowserUrl(params);
                }
            })
            .catch(error => {
                console.error('Filter error:', error);
            })
            .finally(() => {
                this.hideLoading();
            });
        },

        updateProductGrid(productsHtml) {
            if (productsHtml) {
                this.productGrid.innerHTML = productsHtml;
            } else {
                this.productGrid.innerHTML = '<div class="col-12 text-center">No products found</div>';
            }
        },

        updatePagination(paginationHtml) {
            if (paginationHtml) {
                this.paginationContainer.innerHTML = paginationHtml;
            } else {
                this.paginationContainer.innerHTML = '';
            }
        },

        updateActiveFiltros() {
            const activeFiltros = [];

            Object.entries(this.currentFiltros).forEach(([key, value]) => {
                if (key === 'page' || key === 'sort') return;

                if (Array.isArray(value)) {
                    value.forEach(v => {
                        activeFiltros.push({ code: key, value: v });
                    });
                } else {
                    activeFiltros.push({ code: key, value });
                }
            });

            if (activeFiltros.length === 0) {
                this.activeFiltrosContainer.innerHTML = '';
                return;
            }

            const html = activeFiltros.map(filter => `
                <span class="badge badge-primary mr-2 mb-2">
                    ${filter.code}: ${filter.value}
                    <button type="button" class="btn btn-sm btn-link text-white p-0 ml-2"
                            onclick="AjaxFilter.removeFilter('${filter.code}', '${filter.value}')">
                        &times;
                    </button>
                </span>
            `).join('');

            this.activeFiltrosContainer.innerHTML = `
                <div class="active-filters">
                    <small class="text-muted mr-2">Active Filtros:</small>
                    ${html}
                    <button type="button" class="btn btn-sm btn-link text-danger"
                            onclick="AjaxFilter.clearAllFiltros()">
                        Clear All
                    </button>
                </div>
            `;
        },

        updateFilterCounts(counts) {
            // Update option counts based on current filter results
            if (counts) {
                Object.entries(counts).forEach(([attributeCode, options]) => {
                    Object.entries(options).forEach(([value, count]) => {
                        const countElement = document.querySelector(
                            `[data-attribute="${attributeCode}"][data-value="${value}"] .count`
                        );
                        if (countElement) {
                            countElement.textContent = count;
                        }
                    });
                });
            }
        },

        removeFilter(code, value) {
            if (Array.isArray(this.currentFiltros[code])) {
                this.currentFiltros[code] = this.currentFiltros[code].filter(v => v !== value);
                if (this.currentFiltros[code].length === 0) {
                    delete this.currentFiltros[code];
                }
            } else {
                delete this.currentFiltros[code];
            }

            // Uncheck corresponding checkbox
            const checkbox = document.querySelector(
                `input[name="${code}"][value="${value}"]`
            );
            if (checkbox) {
                checkbox.checked = false;
            }

            this.applyFiltros();
        },

        clearAllFiltros() {
            this.currentFiltros = {};

            // Uncheck all checkboxes
            document.querySelectorAll('.filter-option input[type="checkbox"]')
                .forEach(cb => cb.checked = false);

            this.applyFiltros();
        },

        updateBrowserUrl(params) {
            const url = new URL(window.location.href);
            url.search = params.toString();
            window.history.pushState({ filters: this.currentFiltros }, '', url);
        },

        parseUrlParams() {
            const urlParams = new URLSearchParams(window.location.search);

            urlParams.forEach((value, key) => {
                if (value.includes(',')) {
                    this.currentFiltros[key] = value.split(',');
                } else {
                    this.currentFiltros[key] = value;
                }
            });

            // Mark checkboxes as checked based on URL params
            Object.entries(this.currentFiltros).forEach(([key, value]) => {
                if (Array.isArray(value)) {
                    value.forEach(v => {
                        const checkbox = document.querySelector(
                            `input[name="${key}"][value="${v}"]`
                        );
                        if (checkbox) checkbox.checked = true;
                    });
                }
            });
        },

        showLoading() {
            this.loading.classList.remove('d-none');
        },

        hideLoading() {
            this.loading.classList.add('d-none');
        },

        getAttributeCodeFromElement(element) {
            // Try to find attribute code from parent or data attribute
            return element.closest('[data-attribute-code]')?.dataset.attributeCode ||
                   element.name.replace('attribute_', '');
        }
    };

    // Expose to global scope for onclick handlers
    window.AjaxFilter = AjaxFilter;

    // Initialize on DOM ready
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', () => AjaxFilter.init());
    } else {
        AjaxFilter.init();
    }
})();
</script>
@endpush

@push('styles')
<style>
    .spinner-overlay {
        position: fixed;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: rgba(255, 255, 255, 0.8);
        display: flex;
        align-items: center;
        justify-content: center;
        z-index: 9999;
    }

    .active-filters {
        padding: 1rem;
        background: #f8f9fa;
        border-radius: 0.5rem;
    }

    .active-filters .badge {
        font-size: 0.875rem;
        padding: 0.5rem 0.75rem;
    }

    .active-filters .btn-link {
        text-decoration: none;
        font-size: 1.2rem;
        line-height: 1;
    }
</style>
@endpush
