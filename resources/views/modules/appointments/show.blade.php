@extends('layouts.panel')

@section('title', 'Detalles de Cita')
@section('breadcrumb', 'Citas / Detalles')

@section('content')
<div class="container-fluid py-4">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header pb-0 bg-info">
                    <div class="row align-items-center">
                        <div class="col-md-8">
                            <h6 class="text-white mb-0">Detalles de Cita - {{ $appointment->appointment_number }}</h6>
                            <p class="text-sm text-white opacity-8 mb-0">
                                Información completa de la cita médica
                            </p>
                        </div>
                        <div class="col-md-4 text-end">
                            <a href="{{ route('appointments.index') }}" class="btn btn-sm btn-white me-2">
                                <i class="fas fa-arrow-left me-2"></i>Volver al listado
                            </a>
                            <a href="{{ route('appointments.print', $appointment) }}" class="btn btn-sm btn-white" target="_blank">
                                <i class="fas fa-print me-2"></i>Imprimir PDF
                            </a>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <!-- Información general de la cita -->
                        <div class="col-lg-8">
                            <div class="card">
                                <div class="card-header">
                                    <h6><i class="fas fa-calendar-alt me-2"></i>Información de la Cita</h6>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <p class="mb-2"><strong>Número de Cita:</strong> {{ $appointment->appointment_number }}</p>
                                            <p class="mb-2"><strong>Fecha y Hora:</strong> {{ $appointment->appointment_date->format('d/m/Y H:i') }}</p>
                                            <p class="mb-2"><strong>Turno:</strong> {{ $appointment->slot_number ?? '1' }}</p>
                                            <p class="mb-2"><strong>Tipo de Atención:</strong> 
                                                <span class="badge bg-secondary">{{ ucfirst(str_replace('_', ' ', $appointment->attention_type)) }}</span>
                                            </p>
                                        </div>
                                        <div class="col-md-6">
                                            <p class="mb-2"><strong>Estado:</strong> 
                                                <span class="badge {{ $appointment->status_badge }}">{{ $appointment->status_text }}</span>
                                            </p>
                                            <p class="mb-2"><strong>Creada por:</strong> {{ $appointment->createdBy->name ?? '-' }}</p>
                                            <p class="mb-2"><strong>Fecha de Creación:</strong> {{ $appointment->created_at->format('d/m/Y H:i') }}</p>
                                            @if($appointment->confirmed_at)
                                                <p class="mb-2"><strong>Confirmada:</strong> {{ $appointment->confirmed_at->format('d/m/Y H:i') }}</p>
                                            @endif
                                        </div>
                                    </div>
                                    
                                    @if($appointment->notes)
                                        <div class="mt-3">
                                            <strong>Notas:</strong>
                                            <p class="text-muted mb-0">{{ $appointment->notes }}</p>
                                        </div>
                                    @endif
                                </div>
                            </div>

                            <!-- Información del paciente -->
                            <div class="card mt-4">
                                <div class="card-header">
                                    <h6><i class="fas fa-user me-2"></i>Información del Paciente</h6>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <p class="mb-2"><strong>Nombre Completo:</strong> {{ $appointment->clinicalRecord->full_name }}</p>
                                            <p class="mb-2"><strong>CUI:</strong> {{ $appointment->clinicalRecord->cui }}</p>
                                            <p class="mb-2"><strong>N° Expediente:</strong> {{ $appointment->clinicalRecord->record_number }}</p>
                                            <p class="mb-2"><strong>Edad:</strong> {{ $appointment->clinicalRecord->age }} años</p>
                                        </div>
                                        <div class="col-md-6">
                                            <p class="mb-2"><strong>Sexo:</strong> {{ $appointment->clinicalRecord->sex->name ?? '-' }}</p>
                                            <p class="mb-2"><strong>Fecha de Nacimiento:</strong> {{ $appointment->clinicalRecord->birth_date ? $appointment->clinicalRecord->birth_date->format('d/m/Y') : '-' }}</p>
                                            <p class="mb-2"><strong>Ubicación:</strong> 
                                                {{ $appointment->clinicalRecord->municipality->name ?? '-' }}, 
                                                {{ $appointment->clinicalRecord->department->name ?? '-' }}
                                            </p>
                                        </div>
                                    </div>
                                    <div class="mt-3">
                                        <a href="{{ route('clinical-records.show', $appointment->clinicalRecord) }}" class="btn btn-sm btn-outline-info">
                                            <i class="fas fa-eye me-2"></i>Ver Expediente Completo
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Panel de acciones -->
                        <div class="col-lg-4">
                            <div class="card">
                                <div class="card-header">
                                    <h6><i class="fas fa-cogs me-2"></i>Acciones Disponibles</h6>
                                </div>
                                <div class="card-body">
                                    <div class="d-grid gap-2">
                                        @if(in_array($appointment->status, ['pendiente', 'confirmada']))
                                            {{-- Citas pendientes o confirmadas - Acciones normales --}}
                                            @if($appointment->status === 'pendiente')
                                                <button class="btn btn-info" onclick="updateStatus('{{ $appointment->id }}', 'confirmada')">
                                                    <i class="fas fa-check me-2"></i>Confirmar Cita
                                                </button>
                                            @endif
                                            
                                            <button class="btn btn-success" onclick="updateStatus('{{ $appointment->id }}', 'atendida')">
                                                <i class="fas fa-user-check me-2"></i>Marcar como Atendida
                                            </button>
                                            
                                            <button class="btn btn-secondary" onclick="updateStatus('{{ $appointment->id }}', 'perdida')">
                                                <i class="fas fa-user-slash me-2"></i>Marcar como Perdida
                                            </button>
                                            
                                            <a href="{{ route('appointments.create', ['clinical_record_id' => $appointment->clinical_record_id]) }}" class="btn btn-warning">
                                                <i class="fas fa-calendar-alt me-2"></i>Reagendar Cita
                                            </a>
                                            
                                            <button class="btn btn-danger" onclick="updateStatus('{{ $appointment->id }}', 'cancelada')">
                                                <i class="fas fa-times me-2"></i>Cancelar Cita
                                            </button>

                                        @elseif($appointment->status === 'perdida')
                                            {{-- Cita perdida - Solo reagendar --}}
                                            <div class="alert alert-warning">
                                                <i class="fas fa-exclamation-triangle me-2"></i>
                                                <strong>Cita Perdida:</strong> El paciente no se presentó a la cita.
                                            </div>
                                            <a href="{{ route('appointments.create', ['clinical_record_id' => $appointment->clinical_record_id]) }}" class="btn btn-warning btn-lg">
                                                <i class="fas fa-calendar-alt me-2"></i>Reagendar Cita
                                            </a>

                                        @elseif($appointment->status === 'atendida')
                                            {{-- Cita atendida - Agendar nueva --}}
                                            <div class="alert alert-success">
                                                <i class="fas fa-check-circle me-2"></i>
                                                <strong>Cita Atendida:</strong> El paciente fue atendido exitosamente.
                                            </div>
                                            <a href="{{ route('appointments.create', ['clinical_record_id' => $appointment->clinical_record_id]) }}" class="btn btn-success btn-lg">
                                                <i class="fas fa-plus-circle me-2"></i>Agendar Nueva Cita
                                            </a>

                                        @elseif($appointment->status === 'cancelada')
                                            {{-- Cita cancelada - Agendar nueva --}}
                                            <div class="alert alert-danger">
                                                <i class="fas fa-times-circle me-2"></i>
                                                <strong>Cita Cancelada:</strong> 
                                                @if($appointment->cancelled_reason)
                                                    {{ $appointment->cancelled_reason }}
                                                @else
                                                    Cita cancelada por el sistema.
                                                @endif
                                            </div>
                                            <a href="{{ route('appointments.create', ['clinical_record_id' => $appointment->clinical_record_id]) }}" class="btn btn-info btn-lg">
                                                <i class="fas fa-plus-circle me-2"></i>Agendar Nueva Cita
                                            </a>
                                        @endif
                                    </div>

                                    <hr>
                                    
                                    <div class="d-grid gap-2">
                                        <a href="{{ route('appointments.print', $appointment) }}" class="btn btn-outline-primary" target="_blank">
                                            <i class="fas fa-print me-2"></i>Imprimir PDF
                                        </a>
                                        
                                        @if($appointment->status !== 'atendida')
                                            {{-- Mostrar botón para ver expediente completo si no está atendida --}}
                                            <a href="{{ route('clinical-records.show', $appointment->clinicalRecord) }}" class="btn btn-outline-info">
                                                <i class="fas fa-folder-medical me-2"></i>Ver Expediente Completo
                                            </a>
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
                        <input type="text" name="cancelled_reason" class="form-control" placeholder="Especificar razón de cancelación">
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

<script>
function updateStatus(appointmentId, status) {

    
    const statusModal = new bootstrap.Modal(document.getElementById('statusModal'));
    const form = document.getElementById('statusForm');
    const statusInput = document.getElementById('statusInput');
    const cancelReasonDiv = document.getElementById('cancelReasonDiv');
    const cancelReasonInput = cancelReasonDiv.querySelector('input');
    const notesInput = form.querySelector('textarea[name="notes"]');
    
    form.action = `/appointments/${appointmentId}/status`;
    statusInput.value = status;
    
    
    
    // Limpiar notas anteriores
    if (notesInput) {
        notesInput.value = '';
    }
    
    // Mostrar campo de razón solo para cancelaciones
    if (status === 'cancelada') {
        cancelReasonDiv.style.display = 'block';
        cancelReasonInput.required = true;
    } else {
        cancelReasonDiv.style.display = 'none';
        cancelReasonInput.required = false;
        cancelReasonInput.value = '';
    }
    
    statusModal.show();
}

// Manejar envío del formulario
document.addEventListener('DOMContentLoaded', function() {
    document.getElementById('statusForm').addEventListener('submit', function(e) {
        e.preventDefault();
        
        const statusInput = document.getElementById('statusInput');
        const cancelReasonInput = this.querySelector('input[name="cancelled_reason"]');
        const notesInput = this.querySelector('textarea[name="notes"]');
        
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

        
        fetch(this.action, {
            method: 'POST',
            body: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            }
        })
        .then(response => {
            if (!response.ok) {
                return response.json().then(err => Promise.reject(err));
            }
            return response.json();
        })
        .then(data => {
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
</script>
@endsection
