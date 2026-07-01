<ul class="navbar-nav bg-gradient-primary sidebar sidebar-dark accordion" id="accordionSidebar">
    <a class="sidebar-brand d-flex align-items-center justify-content-center" href="{{route('admin')}}">
        <div class="sidebar-brand-icon rotate-n-15">
            <i class="fas fa-laugh-wink"></i>
        </div>
        <div class="sidebar-brand-text mx-3">
            @hasrole('super-admin') @lang('sidebar.admin') @else {{ Auth::user()->name ?? 'Account' }} @endhasrole
        </div>
    </a>
    <hr class="sidebar-divider my-0">

    <li class="nav-item active">
        <a class="nav-link" href="{{route('admin')}}">
            <i class="fas fa-fw fa-tachometer-alt"></i>
            <span>@lang('sidebar.dashboard')</span>
        </a>
    </li>

    @hasrole('super-admin')
    <li class="nav-item">
        <a class="nav-link" href="{{route('admin.analytics')}}">
            <i class="fas fa-chart-line"></i>
            <span>@lang('sidebar.analytics_dashboard')</span>
        </a>
    </li>
    @endhasrole

    <hr class="sidebar-divider">

    <div class="sidebar-heading">@lang('sidebar.shop')</div>
    @hasrole('super-admin')
    <li class="nav-item">
        <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#attrCollapse">
            <i class="fas fa-cubes"></i>
            <span>@lang('sidebar.attributes')</span>
        </a>
        <div id="attrCollapse" class="collapse" data-parent="#accordionSidebar">
            <div class="bg-white py-1 collapse-inner rounded">
                <a class="collapse-item" href="{{route('attributes.index')}}">@lang('sidebar.attributes')</a>
                <a class="collapse-item" href="{{route('attributes.create')}}">@lang('sidebar.add_attribute')</a>
                <a class="collapse-item" href="{{route('attribute_groups.index')}}">@lang('sidebar.attribute_groups')</a>
            </div>
        </div>
    </li>
    <li class="nav-item">
        <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#bannerCollapse">
            <i class="fas fa-table"></i>
            <span>@lang('sidebar.banners')</span>
        </a>
        <div id="bannerCollapse" class="collapse" data-parent="#accordionSidebar">
            <div class="bg-white py-1 collapse-inner rounded">
                <a class="collapse-item" href="{{route('banners.index')}}">@lang('sidebar.banners')</a>
                <a class="collapse-item" href="{{route('banners.create')}}">@lang('sidebar.add_banners')</a>
            </div>
        </div>
    </li>
    <li class="nav-item">
        <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#brandCollapse">
            <i class="fas fa-table"></i>
            <span>@lang('sidebar.brands')</span>
        </a>
        <div id="brandCollapse" class="collapse" data-parent="#accordionSidebar">
            <div class="bg-white py-1 collapse-inner rounded">
                <a class="collapse-item" href="{{route('brands.index')}}">@lang('sidebar.brands')</a>
                <a class="collapse-item" href="{{route('brands.create')}}">@lang('sidebar.add_brand')</a>
            </div>
        </div>
    </li>
    <li class="nav-item">
        <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#bundleCollapse">
            <i class="fas fa-cubes"></i>
            <span>@lang('sidebar.bundles')</span>
        </a>
        <div id="bundleCollapse" class="collapse" data-parent="#accordionSidebar">
            <div class="bg-white py-1 collapse-inner rounded">
                <a class="collapse-item" href="{{route('bundles.index')}}">@lang('sidebar.bundles')</a>
                <a class="collapse-item" href="{{route('bundles.create')}}">@lang('sidebar.add_bundles')</a>
            </div>
        </div>
    </li>
    <li class="nav-item">
        <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#catCollapse">
            <i class="fas fa-sitemap"></i>
            <span>@lang('sidebar.category')</span>
        </a>
        <div id="catCollapse" class="collapse" data-parent="#accordionSidebar">
            <div class="bg-white py-1 collapse-inner rounded">
                <a class="collapse-item" href="{{route('categories.index')}}">@lang('sidebar.category')</a>
                <a class="collapse-item" href="{{route('categories.create')}}">@lang('sidebar.add_category')</a>
            </div>
        </div>
    </li>
    <li class="nav-item">
        <a class="nav-link" href="{{route('admin.coupons.index')}}">
            <i class="fas fa-table"></i>
            <span>@lang('sidebar.coupons')</span>
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link" href="{{route('comments.index')}}">
            <i class="fas fa-comments"></i>
            <span>@lang('sidebar.comments')</span>
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#prodCollapse">
            <i class="fas fa-cubes"></i>
            <span>@lang('sidebar.products')</span>
        </a>
        <div id="prodCollapse" class="collapse" data-parent="#accordionSidebar">
            <div class="bg-white py-1 collapse-inner rounded">
                <a class="collapse-item" href="{{route('admin.products.index')}}">@lang('sidebar.products')</a>
                <a class="collapse-item" href="{{route('admin.products.create')}}">@lang('sidebar.add_product')</a>
                <a class="collapse-item" href="{{route('export-import-product.index')}}">@lang('sidebar.csv_import_export')</a>
            </div>
        </div>
    </li>
    <li class="nav-item">
        <a class="nav-link" href="{{ route('product-stats.index') }}">
            <i class="fas fa-chart-bar"></i>
            <span>@lang('sidebar.product_stats')</span>
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#shipCollapse">
            <i class="fas fa-truck"></i>
            <span>@lang('sidebar.shipping')</span>
        </a>
        <div id="shipCollapse" class="collapse" data-parent="#accordionSidebar">
            <div class="bg-white py-1 collapse-inner rounded">
                <a class="collapse-item" href="{{route('admin.shipping.index')}}">@lang('sidebar.shipping')</a>
                <a class="collapse-item" href="{{route('admin.shipping.create')}}">@lang('sidebar.add_shipping')</a>
            </div>
        </div>
    </li>
    <li class="nav-item">
        <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#nslCollapse">
            <i class="fas fa-envelope"></i>
            <span>@lang('sidebar.newsletters')</span>
        </a>
        <div id="nslCollapse" class="collapse" data-parent="#accordionSidebar">
            <div class="bg-white py-1 collapse-inner rounded">
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

    <li class="nav-item">
        <a class="nav-link" href="{{ route('admin.cron-jobs.index') }}">
            <i class="fas fa-clock"></i>
            <span>@lang('sidebar.cron')</span>
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link" href="{{route('orders.index')}}">
            <i class="fas fa-hammer"></i>
            <span>@lang('sidebar.orders')</span>
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link" href="{{ route('admin.complaints.index') }}">
            <i class="fas fa-question"></i>
            <span>@lang('sidebar.complaints')</span>
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link" href="{{route('reviews.index')}}">
            <i class="fas fa-comments"></i>
            <span>@lang('sidebar.reviews')</span>
        </a>
    </li>

    <hr class="sidebar-divider">

    @hasrole('super-admin')
    <div class="sidebar-heading">@lang('sidebar.marketing')</div>
    <li class="nav-item">
        <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#emailMktCollapse">
            <i class="fas fa-envelope"></i>
            <span>@lang('sidebar.email_marketing')</span>
        </a>
        <div id="emailMktCollapse" class="collapse" data-parent="#accordionSidebar">
            <div class="bg-white py-1 collapse-inner rounded">
                <a class="collapse-item" href="{{route('newsletters.index')}}">@lang('sidebar.newsletters')</a>
                <a class="collapse-item" href="{{route('newsletters.create')}}">@lang('sidebar.create_campaign')</a>
                <a class="collapse-item" href="{{route('admin.email-campaigns.analytics')}}">@lang('sidebar.email_analytics')</a>
                <a class="collapse-item" href="{{ route('admin.email-campaigns.index') }}">@lang('sidebar.campaigns')</a>
            </div>
        </div>
    </li>
    <li class="nav-item">
        <a class="nav-link" href="{{ route('admin.analytics.abandoned-carts') }}">
            <i class="fas fa-shopping-cart"></i>
            <span>@lang('sidebar.abandoned_carts')</span>
        </a>
    </li>
    @endhasrole

    <hr class="sidebar-divider">

    <div class="sidebar-heading">@lang('sidebar.posts')</div>
    @hasrole('super-admin')
    <li class="nav-item">
        <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#postCollapse">
            <i class="fas fa-fw fa-folder"></i>
            <span>@lang('sidebar.posts')</span>
        </a>
        <div id="postCollapse" class="collapse" data-parent="#accordionSidebar">
            <div class="bg-white py-1 collapse-inner rounded">
                <a class="collapse-item" href="{{route('posts.index')}}">@lang('sidebar.posts')</a>
                <a class="collapse-item" href="{{route('posts.create')}}">@lang('sidebar.add_post')</a>
            </div>
        </div>
    </li>
    <li class="nav-item">
        <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#tagCollapse">
            <i class="fas fa-tags"></i>
            <span>@lang('sidebar.tags')</span>
        </a>
        <div id="tagCollapse" class="collapse" data-parent="#accordionSidebar">
            <div class="bg-white py-1 collapse-inner rounded">
                <a class="collapse-item" href="{{route('tags.index')}}">@lang('sidebar.tags')</a>
                <a class="collapse-item" href="{{route('tags.create')}}">@lang('sidebar.add_tag')</a>
            </div>
        </div>
    </li>
    @endhasrole

    <hr class="sidebar-divider">

    @hasrole('super-admin')
    <div class="sidebar-heading">@lang('sidebar.seo_performance')</div>
    <li class="nav-item">
        <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#seoCollapse">
            <i class="fas fa-search"></i>
            <span>@lang('sidebar.seo_tools')</span>
        </a>
        <div id="seoCollapse" class="collapse" data-parent="#accordionSidebar">
            <div class="bg-white py-1 collapse-inner rounded">
                <a class="collapse-item" href="/sitemap.xml">@lang('sidebar.xml_sitemap')</a>
                <a class="collapse-item" href="/robots.txt">@lang('sidebar.robots_txt')</a>
                <a class="collapse-item" href="#" onclick="generateSitemap()">@lang('sidebar.generate_sitemap')</a>
                <a class="collapse-item" href="{{ route('settings.seo.index') }}">@lang('sidebar.meta_tags')</a>
            </div>
        </div>
    </li>
    <li class="nav-item">
        <a class="nav-link" href="#" onclick="clearCache()">
            <i class="fas fa-tachometer-alt"></i>
            <span>@lang('sidebar.clear_cache')</span>
        </a>
    </li>
    @endhasrole

    <hr class="sidebar-divider">

    <div class="sidebar-heading">@lang('sidebar.general_settings')</div>
    @hasrole('super-admin')
    <li class="nav-item">
        <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#configCollapse">
            <i class="fas fa-wrench"></i>
            <span>@lang('sidebar.configuration')</span>
        </a>
        <div id="configCollapse" class="collapse" data-parent="#accordionSidebar">
            <div class="bg-white py-1 collapse-inner rounded">
                <a class="collapse-item" href="{{route('users.index')}}">@lang('sidebar.users')</a>
                <a class="collapse-item" href="{{route('roles.index')}}">@lang('sidebar.roles')</a>
                <a class="collapse-item" href="{{route('permissions.index')}}">@lang('sidebar.permissions')</a>
                <a class="collapse-item" href="{{route('settings.index')}}">@lang('sidebar.settings')</a>
                <a class="collapse-item" href="{{route('settings.appearance.index')}}">@lang('sidebar.appearance')</a>
                <a class="collapse-item" href="{{route('settings.payment.index')}}">@lang('sidebar.payment_settings')</a>
                <a class="collapse-item" href="{{route('settings.shipping.index')}}">@lang('sidebar.shipping_settings')</a>
                <a class="collapse-item" href="{{route('settings.email.index')}}">@lang('sidebar.email_settings')</a>
                <a class="collapse-item" href="{{route('settings.seo.index')}}">@lang('sidebar.seo_settings')</a>
                <a class="collapse-item" href="{{route('pages.index')}}">@lang('sidebar.page')</a>
                @if (config('tenant.multi_tenant.enabled'))
                    <a class="collapse-item" href="{{route('tenant.index')}}">@lang('sidebar.tenant')</a>
                @endif
                <a class="collapse-item" href="javascript:void(0);">@lang('sidebar.blocked_ip')</a>
                <a class="collapse-item" href="{{route('activity')}}">@lang('sidebar.activity_log')</a>
                @if(Route::has('admin.languages.index'))
                    <a class="collapse-item" href="{{ route('admin.languages.index') }}">@lang('sidebar.languages')</a>
                @endif
                <a class="collapse-item" href="{{ url('translations/') }}">@lang('sidebar.translation')</a>
            </div>
        </div>
    </li>
    @endhasrole

    <div class="text-center d-none d-md-inline">
        <button class="rounded-circle border-0" id="sidebarToggle"></button>
    </div>
</ul>

<style>
#accordionSidebar .nav-item .nav-link { padding: 0.5rem 0.8rem; font-size: 0.8rem; }
#accordionSidebar .nav-item .nav-link i { font-size: 0.85rem; min-width: 1.2rem; }
#accordionSidebar .collapse .collapse-inner .collapse-item { padding: 0.25rem 0.8rem; font-size: 0.78rem; }
#accordionSidebar .sidebar-heading { font-size: 0.65rem; padding: 0.6rem 0.8rem 0.3rem; }
#accordionSidebar .sidebar-divider { margin: 0.3rem 0; }
#accordionSidebar .sidebar-brand { height: auto; padding: 0.8rem 0; }
#accordionSidebar .sidebar-brand-text { font-size: 0.9rem; }
.sidebar.toggled #accordionSidebar .nav-item .nav-link span { display: none; }
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    var currentPath = window.location.pathname;
    document.querySelectorAll('#accordionSidebar .collapse').forEach(function(el) {
        var show = false;
        el.querySelectorAll('.collapse-item').forEach(function(item) {
            if (item.getAttribute('href') === currentPath) show = true;
        });
        if (show) {
            el.classList.add('show');
            el.closest('.nav-item')?.classList.add('active');
        }
    });
});

function generateSitemap() {
    Swal.fire({
        title: "Generate XML Sitemap?",
        icon: "question",
        showCancelButton: true,
        confirmButtonText: "Yes, generate!",
        cancelButtonText: "Cancel"
    }).then((result) => {
        if (result.isConfirmed) {
            fetch('/api/v1/admin/seo/generate-sitemap', { method:'POST',
                headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'), 'Content-Type':'application/json' }
            }).then(r=>r.json()).then(d=>toastr.success('Sitemap generated!')).catch(e=>toastr.error('Error: '+e.message));
        }
    });
}
function clearCache() {
    Swal.fire({
        title: "Clear all application cache?",
        icon: "warning",
        showCancelButton: true,
        confirmButtonText: "Yes, clear!",
        cancelButtonText: "Cancel"
    }).then((result) => {
        if (result.isConfirmed) {
            fetch('/api/v1/admin/clear-cache', { method:'POST',
                headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'), 'Content-Type':'application/json' }
            }).then(r=>r.json()).then(d=>toastr.success('Cache cleared!')).catch(e=>toastr.error('Error: '+e.message));
        }
    });
}
</script>
