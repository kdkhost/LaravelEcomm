<head>

    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="Zoran Bogoevski">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>E-SHOP || DASHBOARD</title>
    <!-- Custom fonts for this template-->
    <link href="{{asset('backend/vendor/fontawesome-free/css/all.min.css')}}" rel="stylesheet" type="text/css">
    <link href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,
    900,900i" rel="stylesheet">
    <link href="https://use.fontawesome.com/releases/v5.15.4/css/all.css" rel="stylesheet">
    <!-- Custom styles for this template-->
    <link href="{{asset('backend/css/all.min.css')}}" rel="stylesheet">
    <link href="{{asset('backend/css/custom.css')}}" rel="stylesheet">
    <link href="{{asset('backend/css/pagination.css')}}" rel="stylesheet">
    <link href="{{asset('backend/css/sweetalert2.min.css')}}" rel="stylesheet">
    <link href="{{asset('backend/css/toastr.min.css')}}" rel="stylesheet">
    @stack('styles')

    @php
        $themeColors = [];
        try {
            $themeColors = app('settings')->theme_settings ?? [];
        } catch (\Exception $e) {}
        $primary = $themeColors['primary_color'] ?? '#4e73df';
        $sidebarBg = $themeColors['sidebar_bg'] ?? '#4e73df';
        $sidebarText = $themeColors['sidebar_text'] ?? '#ffffff';
        $accent = $themeColors['accent_color'] ?? '#F7941D';
        $headerBg = $themeColors['header_bg'] ?? '#ffffff';
    @endphp
    <style>
        :root {
            --primary-color: {{ $primary }};
            --sidebar-bg: {{ $sidebarBg }};
            --sidebar-text: {{ $sidebarText }};
            --accent-color: {{ $accent }};
            --header-bg: {{ $headerBg }};
        }
        .bg-gradient-primary { background: linear-gradient(180deg, var(--sidebar-bg) 0%, {{ $sidebarBg }}cc 100%) !important; }
        .sidebar-dark .nav-item .nav-link { color: var(--sidebar-text) !important; }
        .sidebar-dark .nav-item .nav-link i { color: var(--sidebar-text) !important; }
        .btn-primary { background-color: var(--primary-color) !important; border-color: var(--primary-color) !important; }
        a { color: var(--primary-color); }
        .topbar { background: var(--header-bg) !important; }
    </style>

</head>
