@extends('layouts.form')

@section('title', 'Confirmar Contraseña')
@section('description', 'Por favor, confirme su contraseña antes de continuar')

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

    <form method="POST" action="{{ route('password.confirm') }}" role="form" class="text-start">
        @csrf

        {{-- CONTRASEÑA --}}
        <div class="input-group input-group-outline mb-3 @error('password') is-invalid @enderror">
            <input id="password" type="password" class="form-control" name="password" placeholder="Contraseña" required autocomplete="current-password" aria-describedby="password-error">
            @error('password')
                <div id="password-error" class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        {{-- BOTON DE ENVIAR --}}
        <div class="text-center">
            <button type="submit" class="btn bg-gradient-dark w-100 mb-4">
                Confirmar contraseña
            </button>
        </div>

        {{-- OLVIDE CONTRASEÑA --}}
        <div class="text-center mt-3">
            @if (Route::has('password.request'))
                <a href="{{ route('password.request') }}" class="forgot-password">
                    ¿Olvidó su contraseña?
                </a>
            @endif
        </div>
    </form>
@endsection