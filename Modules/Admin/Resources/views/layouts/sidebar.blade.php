<aside class="app-sidebar bg-body-secondary shadow" data-bs-theme="dark">
    <div class="sidebar-brand">
        <a class="brand-link" href="{{ route('admin') }}">
            <span class="brand-image brand-icon elevation-1">
                <i class="fas fa-store"></i>
            </span>
            <span class="brand-text-wrap">
                <span class="brand-text fw-semibold">Rataplam</span>
                <small class="brand-subtitle">Painel AdminLTE 4</small>
            </span>
        </a>
    </div>
    <div class="sidebar-wrapper">
        <div class="admin-sidebar-panel">
            <div class="admin-sidebar-avatar">
                @php $sidebarUser = auth()->user(); @endphp
                @if($sidebarUser && $sidebarUser->getFirstMediaUrl('photo'))
                    <img src="{{ $sidebarUser->getFirstMediaUrl('photo') }}" alt="Avatar">
                @else
                    <img src="{{ asset('backend/img/avatar.png') }}" alt="Avatar">
                @endif
            </div>
            <div class="admin-sidebar-panel-copy">
                <strong>{{ $sidebarUser?->name ?? 'Conta administrativa' }}</strong>
                <span>
                    @hasrole('super-admin')
                        @lang('sidebar.admin')
                    @else
                        Conta autenticada
                    @endhasrole
                </span>
            </div>
        </div>
        <nav class="mt-2">
            <ul class="nav nav-pills nav-sidebar flex-column sidebar-menu" id="accordionSidebar" data-lte-toggle="treeview" role="menu" data-accordion="false">
                <hr class="sidebar-divider my-0">

    <!-- Nav Item - Dashboard -->
    <li class="nav-item {{ request()->routeIs('admin') ? 'active' : '' }}">
        <a class="nav-link {{ request()->routeIs('admin') ? 'active' : '' }}" href="{{route('admin')}}">
            <i class="fas fa-fw fa-tachometer-alt"></i>
            <span>@lang('sidebar.dashboard')</span></a>
    </li>
    <!-- Divider -->
    <hr class="sidebar-divider">

    @hasrole('super-admin')
    <!-- Analytics Dashboard -->
    <li class="nav-item">
        <a class="nav-link" href="{{route('admin.analytics')}}">
            <i class="fas fa-chart-line"></i>
            <span>@lang('sidebar.analytics_dashboard')</span>
        </a>
    </li>

    <!-- Divider -->
    <hr class="sidebar-divider">
    @endhasrole

    <div class="sidebar-heading">
        @lang('sidebar.shop')
    </div>
    @hasrole('super-admin')
    {{-- Attrinute --}}
    <li class="nav-item">
        <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#attributeCollapse"
           aria-expanded="true" aria-controls="attributeCollapse">
            <i class="fas fa-cubes"></i>
            <span>@lang('sidebar.attributes')</span>
        </a>
        <div id="attributeCollapse" class="collapse" aria-labelledby="headingTwo" data-parent="#accordionSidebar">
            <div class="bg-white py-2 collapse-inner rounded">
                <h6 class="collapse-header">@lang('sidebar.attribute_options'):</h6>
                <a class="collapse-item" href="{{route('attributes.index')}}">@lang('sidebar.attributes')</a>
                <a class="collapse-item" href="{{route('attributes.create')}}">@lang('sidebar.add_attribute')</a>
                <a class="collapse-item"
                   href="{{route('attribute_groups.index')}}">@lang('sidebar.attribute_groups')</a>
            </div>
        </div>
    </li>
    {{-- Attrinute --}}
    {{-- Banner --}}
    <li class="nav-item">
        <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#bannerCollapse" aria-expanded="true"
           aria-controls="brandCollapse">
            <i class="fas fa-table"></i>
            <span>@lang('sidebar.banners')</span>
        </a>
        <div id="bannerCollapse" class="collapse" aria-labelledby="headingTwo" data-parent="#accordionSidebar">
            <div class="bg-white py-2 collapse-inner rounded">
                <h6 class="collapse-header">@lang('sidebar.brand_options'):</h6>
                <a class="collapse-item" href="{{route('banners.index')}}">@lang('sidebar.banners')</a>
                <a class="collapse-item" href="{{route('banners.create')}}">@lang('sidebar.add_banners')</a>
            </div>
        </div>
    </li>
    {{-- Marcas --}}
    <li class="nav-item">
        <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#brandCollapse" aria-expanded="true"
           aria-controls="brandCollapse">
            <i class="fas fa-table"></i>
            <span>@lang('sidebar.brands')</span>
        </a>
        <div id="brandCollapse" class="collapse" aria-labelledby="headingTwo" data-parent="#accordionSidebar">
            <div class="bg-white py-2 collapse-inner rounded">
                <h6 class="collapse-header">@lang('sidebar.brand_options'):</h6>
                <a class="collapse-item" href="{{route('brands.index')}}">@lang('sidebar.brands')</a>
                <a class="collapse-item" href="{{route('brands.create')}}">@lang('sidebar.add_brand')</a>
            </div>
        </div>
    </li>
    {{-- Bandle --}}
    <li class="nav-item">
        <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#bundleCollapse"
           aria-expanded="true" aria-controls="bundleCollapse">
            <i class="fas fa-cubes"></i>
            <span>@lang('sidebar.bundles')</span>
        </a>
        <div id="bundleCollapse" class="collapse" aria-labelledby="headingTwo" data-parent="#accordionSidebar">
            <div class="bg-white py-2 collapse-inner rounded">
                <h6 class="collapse-header">@lang('sidebar.bundles_options'):</h6>
                <a class="collapse-item" href="{{route('bundles.index')}}">@lang('sidebar.bundles')</a>
                <a class="collapse-item" href="{{route('bundles.create')}}">@lang('sidebar.add_bundles')</a>

            </div>
        </div>
    </li>
    <!-- Categorias -->
    <li class="nav-item">
        <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#categoryCollapse"
           aria-expanded="true" aria-controls="categoryCollapse">
            <i class="fas fa-sitemap"></i>
            <span>@lang('sidebar.category')</span>
        </a>
        <div id="categoryCollapse" class="collapse" aria-labelledby="headingTwo" data-parent="#accordionSidebar">
            <div class="bg-white py-2 collapse-inner rounded">
                <h6 class="collapse-header">@lang('sidebar.category_options'):</h6>
                <a class="collapse-item" href="{{route('categories.index')}}">@lang('sidebar.category')</a>
                <a class="collapse-item" href="{{route('categories.create')}}">@lang('sidebar.add_category')</a>
            </div>
        </div>
    </li>
    {{-- Products --}}
    {{-- Bandle --}}
    <li class="nav-item">
        <a class="nav-link" href="{{route('admin.coupons.index')}}">
            <i class="fas fa-table"></i>
            <span>@lang('sidebar.coupons')</span></a>
    </li>
    <!-- Comments -->
    <li class="nav-item">
        <a class="nav-link" href="{{route('comments.index')}}">
            <i class="fas fa-comments fa-chart-area"></i>
            <span>@lang('sidebar.comments')</span>
        </a>
    </li>

    {{-- Products --}}
    <li class="nav-item">
        <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#productCollapse"
           aria-expanded="true" aria-controls="productCollapse">
            <i class="fas fa-cubes"></i>
            <span>@lang('sidebar.products')</span>
        </a>
        <div id="productCollapse" class="collapse" aria-labelledby="headingTwo" data-parent="#accordionSidebar">
            <div class="bg-white py-2 collapse-inner rounded">
                <h6 class="collapse-header">@lang('sidebar.product_options'):</h6>
                <a class="collapse-item" href="{{route('admin.products.index')}}">@lang('sidebar.products')</a>
                <a class="collapse-item" href="{{route('admin.products.create')}}">@lang('sidebar.add_product')</a>
                <a class="collapse-item" href="{{route('export-import-product.index')}}">@lang('sidebar.csv_import_export')</a>
            </div>
        </div>
    </li>

    {{-- Product Stats --}}
    <li class="nav-item">
        <a class="nav-link" href="{{ route('product-stats.index') }}">
            <i class="fas fa-chart-bar"></i>
            <span>@lang('sidebar.product_stats')</span>
        </a>
    </li>
    {{-- Shipping --}}
    <li class="nav-item">
        <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#shippingCollapse"
           aria-expanded="true" aria-controls="shippingCollapse">
            <i class="fas fa-truck"></i>
            <span>@lang('sidebar.shipping')</span>
        </a>
        <div id="shippingCollapse" class="collapse" aria-labelledby="headingTwo" data-parent="#accordionSidebar">
            <div class="bg-white py-2 collapse-inner rounded">
                <h6 class="collapse-header">@lang('sidebar.shipping_options'):</h6>
                <a class="collapse-item" href="{{route('admin.shipping.index')}}">@lang('sidebar.shipping')</a>
                <a class="collapse-item" href="{{route('admin.shipping.create')}}">@lang('sidebar.add_shipping')</a>
            </div>
        </div>
    </li>
    {{-- Newsletter --}}
    <li class="nav-item">
        <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#newsletterCollapse"
           aria-expanded="true" aria-controls="newsletterCollapse">
            <i class="fas fa-envelope"></i>
            <span>@lang('sidebar.newsletters')</span>
        </a>
        <div id="newsletterCollapse" class="collapse" aria-labelledby="headingTwo" data-parent="#accordionSidebar">
            <div class="bg-white py-2 collapse-inner rounded">
                <h6 class="collapse-header">@lang('sidebar.newsletters_options'):</h6>
                <a class="collapse-item" href="{{route('newsletters.index')}}">@lang('sidebar.newsletters')</a>
                <a class="collapse-item" href="{{route('newsletters.create')}}">@lang('sidebar.add_newsletter')</a>
                <a class="collapse-item" href="{{route('admin.email-templates.index')}}">@lang('sidebar.email_templates')</a>
                <a class="collapse-item" href="{{route('admin.email-templates.create')}}">@lang('sidebar.create_template')</a>
                <a class="collapse-item" href="{{route('admin.email-campaigns.index')}}">@lang('sidebar.email_campaigns')</a>
                <a class="collapse-item" href="{{route('admin.email-campaigns.create')}}">@lang('sidebar.create_campaign')</a>
            </div>
        </div>
    </li>
    @endhasrole

    <!--Orders -->
    <li class="nav-item">
        <a class="nav-link" href="{{route('orders.index')}}">
            <i class="fas fa-hammer fa-chart-area"></i>
            <span>@lang('sidebar.orders')</span>
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link" href="{{ route('admin.complaints.index') }}">
            <i class="fas fa-question fa-chart-area"></i>
            <span>@lang('sidebar.complaints')</span>
        </a>
    </li>
    <!-- Reviews -->
    <li class="nav-item">
        <a class="nav-link" href="{{ route('reviews.index') }}">
            <i class="fas fa-comments"></i>
            <span>@lang('sidebar.avaliacoes')</span></a>
    </li>


    <!-- Divider -->
    <hr class="sidebar-divider">

    @hasrole('super-admin')
    <!-- Heading -->
    <div class="sidebar-heading">
        @lang('sidebar.marketing')
    </div>

    <!-- Email Marketing -->
    <li class="nav-item">
        <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#emailMarketingCollapse" aria-expanded="true"
           aria-controls="emailMarketingCollapse">
            <i class="fas fa-envelope"></i>
            <span>@lang('sidebar.email_marketing')</span>
        </a>
        <div id="emailMarketingCollapse" class="collapse" aria-labelledby="headingPages" data-parent="#accordionSidebar">
            <div class="bg-white py-2 collapse-inner rounded">
                <h6 class="collapse-header">@lang('sidebar.email_marketing_options'):</h6>
                <a class="collapse-item" href="{{route('newsletters.index')}}">@lang('sidebar.newsletters')</a>
                <a class="collapse-item" href="{{route('newsletters.create')}}">@lang('sidebar.create_campaign')</a>
                <a class="collapse-item" href="{{route('admin.email-campaigns.analytics')}}">@lang('sidebar.email_analytics')</a>
                <a class="collapse-item" href="{{ route('admin.email-campaigns.index') }}">@lang('sidebar.campaigns')</a>
            </div>
        </div>
    </li>

    <!-- Abandoned Cart -->
    <li class="nav-item">
        <a class="nav-link" href="{{ route('admin.analytics.abandoned-carts') }}">
            <i class="fas fa-shopping-cart"></i>
            <span>@lang('sidebar.abandoned_carts')</span>
        </a>
    </li>
    @endhasrole

    <!-- Divider -->
    <hr class="sidebar-divider">

    <!-- Heading -->
    <div class="sidebar-heading">
        @lang('sidebar.posts')
    </div>
    @hasrole('super-admin')

    <!-- Products -->
    <li class="nav-item">
        <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#postCollapse" aria-expanded="true"
           aria-controls="postCollapse">
            <i class="fas fa-fw fa-folder"></i>
            <span>@lang('sidebar.posts')</span>
        </a>
        <div id="postCollapse" class="collapse" aria-labelledby="headingPages" data-parent="#accordionSidebar">
            <div class="bg-white py-2 collapse-inner rounded">
                <h6 class="collapse-header">@lang('sidebar.post_options'):</h6>
                <a class="collapse-item" href="{{route('posts.index')}}">@lang('sidebar.posts')</a>
                <a class="collapse-item" href="{{route('posts.create')}}">@lang('sidebar.add_post')</a>
            </div>
        </div>
    </li>

    <!-- Tags -->
    <li class="nav-item">
        <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#tagCollapse" aria-expanded="true"
           aria-controls="tagCollapse">
            <i class="fas fa-tags fa-folder"></i>
            <span>@lang('sidebar.tags')</span>
        </a>
        <div id="tagCollapse" class="collapse" aria-labelledby="headingPages" data-parent="#accordionSidebar">
            <div class="bg-white py-2 collapse-inner rounded">
                <h6 class="collapse-header">@lang('sidebar.tags_options'):</h6>
                <a class="collapse-item" href="{{route('tags.index')}}">@lang('sidebar.tags')</a>
                <a class="collapse-item" href="{{route('tags.create')}}">@lang('sidebar.add_tag')</a>
            </div>
        </div>
    </li>
    <li class="nav-item">
        <a class="nav-link" href="{{route('admin.coupons.index')}}">
            <i class="fas fa-table"></i>
            <span>@lang('sidebar.coupons')</span></a>
    </li>
    @endhasrole
    <!-- Comments -->
    <li class="nav-item">
        <a class="nav-link" href="{{route('comments.index')}}">
            <i class="fas fa-comments fa-chart-area"></i>
            <span>@lang('sidebar.comments')</span>
        </a>
    </li>


    <!-- Divider -->
    <hr class="sidebar-divider d-none d-md-block">

    @hasrole('super-admin')
    <!-- Heading -->
    <div class="sidebar-heading">
        @lang('sidebar.seo_performance')
    </div>

    <!-- SEO Tools -->
    <li class="nav-item">
        <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#seoCollapse" aria-expanded="true"
           aria-controls="seoCollapse">
            <i class="fas fa-search"></i>
            <span>@lang('sidebar.seo_tools')</span>
        </a>
        <div id="seoCollapse" class="collapse" aria-labelledby="headingPages" data-parent="#accordionSidebar">
            <div class="bg-white py-2 collapse-inner rounded">
                <h6 class="collapse-header">@lang('sidebar.seo_options'):</h6>
                <a class="collapse-item" href="/sitemap.xml">@lang('sidebar.xml_sitemap')</a>
                <a class="collapse-item" href="/robots.txt">@lang('sidebar.robots_txt')</a>
                <a class="collapse-item" href="#" onclick="generateSitemap(); return false;">@lang('sidebar.generate_sitemap')</a>
                <a class="collapse-item" href="{{ route('settings.seo.index') }}">@lang('sidebar.meta_tags')</a>
            </div>
        </div>
    </li>

    <!-- Performance -->
    <li class="nav-item">
        <a class="nav-link" href="#" onclick="clearCache(); return false;">
            <i class="fas fa-tachometer-alt"></i>
            <span>@lang('sidebar.clear_cache')</span>
        </a>
    </li>
    @endhasrole

    <!-- Divider -->
    <hr class="sidebar-divider d-none d-md-block">
    <!-- Heading -->
    <div class="sidebar-heading">
        @lang('sidebar.general_settings')
    </div>

    <!-- Users -->
    @hasrole('super-admin')
    <!-- General settings -->
    {{-- Config --}}
    <li class="nav-item">
        <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#configCollapse"
           aria-expanded="true" aria-controls="shippingCollapse">
            <i class="fas fa-wrench"></i>
            <span>@lang('sidebar.configuration')</span>
        </a>
        <div id="configCollapse" class="collapse" aria-labelledby="headingTwo" data-parent="#accordionSidebar">
            <div class="bg-white py-2 collapse-inner rounded">
                <h6 class="collapse-header">@lang('sidebar.configuration'):</h6>
                <a class="collapse-item" href="{{route('users.index')}}">@lang('sidebar.users')</a>
                <a class="collapse-item" href="{{route('roles.index')}}">@lang('sidebar.roles')</a>
                <a class="collapse-item" href="{{route('permissions.index')}}">@lang('sidebar.permissions')</a>
                <a class="collapse-item" href="{{route('settings.index')}}">@lang('sidebar.settings')</a>
                <a class="collapse-item" href="{{route('settings.payment.index')}}">@lang('sidebar.payment_settings')</a>
                <a class="collapse-item" href="{{route('settings.shipping.index')}}">@lang('sidebar.shipping_settings')</a>
                <a class="collapse-item" href="{{route('settings.email.index')}}">@lang('sidebar.email_settings')</a>
                <a class="collapse-item" href="{{route('settings.seo.index')}}">@lang('sidebar.seo_settings')</a>
                <a class="collapse-item" href="{{route('pages.index')}}">@lang('sidebar.page')</a>
                @if (config('tenant.multi_tenant.enabled'))
                    <a class="collapse-item" href="{{route('tenant.index')}}">@lang('sidebar.tenant')</a>
                @endif
                <a class="collapse-item"
                   href="javascript:void(0);">@lang('sidebar.blocked_ip')</a>
                <a class="collapse-item" href="{{route('activity')}}">@lang('sidebar.activity_log')</a>
                <a class="collapse-item" href="{{ url('translations/') }}">@lang('sidebar.translation')</a>
            </div>
        </div>
    </li>
    @endhasrole

    <!-- Sidebar Toggler (Sidebar) -->
            </ul>
        </nav>
    </div>
</aside>

<script>
(function () {
    async function parseError(response) {
        const contentType = response.headers.get('content-type') || '';

        if (contentType.includes('application/json')) {
            const payload = await response.json();
            throw new Error(payload.message || 'O servidor retornou um erro inesperado.');
        }

        const html = await response.text();
        const text = html
            .replace(/<script[\s\S]*?<\/script>/gi, ' ')
            .replace(/<style[\s\S]*?<\/style>/gi, ' ')
            .replace(/<[^>]+>/g, ' ')
            .replace(/\s+/g, ' ')
            .trim();

        throw new Error(text || `Falha HTTP ${response.status}.`);
    }

    async function postAdminAction(url) {
        const response = await fetch(url, {
            method: 'POST',
            headers: {
                'Accept': 'application/json',
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'X-Requested-With': 'XMLHttpRequest',
            },
            credentials: 'same-origin',
        });

        if (!response.ok) {
            await parseError(response);
        }

        const contentType = response.headers.get('content-type') || '';

        if (!contentType.includes('application/json')) {
            return {
                success: true,
                message: 'Operacao executada com sucesso.',
            };
        }

        return response.json();
    }

    window.generateSitemap = async function () {
        const decision = await Swal.fire({
            title: 'Gerar sitemap XML?',
            text: 'O processo atualiza os arquivos de SEO da loja e pode levar alguns minutos.',
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'Gerar sitemap',
            cancelButtonText: 'Cancelar',
            reverseButtons: true,
        });

        if (!decision.isConfirmed) {
            return;
        }

        try {
            Swal.fire({
                title: 'Gerando sitemap',
                text: 'Aguarde enquanto o sistema processa os arquivos.',
                allowOutsideClick: false,
                didOpen: () => Swal.showLoading(),
            });

            const payload = await postAdminAction('{{ url('/admin/system/seo/sitemap/generate') }}');

            await Swal.fire({
                title: 'Sitemap atualizado',
                text: payload.message || 'Sitemap gerado com sucesso.',
                icon: 'success',
                confirmButtonText: 'Fechar',
            });
        } catch (error) {
            await Swal.fire({
                title: 'Falha ao gerar sitemap',
                text: error.message,
                icon: 'error',
                confirmButtonText: 'Entendi',
            });
        }
    };

    window.clearCache = async function () {
        const decision = await Swal.fire({
            title: 'Limpar caches do sistema?',
            text: 'O proximo carregamento pode ficar um pouco mais lento ate os caches serem recriados.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Limpar agora',
            cancelButtonText: 'Cancelar',
            reverseButtons: true,
        });

        if (!decision.isConfirmed) {
            return;
        }

        try {
            Swal.fire({
                title: 'Limpando caches',
                text: 'Executando optimize:clear no ambiente atual.',
                allowOutsideClick: false,
                didOpen: () => Swal.showLoading(),
            });

            const payload = await postAdminAction('{{ url('/admin/system/cache/all/clear') }}');

            await Swal.fire({
                title: 'Caches limpos',
                text: payload.message || 'Todos os caches foram limpos com sucesso.',
                icon: 'success',
                confirmButtonText: 'Fechar',
            });
        } catch (error) {
            await Swal.fire({
                title: 'Falha ao limpar caches',
                text: error.message,
                icon: 'error',
                confirmButtonText: 'Entendi',
            });
        }
    };
})();
</script>
