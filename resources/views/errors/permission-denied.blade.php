@extends('layouts.panel')

@section('title', 'Acceso Denegado')

@section('content')
<div class="container-fluid d-flex justify-content-center align-items-center" style="min-height: 60vh;">
    <div class="text-center">
        
        <!-- Icono principal -->
        <div class="mb-4">
            <i class="fas fa-lock text-danger" style="font-size: 120px; opacity: 0.8;"></i>
        </div>

        <!-- Título principal -->
        <h1 class="display-4 text-danger mb-3">
            🚫 Acceso Denegado
        </h1>

        <!-- Mensaje descriptivo -->
        <div class="card mx-auto mb-4" style="max-width: 600px;">
            <div class="card-body">
                <p class="card-text fs-5 text-muted mb-3">
                    No tienes los permisos necesarios para acceder a esta página.
                </p>
                <p class="text-muted mb-0">
                    Si consideras que esto es un error, contacta con el administrador del sistema para revisar tus permisos de acceso.
                </p>
            </div>
        </div>

        <!-- Información adicional -->
        <div class="alert alert-warning mx-auto" role="alert" style="max-width: 500px;">
            <i class="fas fa-info-circle"></i>
            <strong>Usuario:</strong> {{ auth()->user()->name ?? 'Desconocido' }}<br>
            <strong>Rol:</strong> {{ auth()->user()->getRoleName() ?? 'Sin rol' }}<br>
            <strong>Fecha:</strong> {{ now()->format('d/m/Y H:i:s') }}
        </div>

        <!-- Botones de acción -->
        <div class="d-flex gap-3 justify-content-center flex-wrap">
            <a href="{{ url()->previous() }}" class="btn btn-secondary btn-lg">
                <i class="fas fa-arrow-left"></i> Volver Atrás
            </a>
            
            <a href="{{ route('home') }}" class="btn btn-primary btn-lg">
                <i class="fas fa-home"></i> Ir al Inicio
            </a>

            @if(auth()->user()->isAdmin())
            <a href="{{ route('roles.index') }}" class="btn btn-outline-info btn-lg">
                <i class="fas fa-users-cog"></i> Gestionar Roles
            </a>
            @endif
        </div>

        <!-- Footer informativo -->
        <div class="mt-5">
            <small class="text-muted">
                <i class="fas fa-shield-alt"></i>
                Este sistema está protegido por permisos de seguridad
            </small>
        </div>

    </div>
</div>
@endsection

@push('styles')
<style>
.container-fluid {
    background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
    min-height: 100vh;
}

.btn-lg {
    padding: 12px 30px;
    font-weight: 600;
}

.card {
    border: none;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
}

.alert {
    border: none;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
}

@media (max-width: 768px) {
    .display-4 {
        font-size: 2.5rem;
    }
    
    .fas.fa-lock {
        font-size: 80px !important;
    }
    
    .btn-lg {
        padding: 10px 20px;
        font-size: 0.9rem;
    }
}
</style>
@endpush