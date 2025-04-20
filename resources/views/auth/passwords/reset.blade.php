@extends('layouts.form')

@section('tittle', 'Restablecer Contraseña')
@section('description', 'Ingrese su nueva contraseña para continuar')

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

    <form method="POST" action="{{ route('password.update') }}" role="form" class="text-start">
        @csrf

        <input type="hidden" name="token" value="{{ $token }}">

        {{-- CORREO ELECTRONICO --}}
        <div class="input-group input-group-outline mb-3 @error('email') is-invalid @enderror">
            <input id="email" type="email" class="form-control" name="email" value="{{ $email ?? old('email') }}" placeholder="Correo electrónico" required autocomplete="email" autofocus readonly>
        </div>

        {{-- CONTRASEÑA --}}
        <div class="input-group input-group-outline mb-3 @error('password') is-invalid @enderror">
            <input id="password" type="password" class="form-control" name="password" placeholder="Nueva contraseña" required autocomplete="new-password">
        </div>

        {{-- CONFIRMAR CONTRASEÑA --}}
        <div class="input-group input-group-outline mb-3">
            <input id="password-confirmation" type="password" class="form-control" name="password_confirmation" placeholder="Confirmar contraseña" required autocomplete="new-password">
        </div>

        {{-- BOTON DE ENVIAR --}}
        <div class="text-center">
            <button type="submit" class="btn bg-gradient-dark w-100 mb-4">
                Restablecer contraseña
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