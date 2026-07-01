<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

@include('admin::layouts.head')
<body class="layout-fixed layout-navbar-fixed sidebar-expand-lg sidebar-mini bg-body-tertiary adminlte-shell">

<div class="app-wrapper">

    @include('admin::layouts.header')
    @include('admin::layouts.sidebar')

    <main class="app-main admin-content-scroll" id="adminContentScroll">
        <div class="app-content-header">
            <div class="container-fluid admin-page-container">
                <div class="admin-page-hero">
                    <div class="row g-3 align-items-center">
                        <div class="col-sm-7">
                            <div class="admin-page-kicker">Painel administrativo</div>
                            <h3 class="mb-0 admin-page-title">
                                @yield('page_title', trim($__env->yieldContent('title')) ?: 'Painel administrativo')
                            </h3>
                            @hasSection('page_subtitle')
                                <p class="admin-page-subtitle mb-0">@yield('page_subtitle')</p>
                            @endif
                            @hasSection('breadcrumbs')
                                <div class="admin-page-breadcrumbs">
                                    @yield('breadcrumbs')
                                </div>
                            @endif
                        </div>
                        <div class="col-sm-5">
                            @hasSection('page_actions')
                                <div class="d-flex justify-content-sm-end mt-1 mt-sm-0 admin-page-actions">
                                    @yield('page_actions')
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="app-content">
            <div class="container-fluid admin-page-container">
                @include('core::notification')
                <div class="admin-page-body">
                    @yield('content')
                </div>
            </div>
        </div>
    </main>

    @include('admin::layouts.footer')
</div>

</body>
</html>
