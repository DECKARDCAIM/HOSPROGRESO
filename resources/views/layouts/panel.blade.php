<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="icon" type="image/png" href="{{ asset('img/logo.png') }}">
    <link rel="apple-touch-icon" href="{{ asset('img/logo.png') }}">
    <title>{{ config('app.name') }} - @yield('title')</title>
    <link href="{{ asset('css/nucleo-icons.css') }}" rel="stylesheet" />
    <link href="{{ asset('css/nucleo-svg.css') }}" rel="stylesheet" />
    <link id="pagestyle" href="{{ asset('css/material-dashboard.css') }}" rel="stylesheet" />
    <link href="{{ asset('bootstrap-icons-1.11.3/font/bootstrap-icons.css') }}" rel="stylesheet" />
    <link href="{{ asset('css/panel.css') }}" rel="stylesheet" />
    <link href="{{ asset('css/search-styles.css') }}" rel="stylesheet" />
    <link href="{{ asset('css/pagination.css') }}" rel="stylesheet" />
    <link href="{{ asset('css/responsive-fixes.css') }}" rel="stylesheet" />
    <link href="{{ asset('css/responsive-datatables.css') }}" rel="stylesheet" />
    <link href="{{ asset('css/datatables-touch-fixes.css') }}" rel="stylesheet" />
    <link href="{{ asset('css/datatables-scroll-fix.css') }}" rel="stylesheet" />
    <link href="{{ asset('css/datatables-buttons-fix.css') }}" rel="stylesheet" />
    <link href="{{ asset('css/checkbox-fixes.css') }}" rel="stylesheet" />
    <link href="{{ asset('css/useroptions-fixes.css') }}" rel="stylesheet" />
    <link href="{{ asset('css/style_card.css') }}" rel="stylesheet" />
    <link href="{{ asset('css/notifications.css') }}" rel="stylesheet" />
</head>

<body class="g-sidenav-show  bg-gray-100">

    <div class="sidenav-overlay" id="sidenav-overlay"></div>
    <aside class="sidenav navbar navbar-vertical navbar-expand-xs border-radius-lg fixed-start ms-2 bg-white my-2"
        id="sidenav-main" style="width: 280px !important; min-width: 280px !important;">
        <div class="sidenav-header">
            <i class="fas fa-times p-3 cursor-pointer text-dark opacity-5 position-absolute end-0 top-0 d-none d-xl-none"
                aria-hidden="true" id="iconSidenav"></i>
            <a class="navbar-brand px-4 py-3 m-0" href="{{ url('/panel') }}">
                <img src="{{ asset('img/logo.png') }}" class="navbar-brand-img" width="26" height="26"
                    alt="main_logo">
                <span class="ms-1 text-sm text-dark">HOSPROGRESO</span>
            </a>
        </div>
        <hr class="horizontal dark mt-0 mb-2">
        <div class="collapse navbar-collapse  w-auto h-auto" id="sidenav-collapse-main">
            <ul class="navbar-nav">
                <li class="nav-item mb-2 mt-0">
                    <a data-bs-toggle="collapse" href="#ProfileNav" class="nav-link text-dark"
                        aria-controls="ProfileNav" role="button" aria-expanded="false">
                        <img src="{{ Auth::user()->profile_photo_url }}" class="avatar">
                        <span class="nav-link-text ms-2 ps-1">Mi Cuenta</span>
                    </a>
                    <div class="collapse" id="ProfileNav" style="">
                        <ul class="nav ">
                            <li class="nav-item">
                                <a class="nav-link text-dark" href="{{ route('profile.index') }}">
                                    <span class="sidenav-mini-icon"></span>
                                    <span class="sidenav-normal  ms-3  ps-1"> Mi Perfil </span>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link text-dark" href="{{ route('logout') }}"
                                    onclick="event.preventDefault(); document.getElementById('formlogout').submit();">
                                    <span class="sidenav-mini-icon"></span>
                                    <span class="sidenav-normal ms-3 ps-1"> Cerrar Sesión </span>
                                </a>
                                <form method="POST" action="{{ route('logout') }}" style="display: none;"
                                    id="formlogout">
                                    @csrf
                                </form>
                            </li>
                        </ul>
                    </div>
                </li>
            </ul>
        </div>
        <div class="collapse navbar-collapse  w-auto h-auto" id="sidenav-collapse-main">
            @include('includes.panel.menu')
        </div>
    </aside>

    <main class="main-content position-relative border-radius-lg d-flex flex-column"
        style="height: 100vh;">
        <nav class="navbar navbar-main navbar-expand-lg px-0 mx-4 shadow-none border-radius-xl" id="navbarBlur"
            data-scroll="true">
            <div class="container-fluid py-1 px-3">
                @include('includes.panel.userOptions')
            </div>
        </nav>
        <div class="container-fluid py-4 flex-grow-1" style="overflow-y: auto;">
            @yield('content')
        </div>
        @include('includes.panel.footer')
    </main>

    @include('includes.panel.globalsearch')
    
    <div class="toast-container position-fixed top-2 end-3 p-3" style="z-index: 1050"></div>

    <script src="{{ asset('js/core/popper.min.js') }}"></script>
    <script src="{{ asset('js/core/bootstrap.min.js') }}"></script>
    <script src="{{ asset('js/plugins/perfect-scrollbar.min.js') }}"></script>
    <script src="{{ asset('js/plugins/smooth-scrollbar.min.js') }}"></script>
    <script src="{{ asset('js/plugins/datatables.js') }}"></script>
    <script src="{{ asset('js/panel.js') }}"></script>
    <script src="{{ asset('js/menu-manager.js') }}"></script>
    <script src="{{ asset('js/global-search.js') }}"></script>
    <script src="{{ asset('js/common-filters.js') }}"></script>
    <script src="{{ asset('js/mobile-sidenav.js') }}"></script>
    <script src="{{ asset('js/responsive-datatables.js') }}"></script>
    <script src="{{ asset('js/datatables-init.js') }}"></script>
    <script src="{{ asset('js/datatables-touch-handler.js') }}"></script>
    <script src="{{ asset('js/plugins/choices.min.js') }}"></script>
    <script src="{{ asset('js/multi-select-init.js') }}"></script>
    <script src="{{ asset('js/material-dashboard.min.js?v=3.1.0') }}"></script>
    <script src="{{ asset('js/toast-notifications.js') }}"></script>
    <script src="{{ asset('js/delete-confirmation.js') }}"></script>
    <script src="{{ asset('js/table-scroll.js') }}"></script>

    @stack('scripts')

    @if (session('toast'))
        <script>
            window.laravelToastData = @json(session('toast'));
        </script>
    @endif

</body>
</html>