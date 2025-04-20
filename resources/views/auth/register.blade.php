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

        <div class="input-group input-group-outline my-3" id="nameGroup">
            <label class="form-label">Nombre completo</label>
            <input type="text" class="form-control" name="name" id="name" required>
        </div>

        <div class="input-group input-group-outline my-3" id="emailGroup">
            <label class="form-label">Correo electrónico</label>
            <input type="email" class="form-control" name="email" id="email" required>
        </div>

        <div class="input-group input-group-outline my-3" id="passwordGroup">
            <label class="form-label">Contraseña</label>
            <input type="password" class="form-control" name="password" id="password" required minlength="8">
        </div>

        <div class="input-group input-group-outline my-3" id="passwordConfirmationGroup">
            <label class="form-label">Confirmar Contraseña</label>
            <input type="password" class="form-control" name="password_confirmation" id="password_confirmation" required minlength="8">
        </div>

        <div class="text-center">
            <button type="submit" class="btn bg-gradient-dark w-100 my-4 mb-2">Registrarse</button>
        </div>

        <p class="mt-4 text-sm text-center">
            ¿Ya tiene una cuenta?
            <a href="{{ route('login') }}" class="sign-up-link text-decoration-none">Iniciar sesión</a>
        </p>
    </form>
@endsection
