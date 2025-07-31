@extends('layouts.panel')

@section('title', 'Editar Usuario')
@section('breadcrumb', 'Usuarios / Editar')

@section('content')
<div class="container-fluid py-4">
    <div class="row">
        <div class="col-12">
            <div class="card mb-4 border">
                <div class="card-header pb-0 bg-gradient-info">
                    <div class="row align-items-center">
                        <div class="col-md-8">
                            <h6 class="text-white mb-0">Editar Usuario: {{ $user->name }}</h6>
                        </div>
                        <div class="col-md-4 text-end">
                            <a href="{{ route('usuarios.index') }}" class="btn btn-sm btn-white">
                                <i class="bi bi-chevron-left me-2"></i>Regresar
                            </a>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    @if ($errors->any())
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <span class="alert-icon"><i class="bi bi-exclamation-triangle"></i></span>
                        <span class="alert-text">
                            <strong>Por favor!</strong> Revisa los siguientes errores:
                            <ul class="mb-0">
                                @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </span>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                    @endif
                    
                    <!-- Foto de perfil actual -->
                    <div class="row mb-4">
                        <div class="col-12 text-center">
                            <div class="avatar avatar-xl">
                                <img src="{{ $user->profile_photo_url }}" alt="profile" class="avatar-img rounded-circle border border-info" style="width: 100px; height: 100px;">
                            </div>
                            <p class="text-sm text-muted mt-2">Foto de perfil actual</p>
                        </div>
                    </div>

                    <form action="{{ route('usuarios.update', $user->id) }}" method="POST" enctype="multipart/form-data" class="form-horizontal">
                        @csrf
                        @method('PUT')
                        <div class="row">
                            <!-- Información Personal -->
                            <div class="col-md-6">
                                <h6 class="text-info mb-3"><i class="bi bi-person me-2"></i>Información Personal</h6>
                                
                                <div class="form-group mb-3">
                                    <label for="name" class="form-control-label mb-2">
                                        <i class="bi bi-person text-info me-2"></i>Nombre completo *
                                    </label>
                                    <input type="text" name="name" id="name" 
                                        class="form-control form-control-lg border border-2 border-info shadow-sm" 
                                        placeholder="Nombre completo del usuario" 
                                        value="{{ old('name', $user->name)}}" 
                                        required>
                                </div>

                                <div class="form-group mb-3">
                                    <label for="cui" class="form-control-label mb-2">
                                        <i class="bi bi-card-text text-info me-2"></i>CUI
                                    </label>
                                    <input type="text" name="cui" id="cui" 
                                        class="form-control form-control-lg border border-2 border-info shadow-sm" 
                                        placeholder="CUI del usuario (13 dígitos)" 
                                        value="{{ old('cui', $user->cui)}}" 
                                        maxlength="13">
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group mb-3">
                                            <label for="birth_date" class="form-control-label mb-2">
                                                <i class="bi bi-calendar text-info me-2"></i>Fecha de nacimiento
                                            </label>
                                            <input type="date" name="birth_date" id="birth_date" 
                                                class="form-control form-control-lg border border-2 border-info shadow-sm" 
                                                value="{{ old('birth_date', $user->birth_date ? $user->birth_date->format('Y-m-d') : '')}}">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group mb-3">
                                            <label for="gender" class="form-control-label mb-2">
                                                <i class="bi bi-gender-ambiguous text-info me-2"></i>Género
                                            </label>
                                            <select name="gender" id="gender" class="form-select form-select-lg border border-2 border-info shadow-sm">
                                                <option value="">Seleccionar género</option>
                                                <option value="M" {{ old('gender', $user->gender) === 'M' ? 'selected' : '' }}>Masculino</option>
                                                <option value="F" {{ old('gender', $user->gender) === 'F' ? 'selected' : '' }}>Femenino</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group mb-3">
                                    <label for="address" class="form-control-label mb-2">
                                        <i class="bi bi-geo-alt text-info me-2"></i>Dirección
                                    </label>
                                    <textarea name="address" id="address" 
                                        class="form-control form-control-lg border border-2 border-info shadow-sm" 
                                        placeholder="Dirección completa" 
                                        rows="2">{{ old('address', $user->address)}}</textarea>
                                </div>
                            </div>

                            <!-- Información de Contacto y Sistema -->
                            <div class="col-md-6">
                                <h6 class="text-info mb-3"><i class="bi bi-gear me-2"></i>Información de Sistema</h6>
                                
                                <div class="form-group mb-3">
                                    <label for="email" class="form-control-label mb-2">
                                        <i class="bi bi-envelope text-info me-2"></i>Correo electrónico *
                                    </label>
                                    <input type="email" name="email" id="email" 
                                        class="form-control form-control-lg border border-2 border-info shadow-sm" 
                                        placeholder="correo@ejemplo.com" 
                                        value="{{ old('email', $user->email)}}" 
                                        required>
                                </div>

                                <div class="form-group mb-3">
                                    <label for="phone" class="form-control-label mb-2">
                                        <i class="bi bi-telephone text-info me-2"></i>Teléfono
                                    </label>
                                    <input type="text" name="phone" id="phone" 
                                        class="form-control form-control-lg border border-2 border-info shadow-sm" 
                                        placeholder="Número de teléfono" 
                                        value="{{ old('phone', $user->phone)}}">
                                </div>

                                <div class="form-group mb-3">
                                    <label for="role_id" class="form-control-label mb-2">
                                        <i class="bi bi-person-badge text-info me-2"></i>Rol del usuario *
                                    </label>
                                    <select name="role_id" id="role_id" class="form-select form-select-lg border border-2 border-info shadow-sm" required>
                                        <option value="">Seleccionar rol</option>
                                        @foreach($roles as $role)
                                            <option value="{{ $role->id }}" {{ old('role_id', $user->role_id) == $role->id ? 'selected' : '' }}>
                                                {{ $role->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="form-group mb-3">
                                    <label for="profile_photo" class="form-control-label mb-2">
                                        <i class="bi bi-camera text-info me-2"></i>Nueva foto de perfil
                                    </label>
                                    <input type="file" name="profile_photo" id="profile_photo" 
                                        class="form-control form-control-lg border border-2 border-info shadow-sm"
                                        accept="image/jpeg,image/png,image/jpg">
                                    <div class="form-text text-muted">Formatos permitidos: JPG, PNG. Tamaño máximo: 2MB. Dejar vacío para mantener la actual.</div>
                                </div>
                            </div>
                        </div>

                        <div class="d-flex justify-content-end mt-4">
                            <button type="button" class="btn btn-secondary btn-lg me-2" onclick="window.location.href='{{ route('usuarios.index') }}'">
                                <i class="bi bi-x me-2"></i>Cancelar
                            </button>
                            <button type="submit" class="btn bg-gradient-info btn-lg text-white">
                                <i class="bi bi-save me-2"></i>Guardar cambios
                            </button>
                        </div>
                    </form>

                    <!-- Sección de restablecimiento de contraseña -->
                    <div class="card mt-4 border border-info">
                        <div class="card-header bg-info">
                            <h6 class="text-white mb-0"><i class="bi bi-lock me-2"></i>Restablecer Contraseña</h6>
                        </div>
                        <div class="card-body">
                            <form action="{{ route('usuarios.reset-password', $user->id) }}" method="POST" id="reset-password-form">
                                @csrf
                                @method('PUT')
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group mb-3">
                                            <label for="password" class="form-control-label mb-2">
                                                <i class="bi bi-key text-info me-2"></i>Nueva contraseña
                                            </label>
                                            <input type="password" name="password" id="password" 
                                                class="form-control form-control-lg border border-2 border-info shadow-sm" 
                                                placeholder="Nueva contraseña">
                                            <div class="form-text text-muted">Mínimo 8 caracteres</div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group mb-3">
                                            <label for="password_confirmation" class="form-control-label mb-2">
                                                <i class="bi bi-key text-info me-2"></i>Confirmar nueva contraseña
                                            </label>
                                            <input type="password" name="password_confirmation" id="password_confirmation" 
                                                class="form-control form-control-lg border border-2 border-info shadow-sm" 
                                                placeholder="Confirmar nueva contraseña">
                                        </div>
                                    </div>
                                </div>
                                <div class="text-end">
                                    <button type="submit" class="btn bg-gradient-info btn-lg text-white">
                                        <i class="bi bi-arrow-clockwise me-2"></i>Restablecer contraseña
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>


@endsection 