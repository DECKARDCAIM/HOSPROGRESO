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

<body><form method="POST" action="{{ route('logout') }}">
    @csrf
    <button type="submit" class="btn btn-danger btn-sm">
        <i class="material-icons"></i> Cerrar sesión
    </button>
</form>

<script src="{{ asset('js/core/popper.min.js') }}"></script>
<script src="{{ asset('js/core/bootstrap.min.js') }}"></script>
<script src="{{ asset('js/plugins/perfect-scrollbar.min.js') }}"></script>
<script src="{{ asset('js/plugins/smooth-scrollbar.min.js') }}"></script>
<script src="{{ asset('js/material-dashboard.min.js?v=3.1.0') }}"></script>

</body>

</html>