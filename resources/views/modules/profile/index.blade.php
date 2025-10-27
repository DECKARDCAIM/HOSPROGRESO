@extends('layouts.panel')
@section('title', 'Mi Perfil')

@section('content')
    <div class="container-fluid px-2 px-md-4">
        <div class="page-header min-height-300 border-radius-xl mt-4" style="background-image: url('{{ $user->banner_photo_url }}');">
            <span class="mask bg-gradient-dark opacity-4"></span>
        </div>
        <div class="card card-body mx-3 mx-md-4 mt-n6">
            <div class="row gx-4 mb-2">
                <div class="col-auto">
                    <div class="avatar avatar-xl position-relative" style="width: 80px; height: 80px; overflow: hidden; border-radius: 12px;">
                        <img src="{{ $user->profile_photo_url }}" alt="profile_image" class="shadow-sm" style="width: 100%; height: 100%; object-fit: cover;">
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
                <div class="col-lg-4 col-md-6 my-sm-auto ms-sm-auto me-sm-0 mt-3 text-end">
                    <a class="btn btn-sm bg-brand-header me-1" href="{{ route('profile.edit') }}">
                        <i class="bi bi-pencil me-2"></i>Editar Perfil
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="container-fluid py-4">
        <div class="row">
            <div class="col-lg-3">
                <div class="card position-sticky top-1">
                    <ul class="nav flex-column bg-white border-radius-lg p-3">
                        <li class="nav-item">
                            <a class="nav-link text-dark d-flex" data-scroll href="#basic-info">
                                <i class="bi bi-person text-lg me-2"></i>
                                <span class="text-sm">Información Básica</span>
                            </a>
                        </li>
                        <li class="nav-item pt-2">
                            <a class="nav-link text-dark d-flex" data-scroll href="#sessions">
                                <i class="bi bi-pc-display-horizontal text-lg me-2"></i>
                                <span class="text-sm">Sesiones</span>
                            </a>
                        </li>
                    </ul>
                </div>
            </div>

            <div class="col-lg-9 mt-lg-0 mt-4">
                <div class="card card-profile" id="basic-info">
                    <div class="card-header bg-brand-header pb-0 p-3">
                        <div class="row">
                            <div class="d-flex align-items-center">
                                <h6 class="mb-0 text-white">
                                    <i class="bi bi-person me-2"></i>Información del Perfil
                                    <p class="text-sm mb-0"> Bienvenido a tu perfil. Desde aquí puedes ver tus sesiones activas y tus datos personales.</p>
                                </h6>
                                
                            </div>
                        </div>
                    </div>
                    <div class="card-body p-3">
                        <div class="row">
                            <div class="col-md-6">
                                <p class="mb-2"><strong class="text-dark"><i class="bi bi-person me-2"></i>Nombre:</strong><br> {{ $user->name }}</p>
                                <p class="mb-2"><strong class="text-dark"><i class="bi bi-envelope me-2"></i>Email:</strong><br> {{ $user->email }}</p>
                                <p class="mb-2"><strong class="text-dark"><i class="bi bi-card-text me-2"></i>CUI:</strong><br> {{ $user->cui ?? 'No especificado' }}</p>
                                <p class="mb-0"><strong class="text-dark"><i class="bi bi-gender-ambiguous me-2"></i>Género:</strong><br> {{ $user->gender ?? 'No especificado' }}</p>
                            </div>
                            <div class="col-md-6">
                                <p class="mb-2"><strong class="text-dark"><i class="bi bi-telephone me-2"></i>Teléfono:</strong><br> {{ $user->phone ?? 'No especificado' }}</p>
                                <p class="mb-2"><strong class="text-dark"><i class="bi bi-geo-alt me-2"></i>Dirección:</strong><br> {{ $user->address ?? 'No especificado' }}</p>
                                <p class="mb-2"><strong class="text-dark"><i class="bi bi-calendar-event me-2"></i>Fecha de Nacimiento:</strong><br> {{ $user->birth_date ? $user->birth_date->format('d/m/Y') : 'No especificada' }}</p>
                                <p class="mb-0"><strong class="text-dark"><i class="bi bi-shield-check me-2"></i>Estado:</strong><br> 
                                    <span class="badge bg-{{ $user->is_active ? 'success' : 'danger' }}">
                                        {{ $user->is_active ? 'Activo' : 'Inactivo' }}
                                    </span>
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card mt-4" id="sessions">
                    <div class="card-header bg-brand-header pb-3">
                        <h6 class="text-white mb-2">
                            <i class="bi bi-pc-display-horizontal me-2"></i>Sesiones Activas
                            <p class="text-sm mb-0">Esta es una lista de dispositivos que han iniciado sesión en tu cuenta.</p>
                        </h6>
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
                                                    <i class="bi bi-pc-display-horizontal fa-2x me-3 text-secondary"></i>
                                                @else
                                                    <i class="bi bi-phone fa-2x me-3 text-secondary"></i>
                                                @endif
                                            </div>
                                            <div class="flex-grow-1">
                                                <h6 class="mb-1"><i class="bi bi-laptop me-2"></i>{{ $session->agent['platform'] ?: 'Desconocido' }} – {{ $session->agent['browser'] ?: 'Desconocido' }}</h6>
                                                <p class="mb-0 text-sm">
                                                    <i class="bi bi-geo-alt me-1"></i>{{ $session->ip_address }}
                                                    @if ($session->is_current_device)
                                                        <span class="badge bg-gradient-success ms-2"><i class="bi bi-check-circle me-1"></i>Este dispositivo</span>
                                                    @endif
                                                </p>
                                            </div>
                                            <div class="text-sm text-center mx-3">
                                                <div><i class="bi bi-clock me-1"></i>Última vez activo</div>
                                                <strong>{{ $session->last_active }}</strong>
                                            </div>
                                            @if (!$session->is_current_device)
                                                <form method="POST" action="{{ route('profile.logoutSession', $session->id) }}">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-outline-danger mb-0"><i class="bi bi-box-arrow-right me-1"></i>Cerrar Sesión</button>
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