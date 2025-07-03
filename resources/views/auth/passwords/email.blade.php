@extends('layouts.form')

@section('title', 'Restablecer Contraseña')
@section('description', 'Ingrese su correo electrónico para recibir un enlace de restablecimiento')

@section('content')

    {{-- ALERTA DE ERRORES GENERALES --}}
    @if (session('status'))
        <div class="alert alert-success text-white" role="alert">
            {{ session('status') }}
        </div>
    @endif

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

    <form method="POST" action="{{ route('password.email') }}" role="form" class="text-start">
        @csrf

        {{-- CORREO ELECTRONICO --}}
        <div class="input-group input-group-outline mb-3 @error('email') is-invalid @enderror">
            <input id="email" type="email" class="form-control" name="email" value="{{ old('email') }}" placeholder="Correo electrónico" required autocomplete="email" autofocus aria-describedby="email-error">
            @error('email')
                <div id="email-error" class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        {{-- BOTON DE ENVIAR --}}
        <div class="text-center">
            <button type="submit" class="btn bg-gradient-dark w-100 mb-4">
                Enviar enlace de restablecimiento
            </button>
        </div>

        {{-- VOLVER A LOGIN --}}
        <p class="mt-4 text-sm text-center">
            <a href="{{ route('login') }}" class="sign-up-link">
                Volver a iniciar sesión
            </a>
        </p>
    </form>
@endsection