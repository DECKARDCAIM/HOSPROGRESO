@extends('layouts.form')

@section('tittle', 'Iniciar Sesión')
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

        <div class="row">
            {{-- CORREO ELECTRONICO --}}
            <div class="col-12">
                <div class="input-group input-group-outline my-3" id="emailGroup">
                    <label class="form-label">Correo electrónico</label>
                    <input type="email" class="form-control" id="email" name="email" required autocomplete="email">
                </div>
            </div>

            {{-- CONTRASEÑA --}}
            <div class="col-12">
                <div class="input-group input-group-outline my-3" id="passwordGroup">
                    <label class="form-label">Contraseña</label>
                    <input type="password" class="form-control" id="password" name="password" required
                        autocomplete="current-password">
                </div>
            </div>
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

        {{-- CREAR CUENTA --}}
        <p class="mt-4 text-sm text-center">
            ¿No tiene una cuenta?
            <a href="{{ route('register') }}" class="sign-up-link text-decoration-none">Registrarse</a>
        </p>
    </form>
@endsection
