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
    <link rel="stylesheet" type="text/css"
        href="https://fonts.googleapis.com/css?family=Inter:300,400,500,600,700,900" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.1/css/all.min.css"
        integrity="sha512-5Hs3dF2AEPkpNAR7UiOHba+lRSJNeM2ECkwxUIxC1Q/FLycGTbNapWXB4tP889k5T5Ju8fs4b1P5z/iB4nMfSQ=="
        crossorigin="anonymous" referrerpolicy="no-referrer">
    <link rel="stylesheet"
        href="https://fonts.googleapis.com/css2?family=Material+Symbols+Rounded:opsz,wght,FILL,GRAD@24,400,0,0" />
    <link href="{{ asset('css/notifications.css') }}" rel="stylesheet" />
</head>

<body class="g-sidenav-show  bg-gray-100">

    <aside class="sidenav navbar navbar-vertical navbar-expand-xs border-radius-lg fixed-start ms-2  bg-white my-2"
        id="sidenav-main">
        <div class="sidenav-header">
            <i class="fas fa-times p-3 cursor-pointer text-dark opacity-5 position-absolute end-0 top-0 d-none d-xl-none"
                aria-hidden="true" id="iconSidenav"></i>
            <a class="navbar-brand px-4 py-3 m-0" href="{{ url('/panel') }}">
                <img src="{{ asset('img/logo.png')}}" class="navbar-brand-img" width="26" height="26"
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
                                <a class="nav-link text-dark" href="{{ route('logout') }}" onclick="event.preventDefault(); document.getElementById('formlogout').submit();">
                                    <span class="sidenav-mini-icon"></span>
                                    <span class="sidenav-normal ms-3 ps-1"> Cerrar Sesión </span>
                                </a>
                                <form method="POST" action="{{ route('logout') }}" style="display: none;" id="formlogout">
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

    <main class="main-content position-relative border-radius-lg d-flex flex-column" style="height: 100vh;">
        <!-- Navbar -->
        @if (!request()->routeIs('profile.index') && !request()->routeIs('profile.edit'))
            <nav class="navbar navbar-main navbar-expand-lg px-0 mx-4 shadow-none border-radius-xl" id="navbarBlur" data-scroll="true">
                <div class="container-fluid py-1 px-3">
                    @include('includes.panel.userOptions')
                </div>
            </nav>
        @endif
        <!-- End Navbar -->

        <div class="container-fluid py-4 flex-grow-1" style="overflow-y: auto;">
            @yield('content')
        </div>
        
        @include('includes.panel.footer')
    </main>

    <div class="toast-container position-fixed top-2 end-3 p-3" style="z-index: 1050">
        <!-- Toast elements will be injected here -->
    </div>

    {{-- Core JS Files --}}
    <script src="{{ asset('js/core/popper.min.js')}}"></script>
    <script src="{{ asset('js/core/bootstrap.min.js')}}"></script>
    <script src="{{ asset('js/plugins/perfect-scrollbar.min.js')}}"></script>
    <script src="{{ asset('js/plugins/smooth-scrollbar.min.js')}}"></script>
    
    {{-- Panel JS --}}
    <script src="{{ asset('js/panel.js') }}"></script>
    
    {{-- Material Dashboard JS --}}
    <script src="{{ asset('js/material-dashboard.min.js?v=3.1.0')}}"></script>
    
    @stack('scripts')

    <script>
        // Función global para mostrar toast notifications
        function showToast(type, title, message, duration = 4000) {
            const toastContainer = document.querySelector('.toast-container');
            if (!toastContainer) return;

            const icons = {
                success: 'check_circle',
                error: 'error',
                info: 'info',
                warning: 'warning'
            };

            const colors = {
                success: 'success',
                error: 'danger',
                info: 'info',
                warning: 'warning'
            };

            const toastId = 'toast-' + Date.now();
            const toastHTML = `
                <div id="${toastId}" class="toast" role="alert" aria-live="assertive" aria-atomic="true" data-bs-delay="${duration}">
                    <div class="toast-header bg-${colors[type] ?? 'info'} text-white">
                        <i class="material-symbols-rounded me-2">${icons[type] ?? 'info'}</i>
                        <strong class="me-auto">${title}</strong>
                        <small>Ahora</small>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="toast" aria-label="Close"></button>
                    </div>
                    <div class="toast-body bg-white text-dark">
                        ${message}
                    </div>
                </div>
            `;

            toastContainer.insertAdjacentHTML('beforeend', toastHTML);
            const toastElement = document.getElementById(toastId);
            const toast = new bootstrap.Toast(toastElement);
            toast.show();

            toastElement.addEventListener('hidden.bs.toast', function () {
                toastElement.remove();
            });

            return toast;
        }

        // Función específica para notificaciones de éxito
        function showSuccessToast(message, title = 'Éxito') {
            return showToast('success', title, message);
        }

        // Función específica para notificaciones de error
        function showErrorToast(message, title = 'Error') {
            return showToast('error', title, message);
        }

        // Función específica para notificaciones de información
        function showInfoToast(message, title = 'Información') {
            return showToast('info', title, message);
        }
    </script>

    @if (session('toast'))
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const toastData = @json(session('toast'));
            showToast(
                toastData.type ?? 'info',
                toastData.title ?? 'Notificación',
                toastData.message ?? '',
                5000
            );
        });
    </script>
    @endif


</body>

</html>
