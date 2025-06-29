@extends('layouts.panel')

@section('title', 'Expediente Clínico')
@section('breadcrumb', 'Expedientes Clínicos / Ver')

@section('content')
<div class="container-fluid py-4">
    <div class="row">
        <div class="col-12">
            <div class="card mb-4">
                <div class="card-header pb-0 bg-info">
                    <div class="row align-items-center">
                        <div class="col-md-8">
                            <h6 class="text-white mb-0">Expediente: {{ $clinicalRecord->record_number }}</h6>
                            <p class="text-sm text-white opacity-8 mb-0">
                                {{ $clinicalRecord->full_name }} | CUI: {{ $clinicalRecord->cui }}
                            </p>
                        </div>
                        <div class="col-md-4 text-end">
                            <a href="{{ route('clinical-records.index') }}" class="btn btn-sm btn-white">
                                <i class="fas fa-chevron-left me-2"></i>Regresar
                            </a>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <!-- Pestañas -->
                    <ul class="nav nav-tabs" id="recordTabs" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active" id="histories-tab" data-bs-toggle="tab" data-bs-target="#histories" type="button" role="tab" aria-controls="histories" aria-selected="true">
                                <i class="fas fa-notes-medical me-2"></i>Historias Clínicas
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="appointments-tab" data-bs-toggle="tab" data-bs-target="#appointments" type="button" role="tab" aria-controls="appointments" aria-selected="false">
                                <i class="fas fa-calendar-alt me-2"></i>Citas Médicas
                            </button>
                        </li>
                    </ul>

                    <div class="tab-content" id="recordTabsContent">
                        <!-- Pestaña de Historias Clínicas -->
                        <div class="tab-pane fade show active" id="histories" role="tabpanel" aria-labelledby="histories-tab">
                            <div class="d-flex justify-content-end my-3">
                                <a href="{{ route('medical-consultations.create', ['clinical_record_id' => $clinicalRecord->id]) }}" class="btn btn-success">
                                    <i class="fas fa-notes-medical me-2"></i>Crear Historia Clínica
                                </a>
                            </div>
                            <div class="table-responsive p-0">
                                <table class="table align-items-center mb-0">
                                    <thead>
                                        <tr>
                                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-3">Fecha</th>
                                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-3">Médico</th>
                                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-3">Especialidad</th>
                                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-3">Motivo</th>
                                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-3">Tipo de Atención</th>
                                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-3">Estado</th>
                                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-3">Estado Final</th>
                                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-3 text-center">Acciones</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($clinicalRecord->medicalConsultations->sortByDesc('consultation_date') as $history)
                                        <tr>
                                            <td class="px-3 py-2">
                                                <span class="text-sm text-secondary mb-0">{{ $history->consultation_date->format('d/m/Y H:i') }}</span>
                                            </td>
                                            <td class="px-3 py-2">
                                                <span class="text-sm text-secondary mb-0">{{ $history->doctor->full_name ?? '-' }}</span>
                                            </td>
                                            <td class="px-3 py-2">
                                                <span class="text-sm text-secondary mb-0">{{ $history->specialty->name ?? '-' }}</span>
                                            </td>
                                            <td class="px-3 py-2">
                                                <span class="text-sm text-secondary mb-0">{{ $history->consultation_reason }}</span>
                                            </td>
                                            <td class="px-3 py-2">
                                                @php
                                                    $attentionType = strtolower($history->attention_type ?? '');
                                                    $badgeClass = match($attentionType) {
                                                        'emergencia' => 'bg-danger',
                                                        'consulta_externa' => 'bg-success',
                                                        default => 'bg-light text-dark',
                                                    };
                                                @endphp
                                                <span class="badge {{ $badgeClass }}">{{ $history->getAttentionTypeLabel() }}</span>
                                            </td>
                                            <td class="px-3 py-2">
                                                @php
                                                    $status = strtolower($history->status ?? '');
                                                    $badgeClass = match($status) {
                                                        'abierta' => 'bg-primary',
                                                        'en_proceso' => 'bg-warning',
                                                        'finalizada' => 'bg-success',
                                                        'cancelada' => 'bg-secondary',
                                                        default => 'bg-light text-dark',
                                                    };
                                                @endphp
                                                <span class="badge {{ $badgeClass }}">{{ ucfirst($history->status ?? '-') }}</span>
                                            </td>
                                            <td class="px-3 py-2">
                                                @if($history->final_status)
                                                    @php
                                                        $finalStatus = strtolower($history->final_status ?? '');
                                                        $badgeClass = match($finalStatus) {
                                                            'hospitalizado' => 'bg-info',
                                                            'egresado' => 'bg-success',
                                                            default => 'bg-light text-dark',
                                                        };
                                                    @endphp
                                                    <span class="badge {{ $badgeClass }}">{{ $history->getFinalStatusLabel() }}</span>
                                                @else
                                                    <span class="text-muted">-</span>
                                                @endif
                                            </td>
                                            <td class="align-middle text-center">
                                                <a href="{{ route('medical-consultations.show', $history->id) }}" class="btn btn-info btn-sm rounded-pill px-3 py-2 me-2">
                                                    <i class="fas fa-eye me-1"></i> Ver Detalle
                                                </a>
                                                <a href="{{ route('medical-consultations.print', $history->id) }}" class="btn btn-secondary btn-sm rounded-pill px-3 py-2" target="_blank">
                                                    <i class="fas fa-print me-1"></i> Imprimir
                                                </a>
                                            </td>
                                        </tr>
                                        @empty
                                        <tr>
                                            <td colspan="8" class="text-center py-4">
                                                <span class="text-muted">No hay historias clínicas registradas para este expediente.</span>
                                            </td>
                                        </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <!-- Pestaña de Citas Médicas -->
                        <div class="tab-pane fade" id="appointments" role="tabpanel" aria-labelledby="appointments-tab">
                            <div class="d-flex justify-content-end my-3">
                                <a href="{{ route('appointments.create', ['clinical_record_id' => $clinicalRecord->id]) }}" class="btn btn-info">
                                    <i class="fas fa-calendar-plus me-2"></i>Agendar Cita
                                </a>
                            </div>
                            <div class="table-responsive p-0">
                                <table class="table align-items-center mb-0">
                                    <thead>
                                        <tr>
                                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-3">N° Cita</th>
                                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-3">Fecha</th>
                                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-3">Médico</th>
                                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-3">Especialidad</th>
                                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-3">Tipo</th>
                                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-3">Estado</th>
                                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-3 text-center">Acciones</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($clinicalRecord->appointments->sortByDesc('appointment_date') as $appointment)
                                        <tr>
                                            <td class="px-3 py-2">
                                                <span class="text-sm font-weight-bold">{{ $appointment->appointment_number }}</span>
                                            </td>
                                            <td class="px-3 py-2">
                                                <span class="text-sm text-secondary mb-0">{{ $appointment->appointment_date->format('d/m/Y H:i') }}</span>
                                            </td>
                                            <td class="px-3 py-2">
                                                <span class="text-sm text-secondary mb-0">{{ $appointment->doctor->full_name ?? '-' }}</span>
                                            </td>
                                            <td class="px-3 py-2">
                                                <span class="text-sm text-secondary mb-0">{{ $appointment->specialty->name ?? '-' }}</span>
                                            </td>
                                            <td class="px-3 py-2">
                                                @php
                                                    $attentionType = strtolower($appointment->attention_type ?? '');
                                                    $badgeClass = match($attentionType) {
                                                        'emergencia' => 'bg-danger',
                                                        'urgencia' => 'bg-warning',
                                                        'consulta_externa' => 'bg-success',
                                                        default => 'bg-light text-dark',
                                                    };
                                                @endphp
                                                <span class="badge {{ $badgeClass }}">{{ ucwords(str_replace('_', ' ', $appointment->attention_type)) }}</span>
                                            </td>
                                            <td class="px-3 py-2">
                                                <span class="badge {{ $appointment->status_badge }}">{{ $appointment->status_text }}</span>
                                            </td>
                                                                                         <td class="align-middle text-center">
                                                <a href="{{ route('appointments.show', $appointment->id) }}" class="btn btn-info btn-sm rounded-pill px-3 py-2">
                                                    <i class="fas fa-eye me-1"></i> Ver Detalles
                                                </a>
                                            </td>
                                        </tr>
                                        @empty
                                        <tr>
                                            <td colspan="7" class="text-center py-4">
                                                <span class="text-muted">No hay citas médicas registradas para este expediente.</span>
                                            </td>
                                        </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal para actualizar estado de cita -->
<div class="modal fade" id="statusModal" tabindex="-1" aria-labelledby="statusModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="statusModalLabel">Actualizar Estado de Cita</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="statusForm" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="notes" class="form-label">Notas (opcional)</label>
                        <textarea class="form-control" id="notes" name="notes" rows="3"></textarea>
                    </div>
                    <div class="mb-3" id="cancelledReasonDiv" style="display: none;">
                        <label for="cancelled_reason" class="form-label">Motivo de cancelación</label>
                        <input type="text" class="form-control" id="cancelled_reason" name="cancelled_reason">
                    </div>
                    <input type="hidden" id="status" name="status">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary" id="confirmButton">Confirmar</button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
function updateStatus(appointmentId, status) {
    console.log('=== EXPEDIENTE - updateStatus LLAMADA ===');
    console.log('appointmentId:', appointmentId);
    console.log('status:', status);

    const statusTexts = {
        'confirmada': 'confirmar',
        'atendida': 'marcar como atendida',
        'perdida': 'marcar como perdida',
        'cancelada': 'cancelar'
    };

    const modalTitle = document.getElementById('statusModalLabel');
    const confirmButton = document.getElementById('confirmButton');
    const statusForm = document.getElementById('statusForm');
    const statusInput = document.getElementById('status');
    const cancelledReasonDiv = document.getElementById('cancelledReasonDiv');
    const cancelledReasonInput = document.getElementById('cancelled_reason');
    const notesInput = statusForm.querySelector('textarea[name="notes"]');

    modalTitle.textContent = `¿Desea ${statusTexts[status]} esta cita?`;
    confirmButton.textContent = 'Confirmar';
    statusForm.action = `{{ url('appointments') }}/${appointmentId}/status`;
    statusInput.value = status;

    console.log('Form action:', statusForm.action);
    console.log('Status configurado:', statusInput.value);

    // Limpiar notas anteriores
    if (notesInput) {
        notesInput.value = '';
    }

    // Mostrar/ocultar campo de motivo de cancelación
    if (status === 'cancelada') {
        cancelledReasonDiv.style.display = 'block';
        cancelledReasonInput.required = true;
        console.log('Campo cancelación mostrado');
    } else {
        cancelledReasonDiv.style.display = 'none';
        cancelledReasonInput.required = false;
        cancelledReasonInput.value = '';
        console.log('Campo cancelación ocultado');
    }

    // Mostrar modal
    const modal = new bootstrap.Modal(document.getElementById('statusModal'));
    modal.show();
}

// Manejar envío del formulario
document.addEventListener('DOMContentLoaded', function() {
    document.getElementById('statusForm').addEventListener('submit', function(e) {
        e.preventDefault();
        
        console.log('=== EXPEDIENTE - FORMULARIO ENVIADO ===');
        
        const statusInput = document.getElementById('status');
        const cancelReasonInput = document.getElementById('cancelled_reason');
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
        
        console.log('=== EXPEDIENTE - DATOS A ENVIAR ===');
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

// Las funciones de toast ahora están globalmente disponibles en el layout
</script>
@endpush 