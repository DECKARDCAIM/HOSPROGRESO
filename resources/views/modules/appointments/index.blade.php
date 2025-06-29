@extends('layouts.panel')

@section('title', 'Gestión de Citas Médicas')
@section('breadcrumb', 'Citas / Gestión')

@push('styles')
<style>
.stats-section {
    margin-bottom: 2rem;
}

/* Mantener dropdown dentro de la tabla */
.table-responsive {
    position: relative;
    min-width: 100%;
}

.table {
    min-width: 1200px; /* Ancho mínimo para que todo se vea bien */
}

.dropdown-menu {
    position: absolute !important;
    transform: none !important;
    max-width: 280px;
    min-width: 220px;
    white-space: nowrap;
    box-shadow: 0 .125rem .25rem rgba(0,0,0,.075) !important;
    border: 1px solid rgba(0,0,0,.15);
}

/* Ajustar posición del dropdown según su ubicación */
.dropdown.dropend .dropdown-menu {
    left: 100% !important;
    top: 0 !important;
    margin-left: 0.125rem;
}

.dropdown.dropstart .dropdown-menu {
    right: 100% !important;
    top: 0 !important;
    left: auto !important;
    margin-right: 0.125rem;
}

.dropdown.dropup .dropdown-menu {
    bottom: 100% !important;
    top: auto !important;
    margin-bottom: 0.125rem;
}

/* Asegurar que el dropdown esté siempre visible */
.table td .dropdown {
    position: relative;
}

.table td .dropdown-menu {
    z-index: 1055;
}

/* Prevenir overflow en las celdas pero permitir dropdown */
.table td {
    overflow: visible;
    position: relative;
}

/* Asegurar que las acciones estén siempre visibles */
.table th:last-child,
.table td:last-child {
    position: sticky;
    right: 0;
    background: white;
    z-index: 10;
    border-left: 1px solid #dee2e6;
}
</style>
@endpush

@section('content')
<div class="container-fluid py-4">
    <!-- Estadísticas rápidas -->
    <div class="row mb-4 stats-section">
        <div class="col-xl-2 col-md-4 col-6">
            <div class="card">
                <div class="card-body p-2">
                    <div class="row">
                        <div class="col-8">
                            <div class="numbers">
                                <p class="text-xs mb-0 text-capitalize font-weight-bold">Total</p>
                                <h6 class="font-weight-bolder mb-0">{{ $stats['total'] }}</h6>
                            </div>
                        </div>
                        <div class="col-4 text-end">
                            <div class="icon icon-shape bg-primary shadow text-center border-radius-md">
                                <i class="fas fa-calendar-alt text-lg opacity-10"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-2 col-md-4 col-6">
            <div class="card">
                <div class="card-body p-2">
                    <div class="row">
                        <div class="col-8">
                            <div class="numbers">
                                <p class="text-xs mb-0 text-capitalize font-weight-bold">Pendientes</p>
                                <h6 class="font-weight-bolder mb-0 text-warning">{{ $stats['pendientes'] }}</h6>
                            </div>
                        </div>
                        <div class="col-4 text-end">
                            <div class="icon icon-shape bg-warning shadow text-center border-radius-md">
                                <i class="fas fa-clock text-lg opacity-10"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-2 col-md-4 col-6">
            <div class="card">
                <div class="card-body p-2">
                    <div class="row">
                        <div class="col-8">
                            <div class="numbers">
                                <p class="text-xs mb-0 text-capitalize font-weight-bold">Confirmadas</p>
                                <h6 class="font-weight-bolder mb-0 text-info">{{ $stats['confirmadas'] }}</h6>
                            </div>
                        </div>
                        <div class="col-4 text-end">
                            <div class="icon icon-shape bg-info shadow text-center border-radius-md">
                                <i class="fas fa-check-circle text-lg opacity-10"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-2 col-md-4 col-6">
            <div class="card">
                <div class="card-body p-2">
                    <div class="row">
                        <div class="col-8">
                            <div class="numbers">
                                <p class="text-xs mb-0 text-capitalize font-weight-bold">Atendidas</p>
                                <h6 class="font-weight-bolder mb-0 text-success">{{ $stats['atendidas'] }}</h6>
                            </div>
                        </div>
                        <div class="col-4 text-end">
                            <div class="icon icon-shape bg-success shadow text-center border-radius-md">
                                <i class="fas fa-user-check text-lg opacity-10"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-2 col-md-4 col-6">
            <div class="card">
                <div class="card-body p-2">
                    <div class="row">
                        <div class="col-8">
                            <div class="numbers">
                                <p class="text-xs mb-0 text-capitalize font-weight-bold">Perdidas</p>
                                <h6 class="font-weight-bolder mb-0 text-secondary">{{ $stats['perdidas'] }}</h6>
                            </div>
                        </div>
                        <div class="col-4 text-end">
                            <div class="icon icon-shape bg-secondary shadow text-center border-radius-md">
                                <i class="fas fa-user-slash text-lg opacity-10"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-2 col-md-4 col-6">
            <div class="card">
                <div class="card-body p-2">
                    <div class="row">
                        <div class="col-8">
                            <div class="numbers">
                                <p class="text-xs mb-0 text-capitalize font-weight-bold">Canceladas</p>
                                <h6 class="font-weight-bolder mb-0 text-danger">{{ $stats['canceladas'] }}</h6>
                            </div>
                        </div>
                        <div class="col-4 text-end">
                            <div class="icon icon-shape bg-danger shadow text-center border-radius-md">
                                <i class="fas fa-times-circle text-lg opacity-10"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card mb-4">
                <div class="card-header pb-0 bg-info">
                    <div class="row align-items-center">
                        <div class="col-md-8">
                            <h6 class="text-white mb-0">Gestión de Citas Médicas</h6>
                            <p class="text-sm text-white opacity-8 mb-0">
                                Sistema inteligente de agendamiento de citas con gestión automática de cupos
                            </p>
                        </div>
                        <div class="col-md-4 text-end">
                            <a href="{{ route('appointments.create') }}" class="btn btn-sm btn-white">
                                <i class="fas fa-plus me-2"></i>Nueva Cita
                            </a>
                        </div>
                    </div>
                </div>
                <div class="card-body px-0 pt-0 pb-4">
                    <!-- Pestañas de filtros por estado -->
                    <div class="px-3 pt-4 pb-3">
                        <ul class="nav nav-pills nav-fill" id="statusTabs" role="tablist">
                            <li class="nav-item" role="presentation">
                                <a class="nav-link {{ !request('status') ? 'active' : '' }}" href="{{ route('appointments.index') }}">
                                    <i class="fas fa-list me-2"></i>Todas ({{ $stats['total'] }})
                                </a>
                            </li>
                            <li class="nav-item" role="presentation">
                                <a class="nav-link {{ request('status') === 'pendiente' ? 'active' : '' }}" href="{{ route('appointments.index', ['status' => 'pendiente']) }}">
                                    <i class="fas fa-clock me-2"></i>Pendientes ({{ $stats['pendientes'] }})
                                </a>
                            </li>
                            <li class="nav-item" role="presentation">
                                <a class="nav-link {{ request('status') === 'confirmada' ? 'active' : '' }}" href="{{ route('appointments.index', ['status' => 'confirmada']) }}">
                                    <i class="fas fa-check-circle me-2"></i>Confirmadas ({{ $stats['confirmadas'] }})
                                </a>
                            </li>
                            <li class="nav-item" role="presentation">
                                <a class="nav-link {{ request('status') === 'atendida' ? 'active' : '' }}" href="{{ route('appointments.index', ['status' => 'atendida']) }}">
                                    <i class="fas fa-user-check me-2"></i>Atendidas ({{ $stats['atendidas'] }})
                                </a>
                            </li>
                            <li class="nav-item" role="presentation">
                                <a class="nav-link {{ request('status') === 'perdida' ? 'active' : '' }}" href="{{ route('appointments.index', ['status' => 'perdida']) }}">
                                    <i class="fas fa-user-slash me-2"></i>Perdidas ({{ $stats['perdidas'] }})
                                </a>
                            </li>
                            <li class="nav-item" role="presentation">
                                <a class="nav-link {{ request('status') === 'cancelada' ? 'active' : '' }}" href="{{ route('appointments.index', ['status' => 'cancelada']) }}">
                                    <i class="fas fa-times-circle me-2"></i>Canceladas ({{ $stats['canceladas'] }})
                                </a>
                            </li>
                        </ul>
                    </div>

                    <!-- Filtros avanzados -->
                    <form method="GET" class="px-3 py-4 border-top" id="filtersForm">
                        <input type="hidden" name="status" value="{{ request('status') }}">
                        <div class="row g-2 align-items-end">
                            <div class="col-md-3">
                                <label class="form-label">Buscar (Paciente, CUI, N° Cita)</label>
                                <input type="text" name="q" class="form-control" placeholder="Buscar..." value="{{ request('q') }}">
                            </div>
                            <div class="col-md-2">
                                <label class="form-label">Especialidad</label>
                                <select name="specialty_id" class="form-select">
                                    <option value="">Todas</option>
                                    @foreach($specialties as $specialty)
                                        <option value="{{ $specialty->id }}" {{ request('specialty_id') == $specialty->id ? 'selected' : '' }}>
                                            {{ $specialty->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-2">
                                <label class="form-label">Doctor</label>
                                <select name="doctor_id" class="form-select">
                                    <option value="">Todos</option>
                                    @foreach($doctors as $doctor)
                                        <option value="{{ $doctor->id }}" {{ request('doctor_id') == $doctor->id ? 'selected' : '' }}>
                                            {{ $doctor->full_name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-2">
                                <label class="form-label">Fecha</label>
                                <input type="date" name="date" class="form-control" value="{{ request('date') }}">
                            </div>
                            <div class="col-md-3 d-flex align-items-end gap-2">
                                <button type="submit" class="btn btn-info"><i class="fas fa-search me-2"></i>Filtrar</button>
                                <a href="{{ route('appointments.index') }}" class="btn btn-secondary">Limpiar</a>
                            </div>
                        </div>
                    </form>

                    <!-- Tabla de citas -->
                    <div class="table-responsive p-0" style="overflow-x: auto;">
                        <table class="table align-items-center mb-0">
                            <thead>
                                <tr>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-3">N° Cita</th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-3">Paciente</th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-3">Especialidad</th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-3">Doctor</th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-3">Fecha y Hora</th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-3">Turno</th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-3">Estado</th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-3 text-center">Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($appointments as $appointment)
                                    <tr>
                                        <td class="px-3 py-2">
                                            <div class="d-flex align-items-center">
                                                <div class="avatar avatar-sm me-3 bg-info rounded-circle">
                                                    <span class="text-white font-weight-bold text-xs">{{ $appointment->slot_number ?? '1' }}</span>
                                                </div>
                                                <div>
                                                    <h6 class="mb-0 text-sm">{{ $appointment->appointment_number }}</h6>
                                                    <p class="text-xs text-secondary mb-0">{{ $appointment->attention_type }}</p>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-3 py-2">
                                            <p class="text-sm font-weight-bold mb-0">{{ $appointment->clinicalRecord->full_name ?? '-' }}</p>
                                            <p class="text-xs text-secondary mb-0">{{ $appointment->clinicalRecord->cui ?? '-' }}</p>
                                        </td>
                                        <td class="px-3 py-2">
                                            <p class="text-sm text-secondary mb-0">{{ $appointment->specialty->name ?? '-' }}</p>
                                        </td>
                                        <td class="px-3 py-2">
                                            <p class="text-sm text-secondary mb-0">{{ $appointment->doctor->full_name ?? '-' }}</p>
                                            <p class="text-xs text-secondary mb-0">{{ $appointment->scheduleType->name ?? '-' }}</p>
                                        </td>
                                        <td class="px-3 py-2">
                                            <p class="text-sm font-weight-bold mb-0">{{ $appointment->appointment_date->format('d/m/Y') }}</p>
                                            <p class="text-xs text-secondary mb-0">{{ $appointment->appointment_date->format('H:i') }}</p>
                                        </td>
                                        <td class="px-3 py-2">
                                            <span class="badge badge-sm bg-primary">Turno {{ $appointment->slot_number ?? '1' }}</span>
                                        </td>
                                        <td class="px-3 py-2">
                                            <span class="badge {{ $appointment->status_badge }}">{{ $appointment->status_text }}</span>
                                        </td>
                                        <td class="align-middle text-center">
                                            <div class="dropdown">
                                                <button class="btn btn-link text-secondary mb-0" data-bs-toggle="dropdown" data-bs-auto-close="true">
                                                    <i class="fas fa-ellipsis-v"></i>
                                                </button>
                                                <ul class="dropdown-menu dropdown-menu-end">
                                                    <li><a class="dropdown-item" href="{{ route('appointments.show', $appointment) }}">
                                                        <i class="fas fa-eye me-2"></i>Ver Detalles
                                                    </a></li>
                                                    <li><a class="dropdown-item" href="{{ route('appointments.print', $appointment) }}" target="_blank">
                                                        <i class="fas fa-print me-2"></i>Imprimir PDF
                                                    </a></li>
                                                    @if(in_array($appointment->status, ['pendiente', 'confirmada']))
                                                        <li><hr class="dropdown-divider"></li>
                                                        @if($appointment->status === 'pendiente')
                                                        <li><button class="dropdown-item text-info" onclick="updateStatus('{{ $appointment->id }}', 'confirmada')">
                                                            <i class="fas fa-check me-2"></i>Confirmar Cita
                                                        </button></li>
                                                        @endif
                                                        @if(in_array($appointment->status, ['pendiente', 'confirmada']))
                                                        <li><button class="dropdown-item text-success" onclick="updateStatus('{{ $appointment->id }}', 'atendida')">
                                                            <i class="fas fa-user-check me-2"></i>Marcar como Atendida
                                                        </button></li>
                                                        <li><button class="dropdown-item text-secondary" onclick="confirmAction(() => updateStatus('{{ $appointment->id }}', 'perdida'), '¿Marcar esta cita como perdida?')">
                                                            <i class="fas fa-user-slash me-2"></i>Marcar como Perdida
                                                        </button></li>
                                                        <li><button class="dropdown-item text-warning" onclick="rescheduleAppointment('{{ $appointment->id }}')">
                                                            <i class="fas fa-calendar-alt me-2"></i>Reagendar Cita
                                                        </button></li>
                                                        <li><button class="dropdown-item text-danger" onclick="confirmAction(() => updateStatus('{{ $appointment->id }}', 'cancelada'), '¿Está seguro de cancelar esta cita?')">
                                                            <i class="fas fa-times me-2"></i>Cancelar Cita
                                                        </button></li>
                                                        @endif
                                                    @endif
                                                </ul>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="8" class="text-center py-4">
                                            <span class="text-muted">No hay citas registradas.</span>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    <div class="d-flex justify-content-center mt-4 mb-3">
                        {{ $appointments->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal para actualizar estado -->
<div class="modal fade" id="statusModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Actualizar Estado de Cita</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="statusForm" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <input type="hidden" name="status" id="statusInput">
                    <div class="mb-3">
                        <label class="form-label">Notas adicionales</label>
                        <textarea name="notes" class="form-control" rows="3" placeholder="Agregar notas sobre esta acción..."></textarea>
                    </div>
                    <div class="mb-3" id="cancelReasonDiv" style="display: none;">
                        <label class="form-label">Razón de cancelación *</label>
                        <input type="text" name="cancelled_reason" id="cancelledReasonInput" class="form-control" placeholder="Especificar razón de cancelación">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Actualizar Estado</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal para reagendar -->
<div class="modal fade" id="rescheduleModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Reagendar Cita</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="rescheduleForm" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Nuevo Doctor *</label>
                        <select name="doctor_id" class="form-select" required>
                            <option value="">Seleccionar doctor...</option>
                            @foreach($doctors as $doctor)
                                <option value="{{ $doctor->id }}">{{ $doctor->full_name }} - {{ $doctor->specialty->name ?? '' }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Notas</label>
                        <textarea name="notes" class="form-control" rows="3" placeholder="Agregar notas sobre el reagendamiento..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-warning">Reagendar Cita</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function updateStatus(appointmentId, status) {
    console.log('=== FUNCIÓN updateStatus LLAMADA ===');
    console.log('appointmentId:', appointmentId);
    console.log('status:', status);
    
    const statusModal = new bootstrap.Modal(document.getElementById('statusModal'));
    const form = document.getElementById('statusForm');
    const statusInput = document.getElementById('statusInput');
    const cancelReasonDiv = document.getElementById('cancelReasonDiv');
    const cancelReasonInput = document.getElementById('cancelledReasonInput');
    
    // CONFIGURAR FORMULARIO
    form.action = `/appointments/${appointmentId}/status`;
    statusInput.value = status;
    
    console.log('Form action configurada:', form.action);
    console.log('Status input configurado:', statusInput.value);
    
    // MOSTRAR/OCULTAR CAMPO DE CANCELACIÓN
    if (status === 'cancelada') {
        cancelReasonDiv.style.display = 'block';
        cancelReasonInput.required = true;
        console.log('Campo de cancelación mostrado');
    } else {
        cancelReasonDiv.style.display = 'none';
        cancelReasonInput.required = false;
        cancelReasonInput.value = '';
        console.log('Campo de cancelación ocultado');
    }
    
    // CAMBIAR TÍTULO DEL MODAL
    const modalTitle = document.querySelector('#statusModal .modal-title');
    const statusTexts = {
        'confirmada': 'Confirmar Cita',
        'atendida': 'Marcar como Atendida',
        'perdida': 'Marcar como Perdida',
        'cancelada': 'Cancelar Cita'
    };
    modalTitle.textContent = statusTexts[status] || 'Actualizar Estado de Cita';
    console.log('Título del modal:', modalTitle.textContent);
    
    // LIMPIAR NOTAS ANTERIORES
    const notesInput = form.querySelector('textarea[name="notes"]');
    if (notesInput) {
        notesInput.value = '';
    }
    
    statusModal.show();
}

function rescheduleAppointment(appointmentId) {
    const rescheduleModal = new bootstrap.Modal(document.getElementById('rescheduleModal'));
    const form = document.getElementById('rescheduleForm');
    
    form.action = `/appointments/${appointmentId}/reschedule`;
    rescheduleModal.show();
}

// Función para confirmaciones
function confirmAction(callback, message) {
    if (confirm(message)) {
        callback();
    }
}

// Agregar token CSRF a todos los formularios
document.addEventListener('DOMContentLoaded', function() {
    // Asegurar que los formularios tengan el token CSRF
    const forms = document.querySelectorAll('#statusForm, #rescheduleForm');
    forms.forEach(form => {
        if (!form.querySelector('input[name="_token"]')) {
            const csrfToken = document.querySelector('meta[name="csrf-token"]');
            if (csrfToken) {
                const tokenInput = document.createElement('input');
                tokenInput.type = 'hidden';
                tokenInput.name = '_token';
                tokenInput.value = csrfToken.getAttribute('content');
                form.appendChild(tokenInput);
            }
        }
    });
});

// Manejar envío del formulario de estado
document.addEventListener('DOMContentLoaded', function() {
    document.getElementById('statusForm').addEventListener('submit', function(e) {
        e.preventDefault();
        
        console.log('=== FORMULARIO ENVIADO ===');
        
        const statusInput = document.getElementById('statusInput');
        const cancelReasonInput = document.getElementById('cancelledReasonInput');
        const notesInput = this.querySelector('textarea[name="notes"]');
        
        console.log('Status a enviar:', statusInput.value);
        console.log('Notes:', notesInput.value);
        console.log('Cancelled reason:', cancelReasonInput.value);
        
        // Validación para cancelación
        if (statusInput.value === 'cancelada') {
            if (!cancelReasonInput.value || cancelReasonInput.value.trim() === '') {
                showErrorToast('Debe especificar un motivo para cancelar la cita', 'Campo Requerido');
                cancelReasonInput.focus();
                return;
            }
        }
        
        const submitBtn = this.querySelector('button[type="submit"]');
        const originalText = submitBtn.innerHTML;
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Procesando...';
        
        // Crear FormData manualmente para control total
        const formData = new FormData();
        formData.append('_token', document.querySelector('meta[name="csrf-token"]').getAttribute('content'));
        formData.append('_method', 'PUT');
        formData.append('status', statusInput.value);
        formData.append('notes', notesInput.value || '');
        
        if (statusInput.value === 'cancelada') {
            formData.append('cancelled_reason', cancelReasonInput.value);
        }
        
        console.log('=== DATOS A ENVIAR ===');
        for (let [key, value] of formData.entries()) {
            console.log(key + ':', value);
        }
        
        fetch(this.action, {
            method: 'POST',
            body: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            }
        })
        .then(response => {
            console.log('Response status:', response.status);
            if (!response.ok) {
                return response.json().then(err => Promise.reject(err));
            }
            return response.json();
        })
        .then(data => {
            console.log('Response data:', data);
            if (data.success) {
                const modal = bootstrap.Modal.getInstance(document.getElementById('statusModal'));
                modal.hide();
                
                const statusTexts = {
                    'confirmada': 'confirmada',
                    'atendida': 'atendida', 
                    'perdida': 'perdida',
                    'cancelada': 'cancelada'
                };
                
                showSuccessToast(
                    `La cita ha sido ${statusTexts[statusInput.value]} correctamente`,
                    'Estado Actualizado'
                );
                
                setTimeout(() => {
                    window.location.reload();
                }, 1500);
            } else {
                showErrorToast(data.message || 'Error desconocido', 'Error');
            }
        })
        .catch(error => {
            console.error('Error completo:', error);
            if (error.errors) {
                let errorMsg = '';
                Object.keys(error.errors).forEach(key => {
                    errorMsg += `${error.errors[key].join(', ')} `;
                });
                showErrorToast(errorMsg.trim(), 'Errores de Validación');
            } else {
                showErrorToast(error.message || 'Error inesperado', 'Error');
            }
        })
        .finally(() => {
            submitBtn.disabled = false;
            submitBtn.innerHTML = originalText;
        });
    });
});

document.getElementById('rescheduleForm').addEventListener('submit', function(e) {
    const submitBtn = this.querySelector('button[type="submit"]');
    submitBtn.disabled = true;
    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Reagendando...';
});

// Auto-refresh si hay mensaje de éxito (indica que se actualizó algo)
@if(session('success'))
setTimeout(function() {
    // Recargar solo las estadísticas sin hacer refresh completo
    window.location.href = window.location.href.split('?')[0] + window.location.search;
}, 100);
@endif

// Posicionamiento inteligente de dropdowns
document.addEventListener('DOMContentLoaded', function() {
    const dropdowns = document.querySelectorAll('.dropdown');
    
    dropdowns.forEach(dropdown => {
        const button = dropdown.querySelector('[data-bs-toggle="dropdown"]');
        const menu = dropdown.querySelector('.dropdown-menu');
        
        button.addEventListener('click', function() {
            setTimeout(() => {
                const rect = button.getBoundingClientRect();
                const tableContainer = document.querySelector('.table-responsive');
                const containerRect = tableContainer.getBoundingClientRect();
                
                // Calcular posición relativa dentro del contenedor
                const relativeX = rect.left - containerRect.left;
                const relativeY = rect.top - containerRect.top;
                const containerWidth = containerRect.width;
                const containerHeight = containerRect.height;
                
                // Ajustar posición según ubicación
                menu.classList.remove('dropdown-menu-end', 'dropdown-menu-start');
                dropdown.classList.remove('dropup', 'dropend', 'dropstart');
                
                // Si está muy a la derecha, abrir hacia la izquierda
                if (relativeX > containerWidth * 0.7) {
                    menu.classList.add('dropdown-menu-end');
                    dropdown.classList.add('dropstart');
                }
                
                // Si está muy abajo, abrir hacia arriba
                if (relativeY > containerHeight * 0.7) {
                    dropdown.classList.add('dropup');
                }
                
                // Si está muy a la izquierda, abrir hacia la derecha
                if (relativeX < containerWidth * 0.3) {
                    menu.classList.add('dropdown-menu-start');
                    dropdown.classList.add('dropend');
                }
            }, 10);
        });
    });
});
</script>
@endsection 