@extends('layouts.panel')

@section('title', 'Mi Perfil')

@push('styles')
<style>
    .page-header {
        background-image: url('{{ asset('img/carrusel/bk1.webp') }}');
        background-size: cover;
        background-position: center;
    }
    .card-profile .card-body {
        padding: 1.5rem;
    }
    .profile-info-list {
        list-style: none;
        padding-left: 0;
    }
    .profile-info-list li {
        display: flex;
        justify-content: space-between;
        padding: 0.75rem 0;
        border-bottom: 1px solid #e9ecef;
    }
    .profile-info-list li:last-child {
        border-bottom: none;
    }
    .profile-info-list .text-dark {
        font-weight: 600;
    }
</style>
@endpush

@section('content')
<!-- Header -->
<div class="container-fluid px-2 px-md-4">
    <div class="page-header min-height-300 border-radius-xl mt-4" style="background-image: url('{{ $user->banner_photo_url }}');">
        <span class="mask bg-gradient-dark opacity-4"></span>
    </div>
    <div class="card card-body mx-3 mx-md-4 mt-n6">
        <div class="row gx-4 mb-2">
            <div class="col-auto">
                <div class="avatar avatar-xl position-relative">
                    <img src="{{ $user->profile_photo_url }}" alt="profile_image" class="w-100 border-radius-lg shadow-sm">
                </div>
            </div>
            <div class="col-auto my-auto">
                <div class="h-100">
                    <h5 class="mb-1">
                        {{ $user->name }}
                    </h5>
                    <p class="mb-0 font-weight-normal text-sm">
                        {{ $user->email }}
                    </p>
                </div>
            </div>
            <div class="col-lg-4 col-md-6 my-sm-auto ms-sm-auto me-sm-0 mx-auto mt-3">
                <div class="nav-wrapper position-relative end-0">
                    <ul class="nav nav-pills nav-fill p-1" role="tablist">
                        <li class="nav-item">
                            <a class="nav-link mb-0 px-0 py-1 active" href="javascript:;">
                                <i class="material-symbols-rounded text-lg position-relative">person</i>
                                <span class="ms-1">Ver Perfil</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link mb-0 px-0 py-1" href="{{ route('profile.edit') }}">
                                <i class="material-symbols-rounded text-lg position-relative">settings</i>
                                <span class="ms-1">Editar Perfil</span>
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Main Content -->
<div class="container-fluid py-4">
    <div class="row">
        <div class="col-lg-3">
            <div class="card position-sticky top-1">
                <ul class="nav flex-column bg-white border-radius-lg p-3">
                    <li class="nav-item">
                        <a class="nav-link text-dark d-flex" data-scroll href="#basic-info">
                            <i class="material-symbols-rounded text-lg me-2">receipt_long</i>
                            <span class="text-sm">Información Básica</span>
                        </a>
                    </li>
                    <li class="nav-item pt-2">
                        <a class="nav-link text-dark d-flex" data-scroll href="#sessions">
                            <i class="material-symbols-rounded text-lg me-2">settings_applications</i>
                            <span class="text-sm">Sesiones</span>
                        </a>
                    </li>
                </ul>
            </div>
        </div>
        <div class="col-lg-9 mt-lg-0 mt-4">
            <!-- Card Basic Info -->
            <div class="card card-profile" id="basic-info">
                <div class="card-header pb-0 p-3">
                    <div class="row">
                        <div class="col-md-8 d-flex align-items-center">
                            <h6 class="mb-0">Información del Perfil</h6>
                        </div>
                    </div>
                </div>
                <div class="card-body p-3">
                    <p class="text-sm">
                        Bienvenido a tu perfil. Desde aquí puedes ver tus sesiones activas y tus datos personales.
                    </p>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <p class="mb-2"><strong class="text-dark">Nombre:</strong><br> {{ $user->name }}</p>
                            <p class="mb-2"><strong class="text-dark">Email:</strong><br> {{ $user->email }}</p>
                            <p class="mb-0"><strong class="text-dark">Género:</strong><br> {{ $user->gender ?? 'No especificado' }}</p>
                        </div>
                        <div class="col-md-6">
                            <p class="mb-2"><strong class="text-dark">Teléfono:</strong><br> {{ $user->phone ?? 'No especificado' }}</p>
                            <p class="mb-2"><strong class="text-dark">Dirección:</strong><br> {{ $user->address ?? 'No especificado' }}</p>
                            <p class="mb-0"><strong class="text-dark">Fecha de Nacimiento:</strong><br> {{ $user->birth_date ? $user->birth_date->format('d/m/Y') : 'No especificada' }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Card Sessions -->
            <div class="card mt-4" id="sessions">
                <div class="card-header pb-3">
                    <h5>Sesiones Activas</h5>
                    <p class="text-sm">Esta es una lista de dispositivos que han iniciado sesión en tu cuenta. Revoca las sesiones que no reconozcas.</p>
                </div>
                <div class="card-body p-3">
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show text-white" role="alert">
                            {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif
                    @if (count($sessions) > 0)
                        <div class="list-group">
                            @foreach ($sessions as $session)
                                <div class="list-group-item list-group-item-action flex-column align-items-start p-3">
                                    <div class="d-flex w-100 justify-content-between align-items-center">
                                        <div>
                                            @if ($session->agent['is_desktop'])
                                                <i class="fas fa-desktop fa-2x me-3 text-secondary"></i>
                                            @else
                                                <i class="fas fa-mobile-alt fa-2x me-3 text-secondary"></i>
                                            @endif
                                        </div>
                                        <div class="flex-grow-1">
                                            <h6 class="mb-1">{{ $session->agent['platform'] ?: 'Desconocido' }} – {{ $session->agent['browser'] ?: 'Desconocido' }}</h6>
                                            <p class="mb-0 text-sm">
                                                {{ $session->ip_address }}
                                                @if ($session->is_current_device)
                                                    <span class="badge bg-gradient-success ms-2">Este dispositivo</span>
                                                @endif
                                            </p>
                                        </div>
                                        <div class="text-sm text-center mx-3">
                                            <div>Última vez activo</div>
                                            <strong>{{ $session->last_active }}</strong>
                                        </div>
                                        @if (!$session->is_current_device)
                                            <form method="POST" action="{{ route('profile.logoutSession', $session->id) }}">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-outline-danger mb-0">Cerrar Sesión</button>
                                            </form>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
