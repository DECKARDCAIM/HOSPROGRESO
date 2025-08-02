@extends('layouts.panel')

@section('title', 'Archivo Clínico')
@section('breadcrumb', 'Archivo Clínico / Expedientes Recientes')

@section('content')
<div class="container-fluid py-4">
    <div class="row">
        <div class="col-12">
            <div class="card mb-4">
                <div class="card-header pb-0 bg-gradient-primary">
                    <div class="row align-items-center">
                        <div class="col-md-8">
                            <h6 class="text-white mb-0">
                                <i class="fas fa-archive me-2"></i>
                                Archivo Clínico - Expedientes Recientes
                            </h6>
                            <p class="text-sm text-white opacity-8 mb-0">
                                Gestión de expedientes para archivo físico
                            </p>
                        </div>
                        <div class="col-md-4 text-end">
                            <a href="{{ route('clinical-file.archived') }}" class="btn btn-sm btn-white me-2">
                                <i class="fas fa-box me-2"></i>Ver Archivados
                            </a>
                        </div>
                    </div>
                </div>

                <div class="card-body">
                    <!-- Estadísticas -->
                    <div class="row mb-4">
                        <div class="col-md-3">
                            <div class="card bg-gradient-info">
                                <div class="card-body text-center text-white p-3">
                                    <i class="fas fa-file-medical fa-2x mb-2"></i>
                                    <h4 class="mb-1">{{ count($recentRecords) }}</h4>
                                    <p class="mb-0 text-sm">Expedientes Recientes</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-gradient-warning">
                                <div class="card-body text-center text-white p-3">
                                    <i class="fas fa-print fa-2x mb-2"></i>
                                    <h4 class="mb-1">{{ $recentRecords->where('tracking.is_printed', false)->count() }}</h4>
                                    <p class="mb-0 text-sm">Sin Imprimir</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-gradient-success">
                                <div class="card-body text-center text-white p-3">
                                    <i class="fas fa-check fa-2x mb-2"></i>
                                    <h4 class="mb-1">{{ $recentRecords->where('tracking.is_printed', true)->count() }}</h4>
                                    <p class="mb-0 text-sm">Impresos</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-gradient-secondary">
                                <div class="card-body text-center text-white p-3">
                                    <i class="fas fa-archive fa-2x mb-2"></i>
                                    <h4 class="mb-1">{{ $recentRecords->where('tracking.is_archived', false)->count() }}</h4>
                                    <p class="mb-0 text-sm">Pendientes Archivo</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Tabla de Expedientes -->
                    <div class="table-responsive">
                        <table class="table align-items-center mb-0" id="clinicalFileTable">
                            <thead>
                                <tr>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Expediente</th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Paciente</th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Fecha Creación</th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Estado Impresión</th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Número Anterior</th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 text-center">Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($recentRecords as $record)
                                <tr>
                                    <td>
                                        <div class="d-flex px-2 py-1">
                                            <div class="avatar avatar-sm me-3 {{ $record->tracking->is_printed ? 'bg-success' : 'bg-warning' }} rounded-circle">
                                                <span class="text-white font-weight-bold">{{ substr($record->first_name, 0, 1) }}</span>
                                            </div>
                                            <div class="d-flex flex-column justify-content-center">
                                                <h6 class="mb-0 text-sm">{{ $record->record_number }}</h6>
                                                <p class="text-xs text-secondary mb-0">ID: {{ $record->id }}</p>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="d-flex flex-column">
                                            <h6 class="mb-0 text-sm">{{ $record->full_name }}</h6>
                                            <p class="text-xs text-secondary mb-0">{{ $record->cui }}</p>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="text-secondary text-xs">{{ $record->created_at->format('d/m/Y H:i') }}</span>
                                    </td>
                                    <td>
                                        @if($record->tracking->is_printed)
                                            <span class="badge badge-sm bg-gradient-success">
                                                <i class="fas fa-check me-1"></i>Impreso
                                            </span>
                                            <p class="text-xs text-secondary mb-0">
                                                {{ $record->tracking->printed_at ? $record->tracking->printed_at->format('d/m/Y H:i') : '' }}
                                            </p>
                                        @else
                                            <span class="badge badge-sm bg-gradient-warning">
                                                <i class="fas fa-clock me-1"></i>Pendiente
                                            </span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($record->old_registration_number)
                                            <span class="badge badge-sm bg-gradient-info">{{ $record->old_registration_number }}</span>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td class="align-middle text-center">
                                        <div class="btn-group" role="group">
                                            <a href="{{ route('clinical-file.show', $record->id) }}" 
                                               class="btn btn-primary btn-sm"
                                               data-bs-toggle="tooltip" 
                                               title="Ver detalles completos">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            
                                            @if(!$record->tracking->is_printed)
                                                <button class="btn btn-warning btn-sm" 
                                                        onclick="markAsPrinted({{ $record->id }})"
                                                        data-bs-toggle="tooltip" 
                                                        title="Marcar como impreso">
                                                    <i class="fas fa-print"></i>
                                                </button>
                                            @endif
                                            
                                            @if(!$record->tracking->is_archived)
                                                <button class="btn btn-success btn-sm" 
                                                        onclick="showArchiveModal({{ $record->id }})"
                                                        data-bs-toggle="tooltip" 
                                                        title="Archivar expediente">
                                                    <i class="fas fa-archive"></i>
                                                </button>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="6" class="text-center py-4">
                                        <span class="text-muted">No hay expedientes recientes para procesar.</span>
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

// Función para mostrar toasts (debe existir en el sistema)
function showToast(type, title, message) {
    // Implementar según el sistema de notificaciones del proyecto
    alert(`${title}: ${message}`);
}
</script>
@endpush