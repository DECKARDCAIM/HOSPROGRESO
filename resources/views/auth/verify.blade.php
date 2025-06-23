@extends('layouts.form')

@section('title', 'Verificar Correo Electrónico')
@section('description', 'Verifique su correo electrónico para continuar')

@section('content')

    {{-- ALERTA DE ERRORES GENERALES --}}
    @if (session('resent'))
        <div class="alert alert-success text-white" role="alert">
            Se ha enviado un nuevo enlace de verificación a su dirección de correo electrónico.
        </div>
    @endif

    <div class="text-center mb-4">
        <p>Antes de continuar, por favor revise su correo electrónico para encontrar el enlace de verificación.</p>
        <p>Si no recibió el correo electrónico, puede solicitar otro.</p>
    </div>

    <form method="POST" action="{{ route('verification.resend') }}" role="form" class="text-start">
        @csrf
        <div class="text-center">
            <button type="submit" class="btn bg-gradient-dark w-100 mb-4">
                Solicitar nuevo enlace
            </button>
        </div>
    </form>

    {{-- VOLVER A LOGIN --}}
    <p class="mt-4 text-sm text-center">
        <a href="{{ route('login') }}" class="sign-up-link">
            Volver a iniciar sesión
        </a>
    </p>
@endsection