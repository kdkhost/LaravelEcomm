<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

@include('admin::layouts.head')
<body class="hold-transition sidebar-mini layout-fixed layout-navbar-fixed layout-footer-fixed adminlte-shell">

<div class="wrapper">

    @include('admin::layouts.sidebar')
    <div class="content-wrapper shell-content-wrapper">
        @include('admin::layouts.header')

        <section class="content pt-3">
            @include('core::notification')
            @yield('content')
        </section>
    </div>

    @include('admin::layouts.footer')
</div>

</body>
</html>
