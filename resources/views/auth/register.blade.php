@extends('layouts.form')

@section('tittle', 'Registrarse')
@section('description', 'Cree una cuenta nueva para acceder al sistema')

@section('content')

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

    <form method="POST" action="{{ route('register') }}" role="form" class="text-start">
        @csrf

        {{-- NOMBRE COMPLETO --}}
        <div class="input-group input-group-outline mb-3 @error('name') is-invalid @enderror">
            <input type="text" class="form-control" name="name" id="name" placeholder="Nombre completo" value="{{ old('name') }}" required>
        </div>

        {{-- CORREO ELECTRONICO --}}
        <div class="input-group input-group-outline mb-3 @error('email') is-invalid @enderror">
            <input type="email" class="form-control" name="email" id="email" placeholder="Correo electrónico" value="{{ old('email') }}" required>
        </div>

        {{-- CONTRASEÑA --}}
        <div class="input-group input-group-outline mb-3 @error('password') is-invalid @enderror">
            <input type="password" class="form-control" name="password" id="password" placeholder="Contraseña" required minlength="8">
        </div>

        {{-- CONFIRMAR CONTRASEÑA --}}
        <div class="input-group input-group-outline mb-3">
            <input type="password" class="form-control" name="password_confirmation" id="password-confirmation" placeholder="Confirmar Contraseña" required minlength="8">
        </div>

        {{-- BOTON DE ENVIAR --}}
        <div class="text-center">
            <button type="submit" class="btn bg-gradient-dark w-100 mb-4">Registrarse</button>
        </div>

        {{-- YA TIENE CUENTA --}}
        <p class="mt-4 text-sm text-center">
            ¿Ya tiene una cuenta?
            <a href="{{ route('login') }}" class="sign-up-link">Iniciar sesión</a>
        </p>
    </form>
@endsection