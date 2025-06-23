@extends('layouts.panel')

@section('title', 'Editar Perfil')

@section('content')
@push('styles')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.6.1/cropper.min.css" integrity="sha512-zxCiHjjAbBILGlO7JAiPOhEk6KSPG+BFCk23+SonilaBGJo3QozM59JvsLCAgmNCoFKgf/t6spGqJCfeA6CJcQ==" crossorigin="anonymous" referrerpolicy="no-referrer" />
<style>
    .page-header {
        background-image: url('{{ asset('img/carrusel/bk1.webp') }}');
        background-size: cover;
        background-position: center;
    }
    .profile-photo-container {
        position: relative;
        display: inline-block;
        cursor: pointer;
    }
    .profile-photo-overlay {
        position: absolute;
        bottom: 0;
        left: 0;
        right: 0;
        background-color: rgba(0, 0, 0, 0.5);
        color: white;
        width: 100%;
        transition: .3s ease;
        opacity: 0;
        padding: 8px;
        text-align: center;
        border-bottom-left-radius: .75rem;
        border-bottom-right-radius: .75rem;
    }
    .profile-photo-container:hover .profile-photo-overlay {
        opacity: 1;
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
    .profile-avatar-container {
        position: relative;
        width: fit-content;
    }
    .edit-photo-icon {
        position: absolute;
        bottom: 5px;
        right: 5px;
        background-color: white;
        color: #344767;
        border-radius: 50%;
        padding: 8px;
        box-shadow: 0 4px 6px rgba(0,0,0,0.2);
        line-height: 1;
        cursor: pointer;
        transition: all 0.2s ease-in-out;
    }
    .edit-photo-icon:hover { transform: scale(1.1); }
    .card-body .form-control, .card-body .form-select {
        border: 1px solid #d2d6da !important;
        background-color: #fff !important;
        padding: 0.5rem 0.75rem !important;
    }
    .card-body .form-control:focus, .card-body .form-select:focus {
        border-color: #5e72e4 !important;
        box-shadow: 0 0 0 2px rgba(94, 114, 228, 0.25) !important;
    }
    #banner-btn {
        font-size: 1.5rem;
        width: 50px;
        height: 50px;
        cursor: pointer;
    }
    .cropper-container {
        direction: ltr;
        font-size: 0;
        line-height: 0;
        position: relative;
        -ms-touch-action: none;
        touch-action: none;
        -webkit-user-select: none;
        -moz-user-select: none;
        -ms-user-select: none;
        user-select: none;
    }
    .img-container { max-height: 60vh; }
</style>
@endpush
<!-- Header -->
<div class="container-fluid px-2 px-md-4">
    <div class="page-header min-height-300 border-radius-xl mt-4" style="background-image: url('{{ $user->banner_photo_url }}');">
        <span class="mask bg-gradient-dark opacity-4"></span>
        
        <label for="banner-upload" id="banner-btn" title="Cambiar Banner" class="btn btn-icon-only btn-sm btn-white mb-0 rounded-circle position-absolute top-0 end-0 m-3 d-flex justify-content-center align-items-center">
            <i class="fas fa-camera"></i>
        </label>
        <input type="file" id="banner-upload" class="d-none" accept="image/*">
    </div>
    <div class="card card-body mx-3 mx-md-4 mt-n6">
        <div class="row gx-4 mb-2">
            <div class="col-auto">
                <div class="profile-avatar-container">
                    <div class="avatar avatar-xl position-relative">
                        <img src="{{ $user->profile_photo_url }}" alt="profile_image" id="profile-image-preview" class="w-100 border-radius-lg shadow-sm">
                    </div>
                    <label for="photo-upload" class="cursor-pointer">
                       <i class="fas fa-camera edit-photo-icon" title="Cambiar foto de perfil"></i>
                    </label>
                    <input type="file" id="photo-upload" class="d-none" accept="image/*">
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
                            <a class="nav-link mb-0 px-0 py-1" href="{{ route('profile.index') }}">
                                <i class="material-symbols-rounded text-lg position-relative">person</i>
                                <span class="ms-1">Ver Perfil</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link mb-0 px-0 py-1 active" href="javascript:;">
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
                            <i class="material-symbols-rounded text-lg me-2">receipt_long</i>
                            <span class="text-sm">Información Básica</span>
                        </a>
                    </li>
                    <li class="nav-item pt-2">
                        <a class="nav-link text-dark d-flex" data-scroll href="#password">
                            <i class="material-symbols-rounded text-lg me-2">lock</i>
                            <span class="text-sm">Cambiar Contraseña</span>
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
                    <div class="card-header">
                        <h5>Información Básica</h5>
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
                                <input type="email" name="email" id="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email', $user->email) }}" required>
                                @error('email') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="phone" class="form-label">Teléfono</label>
                                <input type="text" name="phone" id="phone" class="form-control @error('phone') is-invalid @enderror" value="{{ old('phone', $user->phone) }}">
                                @error('phone') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-md-6 mb-3">
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
                        <button type="submit" class="btn bg-gradient-dark">Guardar Cambios</button>
                    </div>
                </div>
            </form>

            <!-- Card Change Password -->
            <form action="{{ route('profile.updatePassword') }}" method="POST">
                @csrf
                @method('PUT')
                <div class="card mt-4" id="password">
                    <div class="card-header">
                        <h5>Cambiar Contraseña</h5>
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
                        <button type="submit" class="btn bg-gradient-dark">Actualizar Contraseña</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Cropper Modal -->
<div class="modal fade" id="cropperModal" tabindex="-1" aria-labelledby="cropperModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="cropperModalLabel">Recortar Imagen</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="img-container">
                    <img id="cropperImage" src="">
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn bg-gradient-dark" id="cropAndUpload">Recortar y Guardar</button>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.6.1/cropper.min.js" integrity="sha512-9KkIqdfN7ipEW6B6iN+5lnf+iF9iG2RzVFAwc3iJEJUdAiR0nCjMvdo9iAUuC3p7jKoPoaUbv2aJvjKagN3gpQ==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const cropperModalEl = document.getElementById('cropperModal');
    const cropperModal = new bootstrap.Modal(cropperModalEl);
    const image = document.getElementById('cropperImage');
    const cropAndUploadBtn = document.getElementById('cropAndUpload');
    let cropper;
    let uploadUrl = '';
    let inputName = '';

    function showCropper(file, options) {
        if (!file) return;
        const reader = new FileReader();
        reader.onload = function (e) {
            image.src = e.target.result;
            cropperModal.show();
        };
        reader.readAsDataURL(file);

        cropperModalEl.addEventListener('shown.bs.modal', function () {
            if (cropper) cropper.destroy();
            cropper = new Cropper(image, options);
        }, { once: true });
    }

    document.getElementById('photo-upload').addEventListener('change', function (e) {
        uploadUrl = "{{ route('profile.updatePhoto') }}";
        inputName = 'photo';
        showCropper(e.target.files[0], { aspectRatio: 1, viewMode: 1 });
        e.target.value = '';
    });

    document.getElementById('banner-upload').addEventListener('change', function (e) {
        uploadUrl = "{{ route('profile.updateBanner') }}";
        inputName = 'banner_photo';
        showCropper(e.target.files[0], { aspectRatio: 16 / 5, viewMode: 1, autoCropArea: 1});
        e.target.value = '';
    });

    cropAndUploadBtn.addEventListener('click', function () {
        this.disabled = true;
        this.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Procesando...';

        cropper.getCroppedCanvas({ maxWidth: 4096, maxHeight: 4096 }).toBlob((blob) => {
            const formData = new FormData();
            formData.append(inputName, blob, 'image.jpg');
            formData.append('_method', 'PUT');
            formData.append('_token', "{{ csrf_token() }}");

            fetch(uploadUrl, {
                method: 'POST',
                headers: { 'Accept': 'application/json' },
                body: formData,
            })
            .then(response => response.json().then(data => ({ ok: response.ok, data })))
            .then(({ ok, data }) => {
                if (ok) {
                    window.location.reload();
                } else {
                    alert('Error: ' + (data.message || 'Ocurrió un error al subir la imagen.'));
                }
            })
            .catch(error => alert('Ocurrió un error de red.'))
            .finally(() => { cropperModal.hide(); });
        }, 'image/jpeg');
    });
});
</script>
@endpush
@endsection 