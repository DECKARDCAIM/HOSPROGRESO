@extends('layouts.panel')

@section('title', 'Editar Perfil')

@section('content')
@push('styles')
<style>
    .page-header {
        background-image: url('{{ asset('img/carrusel/bk1.webp') }}');
        background-size: cover;
        background-position: center;
    }

    .form-control-static {
        padding-top: .5rem;
        padding-bottom: .5rem;
        min-height: calc(1.5em + 1rem + 2px);
    }
    .form-control, .form-select {
        border: 1px solid #d2d6da !important;
    }
    .form-control:focus, .form-select:focus {
        border-color: #5e72e4 !important;
        box-shadow: 0 0 0 2px rgba(94, 114, 228, 0.25) !important;
    }

    .card-body .form-control, .card-body .form-select {
        border: 1px solid #d2d6da !important;
        background-color: #fff !important;
        padding: 0.5rem 0.75rem !important;
    }
    .card-body .form-control:focus, .card-body .form-select:focus {
        border-color: #5e72e4 !important;
        box-shadow: 0 0 0 2px rgba(94, 114, 228, 0.25) !important;
    }

    /* Estilos para las nuevas secciones de fotos */
                    .card-header.bg-brand-header {
        background: linear-gradient(135deg, #1e88e5 0%, #1976d2 100%) !important;
    }

    .form-control.border-2 {
        border-width: 2px !important;
        transition: all 0.3s ease;
    }

    .form-control.border-2:focus {
        border-color: #1e88e5 !important;
        box-shadow: 0 0 0 0.2rem rgba(30, 136, 229, 0.25) !important;
    }

    .nav-pills .nav-link:not(.active):hover {
        background-color: rgba(30, 136, 229, 0.1) !important;
        color: #1e88e5 !important;
        transform: translateY(-1px);
        transition: all 0.2s ease;
    }

                    .btn.bg-brand-header {
        background: linear-gradient(135deg, #1e88e5 0%, #1976d2 100%) !important;
        border: none;
        transition: all 0.3s ease;
    }

                    .btn.bg-brand-header:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(30, 136, 229, 0.4) !important;
    }

                    .btn.bg-brand-header:disabled {
        opacity: 0.7;
        transform: none !important;
    }
</style>
@endpush
<!-- Header -->
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
                    <p class="mb-0 font-weight-normal text-sm">
                        <span class="badge bg-brand-header">{{ $user->getRoleName() }}</span>
                    </p>
                </div>
            </div>
            <div class="col-lg-4 col-md-6 my-sm-auto ms-sm-auto me-sm-0 mt-3 text-end">
                <a class="btn btn-sm bg-brand-header me-1" href="{{ route('profile.index') }}">
                    <i class="fas fa-arrow-left me-2"></i>Regresar
                </a>
            </div>
        </div>
    </div>
</div>

<!-- Main Content -->
<div class="container-fluid py-4">
     @if (session('success'))
        <div class="alert alert-success text-white alert-dismissible fade show" role="alert">
            <span class="alert-icon"><i class="fas fa-check-circle me-2"></i></span>
            <span class="alert-text"><strong>¡Éxito!</strong> {{ session('success') }}</span>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif
     @if ($errors->any())
        <div class="alert alert-danger text-white alert-dismissible fade show" role="alert">
            <strong class="alert-icon"><i class="fas fa-exclamation-triangle me-2"></i> ¡Error!</strong> Por favor, revisa los campos del formulario.
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif
    <div class="row">
        <div class="col-lg-3">
            <div class="card position-sticky top-1">
                <ul class="nav flex-column bg-white border-radius-lg p-3">
                    <li class="nav-item">
                        <a class="nav-link text-dark d-flex" data-scroll href="#basic-info">
                            <i class="fas fa-user text-lg me-2"></i>
                            <span class="text-sm">Información Básica</span>
                        </a>
                    </li>
                    <li class="nav-item pt-2">
                        <a class="nav-link text-dark d-flex" data-scroll href="#password">
                            <i class="fas fa-lock text-lg me-2"></i>
                            <span class="text-sm">Cambiar Contraseña</span>
                        </a>
                    </li>
                    <li class="nav-item pt-2">
                        <a class="nav-link text-dark d-flex" data-scroll href="#profile-photo">
                            <i class="fas fa-camera text-lg me-2"></i>
                            <span class="text-sm">Foto de Perfil</span>
                        </a>
                    </li>
                    <li class="nav-item pt-2">
                        <a class="nav-link text-dark d-flex" data-scroll href="#banner-photo">
                            <i class="fas fa-image text-lg me-2"></i>
                            <span class="text-sm">Banner</span>
                        </a>
                    </li>
                </ul>
            </div>
        </div>
        <div class="col-lg-9 mt-lg-0 mt-4">
            <!-- Card Basic Info -->
            <form action="{{ route('profile.update') }}" method="POST">
                @csrf
                @method('PUT')
                <div class="card" id="basic-info">
                    <div class="card-header bg-brand-header">
                        <h6 class="text-white mb-0">
                            <i class="fas fa-user me-2"></i>Información Básica
                        </h6>
                    </div>
                    <div class="card-body pt-0">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="name" class="form-label">Nombre Completo</label>
                                <input type="text" name="name" id="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name', $user->name) }}" required>
                                @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="email" class="form-label">Correo Electrónico</label>
                                <input type="email" name="email" id="email" class="form-control" value="{{ $user->email }}" readonly style="background-color: #f8f9fa; cursor: not-allowed;">
                                <small class="text-muted">El correo electrónico no se puede modificar</small>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="cui" class="form-label">CUI</label>
                                <input type="text" name="cui" id="cui" class="form-control @error('cui') is-invalid @enderror" value="{{ old('cui', $user->cui) }}" maxlength="13" placeholder="Ej: 1234567890123">
                                @error('cui') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="phone" class="form-label">Teléfono</label>
                                <input type="text" name="phone" id="phone" class="form-control @error('phone') is-invalid @enderror" value="{{ old('phone', $user->phone) }}">
                                @error('phone') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12 mb-3">
                                 <label for="address" class="form-label">Dirección</label>
                                <input type="text" name="address" id="address" class="form-control @error('address') is-invalid @enderror" value="{{ old('address', $user->address) }}">
                                @error('address') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                        </div>
                         <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="gender" class="form-label">Género</label>
                                <select name="gender" id="gender" class="form-select @error('gender') is-invalid @enderror">
                                    <option value="">No especificar</option>
                                    <option value="Masculino" {{ old('gender', $user->gender) == 'Masculino' ? 'selected' : '' }}>Masculino</option>
                                    <option value="Femenino" {{ old('gender', $user->gender) == 'Femenino' ? 'selected' : '' }}>Femenino</option>
                                </select>
                                @error('gender') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                             <div class="col-md-6 mb-3">
                                <label for="birth_date" class="form-label">Fecha de Nacimiento</label>
                                <input type="date" name="birth_date" id="birth_date" class="form-control @error('birth_date') is-invalid @enderror" value="{{ old('birth_date', $user->birth_date ? $user->birth_date->format('Y-m-d') : '') }}">
                                @error('birth_date') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                        </div>
                    </div>
                    <div class="card-footer text-end">
                        <button type="submit" class="btn bg-brand-header">Guardar Cambios</button>
                    </div>
                </div>
            </form>

            <!-- Card Change Password -->
            <form action="{{ route('profile.updatePassword') }}" method="POST">
                @csrf
                @method('PUT')
                <div class="card mt-4" id="password">
                    <div class="card-header bg-brand-header">
                        <h6 class="text-white mb-0">
                            <i class="fas fa-lock me-2"></i>Cambiar Contraseña
                        </h6>
                    </div>
                    <div class="card-body pt-0">
                        <div class="mb-3">
                             <label for="current_password" class="form-label">Contraseña Actual</label>
                            <input type="password" name="current_password" id="current_password" class="form-control @error('current_password', 'updatePassword') is-invalid @enderror" required>
                             @error('current_password', 'updatePassword') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="mb-3">
                             <label for="password" class="form-label">Nueva Contraseña</label>
                            <input type="password" name="password" id="password" class="form-control @error('password', 'updatePassword') is-invalid @enderror" required>
                             @error('password', 'updatePassword') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="mb-3">
                            <label for="password_confirmation" class="form-label">Confirmar Nueva Contraseña</label>
                            <input type="password" name="password_confirmation" id="password_confirmation" class="form-control" required>
                        </div>
                    </div>
                     <div class="card-footer text-end">
                        <button type="submit" class="btn bg-brand-header">Actualizar Contraseña</button>
                    </div>
                </div>
            </form>

            <!-- Sección para cambiar foto de perfil -->
            <div class="card mt-4" id="profile-photo">
                <div class="card-header bg-brand-header">
                    <div class="row align-items-center">
                        <div class="col">
                            <h6 class="text-white mb-0">
                                <i class="fas fa-camera me-2"></i>Nueva foto de perfil
                            </h6>
                        </div>
                    </div>
                </div>
                <div class="card-body pt-0">
                    <!-- Foto actual -->
                    @if($user->profile_photo_path)
                    <div class="mb-3" id="current-profile-photo">
                        <label class="form-label">Foto actual:</label>
                        <div class="d-flex align-items-center justify-content-between p-3 bg-light rounded">
                            <div class="d-flex align-items-center">
                                <img src="{{ $user->profile_photo_url }}" alt="Foto actual" class="me-3" style="width: 60px; height: 60px; object-fit: cover; border-radius: 8px;">
                                <div>
                                    <h6 class="mb-1">Foto de perfil actual</h6>
                                    <small class="text-muted">Haz clic en eliminar para usar la foto por defecto</small>
                                </div>
                            </div>
                            <button type="button" class="btn btn-sm btn-outline-danger" onclick="deleteCurrentProfilePhoto()">
                                <i class="fas fa-trash me-1"></i>Eliminar
                            </button>
                        </div>
                    </div>
                    @endif

                    <form id="profile-photo-form" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')
                        <div class="mb-3">
                            <label class="form-label">{{ $user->profile_photo_path ? 'Cambiar foto:' : 'Nueva foto:' }}</label>
                            <input type="file" 
                                   name="profile_photo" 
                                   id="profile-photo-input" 
                                   class="form-control form-control-lg border-2" 
                                   accept="image/jpeg,image/png,image/jpg" 
                                   style="border-color: #1e88e5;">
                            <div class="form-text text-muted mt-2">
                                <i class="fas fa-info-circle me-1"></i>
                                Formatos permitidos: JPG, PNG. Tamaño máximo: 2MB.
                            </div>
                        </div>
                        <div id="profile-photo-preview" class="mb-3" style="display: none;">
                            <div class="d-flex align-items-center justify-content-between p-3 bg-light rounded">
                                <div class="d-flex align-items-center">
                                    <img id="profile-photo-preview-img" src="" alt="Preview" class="me-3" style="width: 60px; height: 60px; object-fit: cover; border-radius: 8px;">
                                    <div>
                                        <h6 class="mb-1" id="profile-photo-filename">archivo.jpg</h6>
                                        <small class="text-muted" id="profile-photo-filesize">0 KB</small>
                                    </div>
                                </div>
                                <button type="button" class="btn btn-sm btn-outline-danger" onclick="clearProfilePhoto()">
                                    <i class="fas fa-times"></i>
                                </button>
                            </div>
                        </div>
                        <div class="text-end">
                            <button type="submit" class="btn bg-brand-header" id="upload-profile-photo-btn" disabled>
                                <i class="fas fa-upload me-2"></i>Subir Foto de Perfil
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Sección para cambiar banner -->
            <div class="card mt-4" id="banner-photo">
                <div class="card-header bg-brand-header">
                    <div class="row align-items-center">
                        <div class="col">
                            <h6 class="text-white mb-0">
                                <i class="fas fa-image me-2"></i>Nuevo banner de perfil
                            </h6>
                        </div>
                    </div>
                </div>
                <div class="card-body pt-0">
                    <!-- Banner actual -->
                    @if($user->banner_photo_path)
                    <div class="mb-3" id="current-banner-photo">
                        <label class="form-label">Banner actual:</label>
                        <div class="d-flex align-items-center justify-content-between p-3 bg-light rounded">
                            <div class="d-flex align-items-center">
                                <img src="{{ $user->banner_photo_url }}" alt="Banner actual" class="me-3" style="width: 80px; height: 45px; object-fit: cover; border-radius: 8px;">
                                <div>
                                    <h6 class="mb-1">Banner actual</h6>
                                    <small class="text-muted">Haz clic en eliminar para usar el banner por defecto</small>
                                </div>
                            </div>
                            <button type="button" class="btn btn-sm btn-outline-danger" onclick="deleteCurrentBanner()">
                                <i class="fas fa-trash me-1"></i>Eliminar
                            </button>
                        </div>
                    </div>
                    @endif

                    <form id="banner-photo-form" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')
                        <div class="mb-3">
                            <label class="form-label">{{ $user->banner_photo_path ? 'Cambiar banner:' : 'Nuevo banner:' }}</label>
                            <input type="file" 
                                   name="banner_photo" 
                                   id="banner-photo-input" 
                                   class="form-control form-control-lg border-2" 
                                   accept="image/jpeg,image/png,image/jpg" 
                                   style="border-color: #1e88e5;">
                            <div class="form-text text-muted mt-2">
                                <i class="fas fa-info-circle me-1"></i>
                                Formatos permitidos: JPG, PNG. Tamaño máximo: 4MB.
                            </div>
                        </div>
                        <div id="banner-photo-preview" class="mb-3" style="display: none;">
                            <div class="d-flex align-items-center justify-content-between p-3 bg-light rounded">
                                <div class="d-flex align-items-center">
                                    <img id="banner-photo-preview-img" src="" alt="Preview" class="me-3" style="width: 80px; height: 45px; object-fit: cover; border-radius: 8px;">
                                    <div>
                                        <h6 class="mb-1" id="banner-photo-filename">archivo.jpg</h6>
                                        <small class="text-muted" id="banner-photo-filesize">0 KB</small>
                                    </div>
                                </div>
                                <button type="button" class="btn btn-sm btn-outline-danger" onclick="clearBannerPhoto()">
                                    <i class="fas fa-times"></i>
                                </button>
                            </div>
                        </div>
                        <div class="text-end">
                            <button type="submit" class="btn bg-brand-header" id="upload-banner-photo-btn" disabled>
                                <i class="fas fa-upload me-2"></i>Subir Banner
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>



@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    // Funciones para manejar preview de archivos
    function setupFilePreview(inputId, previewId, previewImgId, filenameId, filesizeId, uploadBtnId, clearFunction) {
        const input = document.getElementById(inputId);
        const preview = document.getElementById(previewId);
        const previewImg = document.getElementById(previewImgId);
        const filename = document.getElementById(filenameId);
        const filesize = document.getElementById(filesizeId);
        const uploadBtn = document.getElementById(uploadBtnId);

        input.addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                // Validaciones
                const maxSize = 2 * 1024 * 1024; // 2MB
                const allowedTypes = ['image/jpeg', 'image/png', 'image/jpg'];
                
                if (!allowedTypes.includes(file.type)) {
                    showErrorToast('Solo se permiten archivos JPG, PNG.', 'Formato no válido');
                    e.target.value = '';
                    return;
                }
                
                if (file.size > maxSize) {
                    showErrorToast('El archivo no debe ser mayor a 2MB.', 'Archivo muy grande');
                    e.target.value = '';
                    return;
                }

                // Mostrar preview
                const reader = new FileReader();
                reader.onload = function(e) {
                    previewImg.src = e.target.result;
                    filename.textContent = file.name;
                    filesize.textContent = formatFileSize(file.size);
                    preview.style.display = 'block';
                    uploadBtn.disabled = false;
                };
                reader.readAsDataURL(file);
            }
        });
    }

    // Función para formatear tamaño de archivo
    function formatFileSize(bytes) {
        if (bytes === 0) return '0 Bytes';
        const k = 1024;
        const sizes = ['Bytes', 'KB', 'MB'];
        const i = Math.floor(Math.log(bytes) / Math.log(k));
        return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
    }

    // Función para limpiar foto de perfil
    window.clearProfilePhoto = function() {
        document.getElementById('profile-photo-input').value = '';
        document.getElementById('profile-photo-preview').style.display = 'none';
        document.getElementById('upload-profile-photo-btn').disabled = true;
    };

    // Función para limpiar banner
    window.clearBannerPhoto = function() {
        document.getElementById('banner-photo-input').value = '';
        document.getElementById('banner-photo-preview').style.display = 'none';
        document.getElementById('upload-banner-photo-btn').disabled = true;
    };

    // Función para eliminar foto de perfil actual
    window.deleteCurrentProfilePhoto = function() {
        fetch('{{ route("profile.deletePhoto") }}', {
            method: 'DELETE',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showSuccessToast('Foto de perfil eliminada correctamente.', 'Foto Eliminada');
                setTimeout(() => {
                    window.location.reload();
                }, 1500);
            } else {
                showErrorToast(data.message || 'Error al eliminar la foto.', 'Error');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showErrorToast('Error de conexión. Intenta nuevamente.', 'Error');
        });
    };

    // Función para eliminar banner actual
    window.deleteCurrentBanner = function() {
        fetch('{{ route("profile.deleteBanner") }}', {
            method: 'DELETE',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showSuccessToast('Banner eliminado correctamente.', 'Banner Eliminado');
                setTimeout(() => {
                    window.location.reload();
                }, 1500);
            } else {
                showErrorToast(data.message || 'Error al eliminar el banner.', 'Error');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showErrorToast('Error de conexión. Intenta nuevamente.', 'Error');
        });
    };

    // Configurar previews
    setupFilePreview(
        'profile-photo-input', 
        'profile-photo-preview', 
        'profile-photo-preview-img', 
        'profile-photo-filename', 
        'profile-photo-filesize', 
        'upload-profile-photo-btn',
        clearProfilePhoto
    );

    setupFilePreview(
        'banner-photo-input', 
        'banner-photo-preview', 
        'banner-photo-preview-img', 
        'banner-photo-filename', 
        'banner-photo-filesize', 
        'upload-banner-photo-btn',
        clearBannerPhoto
    );

    // Handle foto de perfil upload
    document.getElementById('profile-photo-form').addEventListener('submit', function(e) {
        e.preventDefault();
        
        const formData = new FormData(this);
        const btn = document.getElementById('upload-profile-photo-btn');
        const originalText = btn.innerHTML;
        
        btn.disabled = true;
        btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Subiendo...';

        fetch('{{ route("profile.updatePhoto") }}', {
            method: 'POST',
            body: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showSuccessToast('Foto de perfil actualizada correctamente.', 'Actualización Exitosa');
                setTimeout(() => {
                    window.location.reload();
                }, 1500);
            } else {
                showErrorToast(data.message || 'Error al subir la foto.', 'Error');
                btn.disabled = false;
                btn.innerHTML = originalText;
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showErrorToast('Error de conexión. Intenta nuevamente.', 'Error');
            btn.disabled = false;
            btn.innerHTML = originalText;
        });
    });

    // Handle banner upload
    document.getElementById('banner-photo-form').addEventListener('submit', function(e) {
        e.preventDefault();
        
        const formData = new FormData(this);
        const btn = document.getElementById('upload-banner-photo-btn');
        const originalText = btn.innerHTML;
        
        btn.disabled = true;
        btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Subiendo...';

        fetch('{{ route("profile.updateBanner") }}', {
            method: 'POST',
            body: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showSuccessToast('Banner actualizado correctamente.', 'Actualización Exitosa');
                setTimeout(() => {
                    window.location.reload();
                }, 1500);
            } else {
                showErrorToast(data.message || 'Error al subir el banner.', 'Error');
                btn.disabled = false;
                btn.innerHTML = originalText;
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showErrorToast('Error de conexión. Intenta nuevamente.', 'Error');
            btn.disabled = false;
            btn.innerHTML = originalText;
        });
    });
});
</script>
@endpush
@endsection 