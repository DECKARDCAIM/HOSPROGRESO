<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name') }} - @yield('tittle')</title>
    <link href="{{ asset('css/nucleo-icons.css') }}" rel="stylesheet" />
    <link href="{{ asset('css/nucleo-svg.css') }}" rel="stylesheet" />
    <link id="pagestyle" href="{{ asset('css/material-dashboard.css?v=3.1.0') }}" rel="stylesheet" />
    <link href="{{ asset('css/form.css') }}" rel="stylesheet" />
</head>

<body>
    <div class="login-container">
        <div class="row g-0">
            
            @include('includes.form.carrusel')

            <div class="col-lg-5 form-side">
                <div class="login-form">
                    <div class="logo-container mb-3">
                        <img src="{{ asset('img/logo-institucional.jpg') }}" alt="{{ config('app.name') }} Logo"
                            class="img-fluid">
                    </div>

                    <h3 class="text-center">@yield('tittle')</h3>
                    <p class="form-text text-center">@yield('description')</p>

                    @yield('content')

                </div>
            </div>

        </div>
    </div>

    <script src="{{ asset('js/core/popper.min.js') }}"></script>
    <script src="{{ asset('js/core/bootstrap.min.js') }}"></script>
    <script src="{{ asset('js/plugins/perfect-scrollbar.min.js') }}"></script>
    <script src="{{ asset('js/plugins/smooth-scrollbar.min.js') }}"></script>
    <script src="{{ asset('js/material-dashboard.min.js?v=3.1.0') }}"></script>
    <script src="{{ asset('js/form.js') }}"></script>

</body>

</html>