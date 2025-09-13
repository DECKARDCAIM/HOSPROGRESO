@extends('layouts.panel')

@section('title', 'Crear Usuario')
@section('breadcrumb', 'Usuarios / Crear')

@section('content')
<div class="container-fluid py-4">
    <div class="row">
        <div class="col-12">
            <div class="card mb-4 border">
                <div class="card-header pb-0 bg-brand-header">
                    <div class="row align-items-center">
                        <div class="col-md-8">
                            <h6 class="text-white mb-0">Nuevo Usuario</h6>
                        </div>
                        <div class="col-md-4 text-end">
                            <a href="{{ route('usuarios.index') }}" class="btn btn-sm btn-white">
                                <i class="bi bi-arrow-left me-2"></i>Regresar
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
                    
                    <form action="{{ route('usuarios.store') }}" method="POST" enctype="multipart/form-data" class="form-horizontal">
                        @csrf
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
                                        value="{{ old('name')}}" 
                                        required>
                                </div>

                                <div class="form-group mb-3">
                                    <label for="cui" class="form-control-label mb-2">
                                        <i class="fas fa-id-card text-info me-2"></i>CUI
                                    </label>
                                    <input type="text" name="cui" id="cui" 
                                        class="form-control form-control-lg border border-2 border-info shadow-sm" 
                                        placeholder="CUI del usuario (13 dígitos)" 
                                        value="{{ old('cui')}}" 
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
                                                value="{{ old('birth_date')}}">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group mb-3">
                                            <label for="gender" class="form-control-label mb-2">
                                                <i class="fas fa-venus-mars text-info me-2"></i>Género
                                            </label>
                                            <select name="gender" id="gender" class="form-select form-select-lg border border-2 border-info shadow-sm">
                                                <option value="">Seleccionar género</option>
                                                <option value="M" {{ old('gender') === 'M' ? 'selected' : '' }}>Masculino</option>
                                                <option value="F" {{ old('gender') === 'F' ? 'selected' : '' }}>Femenino</option>
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
                                        rows="2">{{ old('address')}}</textarea>
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
                                        value="{{ old('email')}}" 
                                        required>
                                </div>

                                <div class="form-group mb-3">
                                    <label for="phone" class="form-control-label mb-2">
                                        <i class="bi bi-telephone text-info me-2"></i>Teléfono
                                    </label>
                                    <input type="text" name="phone" id="phone" 
                                        class="form-control form-control-lg border border-2 border-info shadow-sm" 
                                        placeholder="Número de teléfono" 
                                        value="{{ old('phone')}} "
                                        maxlength="8">
                                </div>

                                <div class="form-group mb-3">
                                    <label for="role_id" class="form-control-label mb-2">
                                        <i class="bi bi-person-tag text-info me-2"></i>Rol del usuario *
                                    </label>
                                    <select name="role_id" id="role_id" class="form-select form-select-lg border border-2 border-info shadow-sm" required>
                                        <option value="">Seleccionar rol</option>
                                        @foreach($roles as $role)
                                            <option value="{{ $role->id }}" {{ old('role_id') == $role->id ? 'selected' : '' }}>
                                                {{ $role->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="form-group mb-3">
                                    <label for="profile_photo" class="form-control-label mb-2">
                                        <i class="bi bi-camera text-info me-2"></i>Foto de perfil
                                    </label>
                                    <input type="file" name="profile_photo" id="profile_photo" 
                                        class="form-control form-control-lg border border-2 border-info shadow-sm"
                                        accept="image/jpeg,image/png,image/jpg">
                                    <div class="form-text text-muted">Formatos permitidos: JPG, PNG. Tamaño máximo: 2MB</div>
                                </div>
                            </div>
                        </div>

                        <!-- Sección de Contraseña -->
                        <div class="row mt-4">
                            <div class="col-12">
                                <h6 class="text-info mb-3"><i class="bi bi-lock me-2"></i>Credenciales de Acceso</h6>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group mb-4">
                                    <label for="password" class="form-control-label mb-2">
                                        <i class="bi bi-key text-info me-2"></i>Contraseña *
                                    </label>
                                    <input type="password" name="password" id="password" 
                                        class="form-control form-control-lg border border-2 border-info shadow-sm" 
                                        placeholder="Contraseña del usuario" 
                                        required>
                                    <div class="form-text text-muted">Mínimo 8 caracteres</div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group mb-4">
                                    <label for="password_confirmation" class="form-control-label mb-2">
                                        <i class="bi bi-key text-info me-2"></i>Confirmar contraseña *
                                    </label>
                                    <input type="password" name="password_confirmation" id="password_confirmation" 
                                        class="form-control form-control-lg border border-2 border-info shadow-sm" 
                                        placeholder="Confirmar contraseña" 
                                        required>
                                </div>
                            </div>
                        </div>

                        <div class="d-flex justify-content-end mt-4">
                            <button type="button" class="btn btn-secondary btn-lg me-2" onclick="window.location.href='{{ route('usuarios.index') }}'">
                                <i class="bi bi-x me-2"></i>Cancelar
                            </button>
                            <button type="submit" class="btn bg-brand-header btn-lg text-white">
                                <i class="bi bi-check-circle me-2"></i>Crear usuario
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const passwordInput = document.getElementById('password');
    const passwordConfirmationInput = document.getElementById('password_confirmation');
    
    // Generar contraseña automáticamente
    const password = generateSecurePassword();
    passwordInput.value = password;
    passwordConfirmationInput.value = password;
    
    function generateSecurePassword() {
        const lowercase = 'abcdefghijklmnopqrstuvwxyz';
        const uppercase = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        const numbers = '0123456789';
        const symbols = '!@#$%^&*()_+-=[]{}|;:,.<>?';
        
        let password = '';
        
        // Asegurar al menos un carácter de cada tipo
        password += lowercase[Math.floor(Math.random() * lowercase.length)];
        password += uppercase[Math.floor(Math.random() * uppercase.length)];
        password += numbers[Math.floor(Math.random() * numbers.length)];
        password += symbols[Math.floor(Math.random() * symbols.length)];
        
        // Completar hasta 12 caracteres con caracteres aleatorios
        const allChars = lowercase + uppercase + numbers + symbols;
        for (let i = 4; i < 12; i++) {
            password += allChars[Math.floor(Math.random() * allChars.length)];
        }
        
        // Mezclar la contraseña
        return password.split('').sort(() => Math.random() - 0.5).join('');
    }
});
</script>
@endsection 