<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

@include('admin::layouts.head')
<body class="layout-fixed sidebar-expand-lg sidebar-mini bg-body-tertiary adminlte-shell">

<div class="app-wrapper">

    @include('admin::layouts.header')
    @include('admin::layouts.sidebar')

    <main class="app-main">
        <div class="app-content-header">
            <div class="container-fluid">
                <div class="row align-items-center">
                    <div class="col-sm-6">
                        <h3 class="mb-0 admin-page-title">
                            @yield('page_title', trim($__env->yieldContent('title')) ?: 'Painel administrativo')
                        </h3>
                    </div>
                    <div class="col-sm-6">
                        @hasSection('page_actions')
                            <div class="d-flex justify-content-sm-end mt-3 mt-sm-0">
                                @yield('page_actions')
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <div class="app-content">
            <div class="container-fluid">
            @include('core::notification')
            @yield('content')
            </div>
        </div>
    </main>

    @include('admin::layouts.footer')
</div>

</body>
</html>
