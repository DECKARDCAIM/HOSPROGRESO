@extends('layouts.panel')

@section('title', 'Permisos del Rol: ' . $role->name)

@section('content')
<div class="container-fluid">
    
    <!-- Encabezado -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="text-primary mb-1">
                <i class="fas fa-key"></i> Gestión de Permisos
            </h2>
            <p class="text-muted mb-0">Configurar permisos para el rol: <strong>{{ $role->name }}</strong></p>
        </div>
        <div>
            <a href="{{ route('roles.index') }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left"></i> Volver a Roles
            </a>
        </div>
    </div>

    <!-- Información del rol -->
    <div class="card mb-4">
        <div class="card-body">
            <div class="row align-items-center">
                <div class="col-md-8">
                    <h5 class="card-title mb-1">{{ $role->name }}</h5>
                    <p class="card-text text-muted mb-0">{{ $role->description ?? 'Sin descripción' }}</p>
                </div>
                <div class="col-md-4 text-end">
                    <span class="badge bg-{{ $role->is_active ? 'success' : 'danger' }} fs-6">
                        {{ $role->is_active ? 'Activo' : 'Inactivo' }}
                    </span>
                </div>
            </div>
        </div>
    </div>

    <!-- Formulario de permisos -->
    <form action="{{ route('roles.permissions.update', $role) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="row">
            <!-- Botones de control -->
            <div class="col-12 mb-4">
                <div class="card bg-light">
                    <div class="card-body">
                        <h6 class="card-title text-primary mb-3">
                            <i class="fas fa-tools"></i> Controles Rápidos
                        </h6>
                        <div class="d-flex gap-2 flex-wrap">
                            <button type="button" class="btn btn-success btn-sm" id="selectAll">
                                <i class="fas fa-check-double"></i> Activar Todos
                            </button>
                            <button type="button" class="btn btn-warning btn-sm" id="deselectAll">
                                <i class="fas fa-times"></i> Desactivar Todos
                            </button>
                            <button type="button" class="btn btn-info btn-sm" id="toggleModules">
                                <i class="fas fa-exchange-alt"></i> Alternar por Módulos
                            </button>
                            <div class="ms-auto">
                                <small class="text-muted">
                                    <strong id="selectedCount">0</strong> permisos seleccionados
                                </small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Permisos por módulo -->
            @foreach($permissions as $module => $perms)
            <div class="col-lg-6 col-xl-4 mb-4">
                <div class="card h-100 shadow-sm module-card" data-module="{{ $module }}">
                    <div class="card-header bg-gradient-primary text-white">
                        <div class="d-flex justify-content-between align-items-center">
                            <div class="d-flex align-items-center">
                                <div class="form-check form-switch me-3">
                                    <input 
                                        class="form-check-input module-toggle" 
                                        type="checkbox" 
                                        id="module_{{ Str::slug($module) }}"
                                        data-module="{{ $module }}"
                                        {{ $perms->filter(fn($p) => in_array($p->id, $rolePermissions))->count() > 0 ? 'checked' : '' }}
                                    >
                                    <label class="form-check-label text-white fw-bold" for="module_{{ Str::slug($module) }}">
                                        {{ $module }}
                                    </label>
                                </div>
                            </div>
                            <div class="text-end">
                                <small class="text-white-50">{{ $perms->count() }} permisos</small>
                                <br>
                                <small class="module-status text-white" data-module="{{ $module }}">
                                    <span class="selected-count">{{ $perms->filter(fn($p) => in_array($p->id, $rolePermissions))->count() }}</span>/{{ $perms->count() }} activos
                                </small>
                            </div>
                        </div>
                    </div>
                    <div class="card-body permission-list" data-module="{{ $module }}">
                        @foreach($perms as $perm)
                        <div class="form-check form-switch mb-3 permission-item">
                            <input 
                                class="form-check-input permission-checkbox" 
                                type="checkbox" 
                                name="permissions[]" 
                                value="{{ $perm->id }}"
                                id="perm{{ $perm->id }}"
                                data-module="{{ $module }}"
                                @checked(in_array($perm->id, $rolePermissions))
                            >
                            <label class="form-check-label" for="perm{{ $perm->id }}">
                                <strong>{{ $perm->name }}</strong>
                                <br>
                                <small class="text-muted">
                                    <code>{{ $perm->slug }}</code>
                                </small>
                            </label>
                        </div>
                        @endforeach
                    </div>
                    <div class="card-footer bg-light">
                        <div class="d-flex justify-content-between">
                            <button type="button" class="btn btn-outline-success btn-sm select-module" data-module="{{ $module }}">
                                <i class="fas fa-check"></i> Activar Módulo
                            </button>
                            <button type="button" class="btn btn-outline-danger btn-sm deselect-module" data-module="{{ $module }}">
                                <i class="fas fa-times"></i> Desactivar
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            @endforeach
        </div>

        <!-- Botón de guardar -->
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body text-center">
                        <button type="submit" class="btn btn-primary btn-lg px-5">
                            <i class="fas fa-save"></i> Guardar Cambios
                        </button>
                        <p class="text-muted mt-2 mb-0">
                            Los cambios se aplicarán inmediatamente para todos los usuarios con este rol
                        </p>
                    </div>
                </div>
            </div>
        </div>

    </form>

</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const selectAllBtn = document.getElementById('selectAll');
    const deselectAllBtn = document.getElementById('deselectAll');
    const toggleModulesBtn = document.getElementById('toggleModules');
    const checkboxes = document.querySelectorAll('.permission-checkbox');
    const moduleToggles = document.querySelectorAll('.module-toggle');
    const selectedCountElement = document.getElementById('selectedCount');

    // Función para actualizar contador
    function updateSelectedCount() {
        const count = document.querySelectorAll('.permission-checkbox:checked').length;
        selectedCountElement.textContent = count;
        
        // Actualizar estados de módulos
        document.querySelectorAll('.module-card').forEach(card => {
            const module = card.dataset.module;
            const moduleCheckboxes = card.querySelectorAll('.permission-checkbox');
            const checkedInModule = card.querySelectorAll('.permission-checkbox:checked').length;
            const totalInModule = moduleCheckboxes.length;
            
            // Actualizar toggle del módulo
            const moduleToggle = card.querySelector('.module-toggle');
            if (checkedInModule === totalInModule) {
                moduleToggle.checked = true;
                moduleToggle.indeterminate = false;
            } else if (checkedInModule > 0) {
                moduleToggle.checked = false;
                moduleToggle.indeterminate = true;
            } else {
                moduleToggle.checked = false;
                moduleToggle.indeterminate = false;
            }
            
            // Actualizar contador del módulo
            const statusElement = card.querySelector('.module-status .selected-count');
            if (statusElement) {
                statusElement.textContent = checkedInModule;
            }
            
            // Cambiar color del header según estado
            const header = card.querySelector('.card-header');
            if (checkedInModule === totalInModule) {
                header.className = 'card-header bg-gradient-success text-white';
            } else if (checkedInModule > 0) {
                header.className = 'card-header bg-gradient-warning text-white';
            } else {
                header.className = 'card-header bg-gradient-secondary text-white';
            }
        });
    }

    // Seleccionar todos
    selectAllBtn.addEventListener('click', function() {
        checkboxes.forEach(checkbox => {
            checkbox.checked = true;
        });
        updateSelectedCount();
    });

    // Deseleccionar todos
    deselectAllBtn.addEventListener('click', function() {
        checkboxes.forEach(checkbox => {
            checkbox.checked = false;
        });
        updateSelectedCount();
    });

    // Alternar módulos (activar/desactivar módulos alternos)
    let toggleState = false;
    toggleModulesBtn.addEventListener('click', function() {
        document.querySelectorAll('.module-card').forEach((card, index) => {
            const moduleCheckboxes = card.querySelectorAll('.permission-checkbox');
            const shouldCheck = toggleState ? (index % 2 === 0) : (index % 2 === 1);
            
            moduleCheckboxes.forEach(checkbox => {
                checkbox.checked = shouldCheck;
            });
        });
        toggleState = !toggleState;
        updateSelectedCount();
    });

    // Manejar toggles de módulos
    moduleToggles.forEach(toggle => {
        toggle.addEventListener('change', function() {
            const module = this.dataset.module;
            const moduleCard = document.querySelector(`.module-card[data-module="${module}"]`);
            const moduleCheckboxes = moduleCard.querySelectorAll('.permission-checkbox');
            
            moduleCheckboxes.forEach(checkbox => {
                checkbox.checked = this.checked;
            });
            
            updateSelectedCount();
        });
    });

    // Botones de activar/desactivar módulo
    document.querySelectorAll('.select-module').forEach(btn => {
        btn.addEventListener('click', function() {
            const module = this.dataset.module;
            const moduleCard = document.querySelector(`.module-card[data-module="${module}"]`);
            const moduleCheckboxes = moduleCard.querySelectorAll('.permission-checkbox');
            
            moduleCheckboxes.forEach(checkbox => {
                checkbox.checked = true;
            });
            
            updateSelectedCount();
        });
    });

    document.querySelectorAll('.deselect-module').forEach(btn => {
        btn.addEventListener('click', function() {
            const module = this.dataset.module;
            const moduleCard = document.querySelector(`.module-card[data-module="${module}"]`);
            const moduleCheckboxes = moduleCard.querySelectorAll('.permission-checkbox');
            
            moduleCheckboxes.forEach(checkbox => {
                checkbox.checked = false;
            });
            
            updateSelectedCount();
        });
    });

    // Actualizar cuando se cambia un checkbox individual
    checkboxes.forEach(checkbox => {
        checkbox.addEventListener('change', updateSelectedCount);
    });

    // Confirmación antes de enviar
    document.querySelector('form').addEventListener('submit', function(e) {
        const selectedCount = document.querySelectorAll('.permission-checkbox:checked').length;
        
        if (selectedCount === 0) {
            e.preventDefault();
            Swal.fire({
                icon: 'warning',
                title: 'Sin permisos seleccionados',
                text: 'Debes seleccionar al menos un permiso para el rol.',
                confirmButtonText: 'Entendido'
            });
            return;
        }

        const activeModules = [...new Set([...document.querySelectorAll('.permission-checkbox:checked')].map(cb => cb.dataset.module))];
        
        Swal.fire({
            title: '¿Confirmar cambios?',
            html: `
                <div class="text-start">
                    <p><strong>Rol:</strong> {{ $role->name }}</p>
                    <p><strong>Permisos seleccionados:</strong> ${selectedCount}</p>
                    <p><strong>Módulos activos:</strong> ${activeModules.length}</p>
                    <div class="mt-3">
                        <small class="text-muted">Los cambios se aplicarán inmediatamente</small>
                    </div>
                </div>
            `,
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'Sí, guardar cambios',
            cancelButtonText: 'Cancelar',
            confirmButtonColor: '#28a745'
        }).then((result) => {
            if (!result.isConfirmed) {
                e.preventDefault();
            }
        });
        
        e.preventDefault(); // Prevenir envío automático para mostrar SweetAlert
    });

    // Inicializar contador
    updateSelectedCount();
});
</script>
@endpush

@push('styles')
<style>
.form-check-input:checked {
    background-color: #28a745;
    border-color: #28a745;
}

.card:hover {
    transform: translateY(-2px);
    transition: transform 0.2s ease-in-out;
}

.permission-checkbox {
    transform: scale(1.2);
}

.form-check-label {
    cursor: pointer;
}
</style>
@endpush