@extends('layouts.panel')

@section('title', 'Expedientes Archivados')
@section('breadcrumb', 'Archivo Clínico / Expedientes Archivados')

@section('content')
<div class="container-fluid py-4">
    <div class="row">
        <div class="col-12">
            <div class="card mb-4">
                <div class="card-header pb-0 bg-gradient-success">
                    <div class="row align-items-center">
                        <div class="col-md-8">
                            <h6 class="text-white mb-0">
                                <i class="fas fa-box me-2"></i>
                                Expedientes Archivados
                            </h6>
                            <p class="text-sm text-white opacity-8 mb-0">
                                Expedientes que ya han sido archivados físicamente
                            </p>
                        </div>
                        <div class="col-md-4 text-end">
                            <a href="{{ route('clinical-file.index') }}" class="btn btn-sm btn-white me-2">
                                <i class="fas fa-arrow-left me-2"></i>Ver Recientes
                            </a>
                        </div>
                    </div>
                </div>

                <div class="card-body">
                    <!-- Filtros -->
                    <div class="row mb-4">
                        <div class="col-md-4">
                            <label class="form-label">Buscar expediente</label>
                            <input type="text" class="form-control" id="searchInput" placeholder="Buscar por número, nombre o CUI...">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Fecha archivo desde</label>
                            <input type="date" class="form-control" id="dateFrom">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Fecha archivo hasta</label>
                            <input type="date" class="form-control" id="dateTo">
                        </div>
                        <div class="col-md-2 d-flex align-items-end">
                            <button class="btn btn-primary w-100" onclick="filterRecords()">
                                <i class="fas fa-search me-2"></i>Filtrar
                            </button>
                        </div>
                    </div>

                    <!-- Estadísticas -->
                    <div class="row mb-4">
                        <div class="col-md-12">
                            <div class="alert alert-info">
                                <i class="fas fa-info-circle me-2"></i>
                                <strong>Total de expedientes archivados:</strong> {{ count($archivedRecords) }}
                            </div>
                        </div>
                    </div>

                    <!-- Tabla de Expedientes Archivados -->
                    <div class="table-responsive">
                        <table class="table align-items-center mb-0" id="archivedTable">
                            <thead>
                                <tr>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Expediente</th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Paciente</th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Fecha Creación</th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Fecha Archivo</th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Número Anterior</th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Notas</th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 text-center">Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($archivedRecords as $record)
                                <tr>
                                    <td>
                                        <div class="d-flex px-2 py-1">
                                            <div class="avatar avatar-sm me-3 bg-success rounded-circle">
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
                                        <div class="d-flex flex-column">
                                            <span class="text-success text-xs font-weight-bold">
                                                {{ $record->tracking->archived_at ? $record->tracking->archived_at->format('d/m/Y H:i') : '-' }}
                                            </span>
                                            @if($record->tracking->archivedBy)
                                                <span class="text-xs text-secondary">
                                                    por {{ $record->tracking->archivedBy->name }}
                                                </span>
                                            @endif
                                        </div>
                                    </td>
                                    <td>
                                        @if($record->old_registration_number)
                                            <span class="badge badge-sm bg-gradient-info">{{ $record->old_registration_number }}</span>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($record->notes)
                                            <span class="text-xs" data-bs-toggle="tooltip" title="{{ $record->notes }}">
                                                {{ Str::limit($record->notes, 30) }}
                                            </span>
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
                                            
                                            <a href="{{ route('clinical-records.print', $record->id) }}" 
                                               class="btn btn-secondary btn-sm" 
                                               target="_blank"
                                               data-bs-toggle="tooltip" 
                                               title="Reimprimir expediente">
                                                <i class="fas fa-print"></i>
                                            </a>
                                            
                                            <button class="btn btn-warning btn-sm" 
                                                    onclick="reactivateRecord({{ $record->id }})"
                                                    data-bs-toggle="tooltip" 
                                                    title="Reactivar expediente">
                                                <i class="fas fa-undo"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="7" class="text-center py-4">
                                        <span class="text-muted">No hay expedientes archivados.</span>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Información adicional -->
                    <div class="row mt-4">
                        <div class="col-12">
                            <div class="alert alert-light">
                                <h6><i class="fas fa-info-circle me-2"></i>Información sobre expedientes archivados:</h6>
                                <ul class="mb-0">
                                    <li><strong>Ver detalles:</strong> Permite revisar toda la información del expediente</li>
                                    <li><strong>Reimprimir:</strong> Genera nuevamente el PDF del expediente</li>
                                    <li><strong>Reactivar:</strong> Devuelve el expediente a la lista de recientes</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
// Filtrar registros
function filterRecords() {
    const searchTerm = document.getElementById('searchInput').value.toLowerCase();
    const dateFrom = document.getElementById('dateFrom').value;
    const dateTo = document.getElementById('dateTo').value;
    
    const table = document.getElementById('archivedTable');
    const rows = table.querySelectorAll('tbody tr');
    
    rows.forEach(row => {
        if (row.cells.length === 1) return; // Skip empty row
        
        const recordNumber = row.cells[0].textContent.toLowerCase();
        const patientName = row.cells[1].textContent.toLowerCase();
        const archivedDate = row.cells[3].textContent.trim();
        
        let showRow = true;
        
        // Filter by search term
        if (searchTerm && !recordNumber.includes(searchTerm) && !patientName.includes(searchTerm)) {
            showRow = false;
        }
        
        // Filter by date range (simplified - you might want more robust date parsing)
        if (dateFrom || dateTo) {
            // This is a simplified date check - implement proper date parsing if needed
            if (dateFrom && archivedDate < dateFrom) showRow = false;
            if (dateTo && archivedDate > dateTo) showRow = false;
        }
        
        row.style.display = showRow ? '' : 'none';
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

// Función para mostrar toasts
function showToast(type, title, message) {
    alert(`${title}: ${message}`);
}

// Event listeners para filtros en tiempo real
document.getElementById('searchInput').addEventListener('input', filterRecords);
document.getElementById('dateFrom').addEventListener('change', filterRecords);
document.getElementById('dateTo').addEventListener('change', filterRecords);
</script>
@endpush