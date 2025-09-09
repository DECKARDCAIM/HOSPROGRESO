@extends('layouts.form')

@section('title', 'Iniciar Sesión')
@section('description', 'Ingrese su correo y contraseña para acceder')

@section('content')

    {{-- ALERTA DE ERRORES GENERALES --}}
    @if ($errors->any())
        <div class="alert alert-danger text-white" role="alert">
            <strong>¡Ups! Ha ocurrido un problema:</strong>
            <ul class="mb-0 mt-2">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('login') }}" role="form" class="text-start" novalidate>
        @csrf

        {{-- CORREO ELECTRONICO --}}
        <div class="input-group input-group-outline mb-3 @error('email') is-invalid @enderror">
            <input type="email" class="form-control" id="email" name="email" placeholder="Correo electrónico" value="{{ old('email') }}" required autocomplete="email" aria-describedby="email-error">
            @error('email')
                <div id="email-error" class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        {{-- CONTRASEÑA --}}
        <div class="input-group input-group-outline mb-3 @error('password') is-invalid @enderror">
            <input type="password" class="form-control" id="password" name="password" placeholder="Contraseña" required autocomplete="current-password" aria-describedby="password-error">
            @error('password')
                <div id="password-error" class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        {{-- OLVIDE CONTRASEÑA --}}
        @if (Route::has('password.request'))
            <div class="mb-3 text-end">
                <a href="{{ route('password.request') }}" class="forgot-password text-muted small">
                    ¿Olvidó su contraseña?
                </a>
            </div>
        @endif

        {{-- BOTON DE ENVIAR --}}
        <div class="text-center">
            <button type="submit" class="btn bg-gradient-dark w-100 my-4 mb-2">Iniciar sesión</button>
        </div>

        {{-- REGISTRO DESHABILITADO --}}
        {{-- <p class="mt-4 text-sm text-center">
            ¿No tiene una cuenta?
            <a href="{{ route('register') }}" class="sign-up-link text-decoration-none">Registrarse</a>
        </p> --}}
    </form>
@endsection