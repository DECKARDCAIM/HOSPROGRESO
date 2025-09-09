@extends('layouts.panel')

@section('title', 'Detalles del Usuario')
@section('breadcrumb', 'Usuarios / Detalles')

@section('content')
<div class="container-fluid py-4">
    <div class="row">
        <div class="col-12">
            <div class="card mb-4 border">
                <div class="card-header pb-0 bg-brand-header">
                    <div class="row align-items-center">
                        <div class="col-md-8">
                            <h6 class="text-white mb-0">Detalles del Usuario</h6>
                        </div>
                        <div class="col-md-4 text-end">
                            <a href="{{ route('usuarios.edit', $user->id) }}" class="btn btn-sm btn-white me-2">
                                <i class="bi bi-pencil me-2"></i>Editar
                            </a>
                            <a href="{{ route('usuarios.index') }}" class="btn btn-sm btn-white">
                                <i class="bi bi-chevron-left me-2"></i>Regresar
                            </a>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <!-- Foto y información básica -->
                        <div class="col-md-4 text-center">
                            <div class="avatar avatar-xxl mb-3">
                                <img src="{{ $user->profile_photo_url }}" alt="profile" class="avatar-img rounded-circle border border-info" style="width: 150px; height: 150px;">
                            </div>
                            <h5 class="mb-1">{{ $user->name }}</h5>
                            <p class="text-sm text-muted mb-2">{{ $user->email }}</p>
                            @if($user->role)
                                <span class="badge bg-gradient-primary p-2">{{ $user->role->name }}</span>
                            @else
                                <span class="badge bg-secondary p-2">Sin rol asignado</span>
                            @endif
                            
                            <div class="mt-3">
                                @if($user->is_active)
                                    @if($user->canAccess())
                                        <span class="badge bg-success p-2">
                                            <i class="bi bi-check-circle me-1"></i>Usuario Activo
                                        </span>
                                    @else
                                        <span class="badge bg-warning p-2">
                                            <i class="bi bi-exclamation-triangle me-1"></i>Activo (Sin acceso)
                                        </span>
                                    @endif
                                @else
                                    <span class="badge bg-secondary p-2">
                                        <i class="bi bi-x-circle me-1"></i>Usuario Inactivo
                                    </span>
                                @endif
                            </div>
                        </div>

                        <!-- Información detallada -->
                        <div class="col-md-8">
                            <div class="row">
                                <!-- Información Personal -->
                                <div class="col-12 mb-4">
                                    <h6 class="text-info border-bottom pb-2 mb-3">
                                        <i class="bi bi-person me-2"></i>Información Personal
                                    </h6>
                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label class="text-sm font-weight-bold text-secondary">Nombre completo:</label>
                                            <p class="text-sm mb-0">{{ $user->name }}</p>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label class="text-sm font-weight-bold text-secondary">CUI:</label>
                                            <p class="text-sm mb-0">{{ $user->cui ?? 'No registrado' }}</p>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label class="text-sm font-weight-bold text-secondary">Fecha de nacimiento:</label>
                                            <p class="text-sm mb-0">
                                                @if($user->birth_date)
                                                    {{ $user->birth_date->format('d/m/Y') }}
                                                    <span class="text-muted">({{ $user->birth_date->age }} años)</span>
                                                @else
                                                    No registrada
                                                @endif
                                            </p>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label class="text-sm font-weight-bold text-secondary">Género:</label>
                                            <p class="text-sm mb-0">
                                                @if($user->gender === 'M')
                                                    <i class="bi bi-gender-male text-secondary me-1"></i>Masculino
                                                @elseif($user->gender === 'F')
                                                    <i class="bi bi-gender-female text-pink me-1"></i>Femenino
                                                @else
                                                    No especificado
                                                @endif
                                            </p>
                                        </div>
                                        <div class="col-12 mb-3">
                                            <label class="text-sm font-weight-bold text-secondary">Dirección:</label>
                                            <p class="text-sm mb-0">{{ $user->address ?? 'No registrada' }}</p>
                                        </div>
                                    </div>
                                </div>

                                <!-- Información de Contacto -->
                                <div class="col-12 mb-4">
                                    <h6 class="text-info border-bottom pb-2 mb-3">
                                        <i class="bi bi-telephone-book me-2"></i>Información de Contacto
                                    </h6>
                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label class="text-sm font-weight-bold text-secondary">Correo electrónico:</label>
                                            <p class="text-sm mb-0">
                                                <i class="bi bi-envelope text-info me-1"></i>
                                                {{ $user->email }}
                                            </p>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label class="text-sm font-weight-bold text-secondary">Teléfono:</label>
                                            <p class="text-sm mb-0">
                                                @if($user->phone)
                                                    <i class="bi bi-telephone text-info me-1"></i>
                                                    {{ $user->phone }}
                                                @else
                                                    No registrado
                                                @endif
                                            </p>
                                        </div>
                                    </div>
                                </div>

                                <!-- Información del Sistema -->
                                <div class="col-12 mb-4">
                                    <h6 class="text-info border-bottom pb-2 mb-3">
                                        <i class="bi bi-gear me-2"></i>Información del Sistema
                                    </h6>
                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label class="text-sm font-weight-bold text-secondary">Rol asignado:</label>
                                            <p class="text-sm mb-0">
                                                @if($user->role)
                                                    <span class="badge bg-gradient-primary">{{ $user->role->name }}</span>
                                                @else
                                                    <span class="badge bg-secondary">Sin rol asignado</span>
                                                @endif
                                            </p>
                                            @if($user->role && $user->role->description)
                                                <p class="text-xs text-muted mb-0 mt-1">{{ $user->role->description }}</p>
                                            @endif
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label class="text-sm font-weight-bold text-secondary">Estado del usuario:</label>
                                            <p class="text-sm mb-0">
                                                @if($user->is_active)
                                                    @if($user->canAccess())
                                                        <span class="badge bg-success">Activo - Puede acceder</span>
                                                    @else
                                                        <span class="badge bg-warning">Activo - Sin acceso</span>
                                                        <br><small class="text-muted">Rol inactivo o no asignado</small>
                                                    @endif
                                                @else
                                                    <span class="badge bg-secondary">Inactivo</span>
                                                @endif
                                            </p>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label class="text-sm font-weight-bold text-secondary">Fecha de registro:</label>
                                            <p class="text-sm mb-0">
                                                <i class="bi bi-calendar text-info me-1"></i>
                                                {{ $user->created_at->format('d/m/Y H:i') }}
                                            </p>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label class="text-sm font-weight-bold text-secondary">Última actualización:</label>
                                            <p class="text-sm mb-0">
                                                <i class="bi bi-clock text-info me-1"></i>
                                                {{ $user->updated_at->format('d/m/Y H:i') }}
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Acciones adicionales -->
                    <div class="row mt-4">
                        <div class="col-12">
                            <div class="card border border-light">
                                <div class="card-header bg-light">
                                    <h6 class="mb-0"><i class="bi bi-tools me-2"></i>Acciones Administrativas</h6>
                                </div>
                                <div class="card-body">
                                    <div class="d-flex flex-wrap gap-2">
                                        <a href="{{ route('usuarios.edit', $user->id) }}" class="btn bg-brand-header">
                                            <i class="bi bi-pencil me-1"></i>Editar información
                                        </a>
                                        
                                        @if($user->is_active)
                                            @if($user->id !== auth()->id())
                                                <form action="{{ route('usuarios.destroy', $user->id) }}" method="POST" class="d-inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-warning">
                                                        <i class="bi bi-person-x me-1"></i>Desactivar usuario
                                                    </button>
                                                </form>
                                            @endif
                                        @else
                                            <form action="{{ route('usuarios.reactivate', $user->id) }}" method="POST" class="d-inline">
                                                @csrf
                                                <button type="submit" class="btn btn-success">
                                                    <i class="bi bi-person-check me-1"></i>Reactivar usuario
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 