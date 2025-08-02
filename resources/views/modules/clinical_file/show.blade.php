@extends('layouts.panel')

@section('title', 'Detalles del Expediente')
@section('breadcrumb', 'Archivo Clínico / Detalles')

@section('content')
<div class="container-fluid py-4">
    <div class="row">
        <div class="col-12">
            <div class="card mb-4">
                <div class="card-header pb-0 bg-gradient-info">
                    <div class="row align-items-center">
                        <div class="col-md-8">
                            <h6 class="text-white mb-0">
                                <i class="fas fa-file-medical me-2"></i>
                                Expediente: {{ $clinicalRecord->record_number }}
                            </h6>
                            <p class="text-sm text-white opacity-8 mb-0">
                                Información completa del paciente y seguimiento de archivo
                            </p>
                        </div>
                        <div class="col-md-4 text-end">
                            <a href="{{ route('clinical-file.index') }}" class="btn btn-sm btn-white me-2">
                                <i class="fas fa-arrow-left me-2"></i>Regresar
                            </a>
                        </div>
                    </div>
                </div>

                <div class="card-body">
                    <!-- Estado del Expediente -->
                    <div class="row mb-4">
                        <div class="col-12">
                            <div class="alert {{ $tracking->is_archived ? 'alert-success' : 'alert-warning' }}">
                                <div class="row align-items-center">
                                    <div class="col-md-8">
                                        <h5 class="mb-1">
                                            @if($tracking->is_archived)
                                                <i class="fas fa-archive me-2"></i>Expediente Archivado
                                            @else
                                                <i class="fas fa-clock me-2"></i>Expediente Activo
                                            @endif
                                        </h5>
                                        <p class="mb-0">
                                            @if($tracking->is_archived)
                                                Archivado el {{ $tracking->archived_at->format('d/m/Y H:i') }}
                                                @if($tracking->archivedBy)
                                                    por {{ $tracking->archivedBy->name }}
                                                @endif
                                            @else
                                                Estado: {{ $tracking->is_printed ? 'Impreso' : 'Pendiente de impresión' }}
                                            @endif
                                        </p>
                                    </div>
                                    <div class="col-md-4 text-end">
                                        @if(!$tracking->is_archived)
                                            @if(!$tracking->is_printed)
                                                <button class="btn btn-warning me-2" onclick="markAsPrinted({{ $clinicalRecord->id }})">
                                                    <i class="fas fa-print me-2"></i>Marcar Impreso
                                                </button>
                                            @endif
                                            <button class="btn btn-success" onclick="showArchiveModal({{ $clinicalRecord->id }})">
                                                <i class="fas fa-archive me-2"></i>Archivar
                                            </button>
                                        @else
                                            <button class="btn btn-warning" onclick="reactivateRecord({{ $clinicalRecord->id }})">
                                                <i class="fas fa-undo me-2"></i>Reactivar
                                            </button>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Información del Paciente -->
                    <div class="row">
                        <div class="col-md-6">
                            <div class="card mb-4">
                                <div class="card-header">
                                    <h6 class="mb-0">
                                        <i class="fas fa-user me-2"></i>
                                        Datos Personales
                                    </h6>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-12 mb-3">
                                            <strong>Nombre Completo:</strong><br>
                                            {{ $clinicalRecord->full_name }}
                                        </div>
                                        <div class="col-6 mb-3">
                                            <strong>CUI:</strong><br>
                                            {{ $clinicalRecord->cui }}
                                        </div>
                                        <div class="col-6 mb-3">
                                            <strong>Sexo:</strong><br>
                                            {{ $clinicalRecord->sex->name ?? '-' }}
                                        </div>
                                        <div class="col-6 mb-3">
                                            <strong>Estado Civil:</strong><br>
                                            {{ $clinicalRecord->civilStatus->name ?? '-' }}
                                        </div>
                                        <div class="col-6 mb-3">
                                            <strong>Fecha de Nacimiento:</strong><br>
                                            {{ $clinicalRecord->birth_date ? $clinicalRecord->birth_date->format('d/m/Y') : '-' }}
                                            @if($clinicalRecord->birth_date)
                                                ({{ $clinicalRecord->age }} años)
                                            @endif
                                        </div>
                                        <div class="col-6 mb-3">
                                            <strong>Comunidad Lingüística:</strong><br>
                                            {{ $clinicalRecord->linguisticCommunity->name ?? '-' }}
                                        </div>
                                        <div class="col-6 mb-3">
                                            <strong>Etnia:</strong><br>
                                            {{ $clinicalRecord->ethnicity->name ?? '-' }}
                                        </div>
                                        @if($clinicalRecord->education)
                                        <div class="col-6 mb-3">
                                            <strong>Educación:</strong><br>
                                            {{ $clinicalRecord->education }}
                                        </div>
                                        @endif
                                        @if($clinicalRecord->occupation)
                                        <div class="col-6 mb-3">
                                            <strong>Ocupación:</strong><br>
                                            {{ $clinicalRecord->occupation }}
                                        </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="card mb-4">
                                <div class="card-header">
                                    <h6 class="mb-0">
                                        <i class="fas fa-map-marker-alt me-2"></i>
                                        Ubicación
                                    </h6>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-12 mb-3">
                                            <strong>País:</strong><br>
                                            {{ $clinicalRecord->country->name ?? '-' }}
                                        </div>
                                        <div class="col-12 mb-3">
                                            <strong>Departamento:</strong><br>
                                            {{ $clinicalRecord->department->name ?? '-' }}
                                        </div>
                                        <div class="col-12 mb-3">
                                            <strong>Municipio:</strong><br>
                                            {{ $clinicalRecord->municipality->name ?? '-' }}
                                        </div>
                                        @if($clinicalRecord->specific_residence)
                                        <div class="col-12 mb-3">
                                            <strong>Residencia Específica:</strong><br>
                                            {{ $clinicalRecord->specific_residence }}
                                        </div>
                                        @endif
                                    </div>
                                </div>
                            </div>

                            <!-- Información de Números de Registro -->
                            <div class="card mb-4">
                                <div class="card-header">
                                    <h6 class="mb-0">
                                        <i class="fas fa-hashtag me-2"></i>
                                        Números de Registro
                                    </h6>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-12 mb-3">
                                            <strong>Número de Expediente Actual:</strong><br>
                                            <span class="badge badge-lg bg-gradient-primary">{{ $clinicalRecord->record_number }}</span>
                                        </div>
                                        @if($oldRegistrationNumber)
                                        <div class="col-12 mb-3">
                                            <strong>Número de Registro Anterior:</strong><br>
                                            <span class="badge badge-lg bg-gradient-info">{{ $oldRegistrationNumber }}</span>
                                            <small class="text-muted d-block mt-1">Del sistema anterior (Excel)</small>
                                        </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Discapacidades y Alergias -->
                    <div class="row">
                        @if($clinicalRecord->disabilities->count() > 0)
                        <div class="col-md-6">
                            <div class="card mb-4">
                                <div class="card-header">
                                    <h6 class="mb-0">
                                        <i class="fas fa-wheelchair me-2"></i>
                                        Discapacidades
                                    </h6>
                                </div>
                                <div class="card-body">
                                    @foreach($clinicalRecord->disabilities as $disability)
                                        <span class="badge badge-warning me-2 mb-2">{{ $disability->name }}</span>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                        @endif

                        @if($clinicalRecord->allergies->count() > 0)
                        <div class="col-md-6">
                            <div class="card mb-4">
                                <div class="card-header">
                                    <h6 class="mb-0">
                                        <i class="fas fa-exclamation-triangle me-2"></i>
                                        Alergias
                                    </h6>
                                </div>
                                <div class="card-body">
                                    @foreach($clinicalRecord->allergies as $allergy)
                                        <span class="badge badge-danger me-2 mb-2">{{ $allergy->name }}</span>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                        @endif
                    </div>

                    <!-- Consultas Médicas -->
                    @if($clinicalRecord->medicalConsultations->count() > 0)
                    <div class="card mb-4">
                        <div class="card-header">
                            <h6 class="mb-0">
                                <i class="fas fa-stethoscope me-2"></i>
                                Consultas Médicas ({{ $clinicalRecord->medicalConsultations->count() }})
                            </h6>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-sm">
                                    <thead>
                                        <tr>
                                            <th>Fecha</th>
                                            <th>Médico</th>
                                            <th>Especialidad</th>
                                            <th>Tipo Control</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($clinicalRecord->medicalConsultations as $consultation)
                                        <tr>
                                            <td>{{ $consultation->created_at->format('d/m/Y H:i') }}</td>
                                            <td>{{ $consultation->doctor->full_name ?? '-' }}</td>
                                            <td>{{ $consultation->doctor->specialty->name ?? '-' }}</td>
                                            <td>{{ $consultation->controlType->name ?? '-' }}</td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    @endif

                    <!-- Citas -->
                    @if($clinicalRecord->appointments->count() > 0)
                    <div class="card mb-4">
                        <div class="card-header">
                            <h6 class="mb-0">
                                <i class="fas fa-calendar-alt me-2"></i>
                                Citas Programadas ({{ $clinicalRecord->appointments->count() }})
                            </h6>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-sm">
                                    <thead>
                                        <tr>
                                            <th>Fecha</th>
                                            <th>Hora</th>
                                            <th>Médico</th>
                                            <th>Especialidad</th>
                                            <th>Estado</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($clinicalRecord->appointments as $appointment)
                                        <tr>
                                            <td>{{ $appointment->appointment_date ? $appointment->appointment_date->format('d/m/Y') : '-' }}</td>
                                            <td>{{ $appointment->appointment_time ?? '-' }}</td>
                                            <td>{{ $appointment->doctor->full_name ?? '-' }}</td>
                                            <td>{{ $appointment->doctor->specialty->name ?? '-' }}</td>
                                            <td>
                                                <span class="badge badge-sm bg-gradient-{{ $appointment->status === 'confirmed' ? 'success' : ($appointment->status === 'cancelled' ? 'danger' : 'warning') }}">
                                                    {{ ucfirst($appointment->status ?? 'pending') }}
                                                </span>
                                            </td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    @endif

                    <!-- Acciones -->
                    <div class="row">
                        <div class="col-12">
                            <div class="d-flex justify-content-end">
                                <a href="{{ route('clinical-records.print', $clinicalRecord->id) }}" 
                                   class="btn btn-secondary me-2" 
                                   target="_blank">
                                    <i class="fas fa-print me-2"></i>Imprimir Expediente
                                </a>
                                <a href="{{ route('clinical-records.edit', $clinicalRecord) }}" 
                                   class="btn btn-info">
                                    <i class="fas fa-edit me-2"></i>Editar Expediente
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal para Archivar -->
<div class="modal fade" id="archiveModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fas fa-archive me-2"></i>
                    Archivar Expediente
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="archiveForm">
                    <input type="hidden" id="archive_record_id">
                    <div class="form-group">
                        <label for="archive_notes" class="form-label">Notas (opcional)</label>
                        <textarea class="form-control" id="archive_notes" rows="3" placeholder="Observaciones sobre el archivado..."></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-success" onclick="confirmArchive()">
                    <i class="fas fa-archive me-2"></i>Archivar
                </button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
// Marcar como impreso
function markAsPrinted(recordId) {
    if (confirm('¿Confirma que este expediente ya fue impreso físicamente?')) {
        fetch(`/clinical-file/${recordId}/mark-printed`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showToast('success', 'Éxito', data.message);
                setTimeout(() => window.location.reload(), 1500);
            } else {
                showToast('error', 'Error', data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showToast('error', 'Error', 'Error al procesar la solicitud');
        });
    }
}

// Mostrar modal de archivo
function showArchiveModal(recordId) {
    document.getElementById('archive_record_id').value = recordId;
    document.getElementById('archive_notes').value = '';
    const modal = new bootstrap.Modal(document.getElementById('archiveModal'));
    modal.show();
}

// Confirmar archivo
function confirmArchive() {
    const recordId = document.getElementById('archive_record_id').value;
    const notes = document.getElementById('archive_notes').value;
    
    fetch(`/clinical-file/${recordId}/archive`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({ notes: notes })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showToast('success', 'Éxito', data.message);
            const modal = bootstrap.Modal.getInstance(document.getElementById('archiveModal'));
            modal.hide();
            setTimeout(() => window.location.reload(), 1500);
        } else {
            showToast('error', 'Error', data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showToast('error', 'Error', 'Error al procesar la solicitud');
    });
}

// Reactivar expediente
function reactivateRecord(recordId) {
    if (confirm('¿Está seguro de que desea reactivar este expediente? Volverá a la lista de expedientes recientes.')) {
        fetch(`/clinical-file/${recordId}/reactivate`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showToast('success', 'Éxito', data.message);
                setTimeout(() => window.location.href = '/clinical-file', 1500);
            } else {
                showToast('error', 'Error', data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showToast('error', 'Error', 'Error al procesar la solicitud');
        });
    }
}

// Función para mostrar toasts
function showToast(type, title, message) {
    alert(`${title}: ${message}`);
}
</script>
@endpush