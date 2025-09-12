@extends('layouts.panel')

@section('title', 'Reportes SIGSA 3H')
@section('breadcrumb', 'Reportes SIGSA 3H')

@push('styles')
<style>
.stats-section {
    margin-bottom: 2rem;
}
</style>
@endpush

@section('content')
<div class="container-fluid py-4">
    <!-- Estadísticas rápidas arriba -->
    <div class="row mb-4 stats-section justify-content-center">
        <div class="col-xl-4 col-md-4 col-12">
            <div class="card">
                <div class="card-body p-3">
                    <div class="row">
                        <div class="col-8">
                            <div class="numbers">
                                <p class="text-xs mb-0 text-capitalize font-weight-bold">Total Consultas</p>
                                <h5 class="font-weight-bolder mb-0" id="total-consultations">...</h5>
                            </div>
                        </div>
                        <div class="col-4 text-end">
                            <div class="icon icon-shape bg-gradient-dark shadow text-center border-radius-md">
                                <i class="bi bi-people text-lg opacity-10"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-4 col-md-4 col-12">
            <div class="card">
                <div class="card-body p-3">
                    <div class="row">
                        <div class="col-8">
                            <div class="numbers">
                                <p class="text-xs mb-0 text-capitalize font-weight-bold">Emergencias</p>
                                <h5 class="font-weight-bolder mb-0 text-danger" id="emergency-consultations">...</h5>
                            </div>
                        </div>
                        <div class="col-4 text-end">
                            <div class="icon icon-shape bg-gradient-danger shadow text-center border-radius-md">
                                <i class="bi bi-heart-pulse text-lg opacity-10"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-4 col-md-4 col-12">
            <div class="card">
                <div class="card-body p-3">
                    <div class="row">
                        <div class="col-8">
                            <div class="numbers">
                                <p class="text-xs mb-0 text-capitalize font-weight-bold">Consulta Externa</p>
                                <h5 class="font-weight-bolder mb-0 text-success" id="external-consultations">...</h5>
                            </div>
                        </div>
                        <div class="col-4 text-end">
                            <div class="icon icon-shape bg-gradient-success shadow text-center border-radius-md">
                                <i class="bi bi-heart-pulse text-lg opacity-10"></i>
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
                <div class="card-header pb-0 bg-brand-header">
                    <div class="row align-items-center">
                        <div class="col-md-8">
                            <h6 class="text-white mb-0">Reportes SIGSA 3H</h6>
                            <p class="text-sm text-white opacity-8 mb-0">
                                Sistema de Información Gerencial de Salud - Formato 3H Guatemala
                            </p>
                        </div>
                    </div>
                </div>
                <div class="card-body px-0 pt-0 pb-4">
                    <!-- Formulario de generación de reportes -->
                    <div class="px-3 pt-4 pb-3">
                        <div class="card border border-info">
                            <div class="card-header bg-light">
                                <h6 class="mb-0 text-info"><i class="bi bi-search me-2"></i>Filtros para Reporte SIGSA 3H</h6>
                            </div>
                            <div class="card-body">
                                <form method="POST" action="{{ route('reports.generate-sigsa') }}" id="reportForm">
                                    @csrf
                                    <div class="row">
                                        <!-- Fechas -->
                                        <div class="col-md-6">
                                            <div class="form-group mb-3">
                                                <label class="form-control-label mb-2">
                                                    <i class="bi bi-calendar text-info me-2"></i>Fecha Inicio
                                                </label>
                                                <input type="date" name="start_date" class="form-control border border-info" required 
                                                       value="{{ old('start_date', now()->startOfMonth()->format('Y-m-d')) }}">
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group mb-3">
                                                <label class="form-control-label mb-2">
                                                    <i class="bi bi-calendar text-info me-2"></i>Fecha Fin
                                                </label>
                                                <input type="date" name="end_date" class="form-control border border-info" required
                                                       value="{{ old('end_date', now()->format('Y-m-d')) }}">
                                            </div>
                                        </div>

                                        <!-- Tipo de Atención -->
                                        <div class="col-md-6">
                                            <div class="form-group mb-3">
                                                <label class="form-control-label mb-2">
                                                    <i class="fas fa-stethoscope text-info me-2"></i>Tipo de Atención
                                                </label>
                                                <select name="attention_type" class="form-control border border-info">
                                                    <option value="">Todos los tipos de atención</option>
                                                    <option value="emergencia" {{ old('attention_type') == 'emergencia' ? 'selected' : '' }}>
                                                        Solo Emergencias
                                                    </option>
                                                    <option value="consulta_externa" {{ old('attention_type') == 'consulta_externa' ? 'selected' : '' }}>
                                                        Solo Consulta Externa
                                                    </option>
                                                </select>
                                            </div>
                                        </div>

                                        <!-- Especialidad -->
                                        <div class="col-md-6">
                                            <div class="form-group mb-3">
                                                <label class="form-control-label mb-2">
                                                    <i class="fas fa-user-md text-info me-2"></i>Especialidad
                                                </label>
                                                <select name="specialty_id" class="form-control border border-info">
                                                    <option value="">Todas las especialidades</option>
                                                    @foreach($specialties as $specialty)
                                                        <option value="{{ $specialty->id }}" {{ old('specialty_id') == $specialty->id ? 'selected' : '' }}>
                                                            {{ $specialty->name }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>

                                        <!-- Tipo de Control -->
                                        <div class="col-md-6">
                                            <div class="form-group mb-3">
                                            </div>
                                        </div>

                                        <!-- Botones -->
                                        <div class="col-md-6 d-flex align-items-end">
                                            <div class="btn-group w-100" role="group">
                                                <button type="button" class="btn btn-outline-info" onclick="previewData()">
                                                    <i class="fas fa-eye me-1"></i> Vista Previa
                                                </button>
                                                <button type="submit" class="btn bg-brand-header text-white" id="generateBtn">
                                                    <i class="fas fa-file-excel me-1"></i> Generar Excel
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>

                    <!-- Vista previa de datos -->
                    <div class="px-3 mt-4" id="previewSection" style="display: none;">
                        <div class="card border border-info">
                            <div class="card-header bg-light">
                                <h6 class="mb-0 text-info">Vista Previa del Reporte</h6>
                            </div>
                            <div class="card-body">
                                <div id="previewContent">
                                    <!-- Contenido dinámico de vista previa -->
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Información adicional -->
                    <div class="px-3 mt-4">
                        <div class="alert alert-info border border-info">
                            <div class="row">
                                <div class="col-md-8 text-white">
                                    <h6 class="alert-heading">Información del Reporte SIGSA 3H</h6>
                                    <p class="mb-0">
                                        El reporte SIGSA 3H incluye todos los campos requeridos por el Ministerio de Salud de Guatemala:
                                        datos del paciente, tipo de atención, diagnósticos, tratamientos, referencias y contra-referencias.
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Contenedor de Toasts -->
<div class="toast-container position-fixed top-0 end-0 p-3" style="z-index: 9999;"></div>

@push('scripts')
<script>
// Cargar estadísticas al inicio
document.addEventListener('DOMContentLoaded', function() {
    loadStatistics();
    
    // Verificar si se generó un reporte y mostrar toast
    checkReportGenerated();
});

// Función para verificar si se generó un reporte
function checkReportGenerated() {
    // Verificar si hay datos de reporte en la sesión
    @if(session('report_generated'))
        const reportData = @json(session('report_generated'));
        if (reportData.success) {
            showSuccessToast(
                reportData.message,
                'Reporte Generado'
            );
        }
    @endif
    
    // También verificar via AJAX por si el usuario regresa después de la descarga
    fetch('{{ route("reports.show-toast") }}')
        .then(response => response.json())
        .then(data => {
            if (data.success && data.toast) {
                showToast(
                    data.toast.type,
                    data.toast.title,
                    data.toast.message
                );
            }
        })
        .catch(error => {
            console.log('No hay toast pendiente');
        });
}

// Función para cargar estadísticas
function loadStatistics() {
    fetch('{{ route("reports.statistics") }}')
        .then(response => response.json())
        .then(data => {
            document.getElementById('total-consultations').textContent = data.total_consultations;
            document.getElementById('emergency-consultations').textContent = data.emergency_consultations;
            document.getElementById('external-consultations').textContent = data.external_consultations;
        })
        .catch(error => {
            console.error('Error loading statistics:', error);
        });
}

// Función para mostrar toast de validación
function showValidationToast(message, type = 'warning') {
    const toastContainer = document.getElementById('toast-container') || createToastContainer();
    const toastId = 'validation-toast-' + Date.now();
    
    const toastHtml = `
        <div class="toast align-items-center text-white bg-${type} border-0" role="alert" aria-live="assertive" aria-atomic="true" id="${toastId}">
            <div class="d-flex">
                <div class="toast-body">
                    <strong><i class="bi bi-exclamation-triangle-fill me-2"></i>Validación</strong><br>
                    ${message}
                </div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
            </div>
        </div>
    `;
    
    toastContainer.insertAdjacentHTML('beforeend', toastHtml);
    
    const toast = new bootstrap.Toast(document.getElementById(toastId), {
        delay: 5000
    });
    toast.show();
    
    // Limpiar después de ocultar
    document.getElementById(toastId).addEventListener('hidden.bs.toast', function () {
        this.remove();
    });
}

function createToastContainer() {
    const container = document.createElement('div');
    container.id = 'toast-container';
    container.className = 'toast-container position-fixed top-0 end-0 p-3';
    container.style.zIndex = '9999';
    document.body.appendChild(container);
    return container;
}

// Función para vista previa
function previewData() {
    const form = document.getElementById('reportForm');
    const formData = new FormData(form);
    
    // Mostrar loading
    const previewSection = document.getElementById('previewSection');
    const previewContent = document.getElementById('previewContent');
    
    previewContent.innerHTML = `
        <div class="text-center">
            <div class="spinner-border text-info" role="status">
                <span class="visually-hidden">Cargando...</span>
            </div>
            <p class="mt-2 text-info">Generando vista previa...</p>
        </div>
    `;
    previewSection.style.display = 'block';
    
    fetch('{{ route("reports.preview") }}', {
        method: 'POST',
        body: formData,
        headers: {
            'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            let html = `
                <div class="alert alert-success border border-success mb-3">
                    <strong>📊 Total de registros encontrados: ${data.total_records}</strong>
                    <br>Mostrando los primeros ${data.preview_records} registros
                </div>
                <div class="table-responsive">
                    <table class="table table-sm border">
                                                    <thead class="bg-brand-header text-white">
                            <tr>
                                <th>Fecha</th>
                                <th>Paciente</th>
                                <th>CUI</th>
                                <th>Doctor</th>
                                <th>Especialidad</th>
                                <th>Tipo</th>
                            </tr>
                        </thead>
                        <tbody>
            `;
            
            data.data.forEach(record => {
                html += `
                    <tr>
                        <td>${record.date}</td>
                        <td>${record.patient}</td>
                        <td>${record.cui}</td>
                        <td>${record.doctor}</td>
                        <td>${record.specialty}</td>
                        <td><span class="badge bg-${record.attention_type === 'Emergencia' ? 'danger' : 'success'}">${record.attention_type}</span></td>
                    </tr>
                `;
            });
            
            html += `
                        </tbody>
                    </table>
                </div>
            `;
            
            previewContent.innerHTML = html;
        } else {
            previewContent.innerHTML = `
                <div class="alert alert-white border border-white">
                    <strong>Sin datos</strong><br>
                    No se encontraron registros para el período y filtros seleccionados.
                </div>
            `;
        }
    })
    .catch(error => {
        console.error('Error:', error);
        previewContent.innerHTML = `
            <div class="alert alert-danger border border-danger">
                <strong>❌ Error</strong><br>
                Ocurrió un error al generar la vista previa.
            </div>
        `;
    });
}

// Validación del formulario
document.getElementById('reportForm').addEventListener('submit', function(e) {
    const startDate = document.querySelector('input[name="start_date"]').value;
    const endDate = document.querySelector('input[name="end_date"]').value;
    
    if (new Date(startDate) > new Date(endDate)) {
        e.preventDefault();
        showValidationToast('La fecha de inicio no puede ser mayor que la fecha de fin.', 'danger');
        return false;
    }
    
    // Mostrar mensaje de generación
    const submitBtn = document.getElementById('generateBtn');
    const originalContent = submitBtn.innerHTML;
    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i> Generando...';
    submitBtn.disabled = true;
    
    // Restaurar botón después de 3 segundos
    setTimeout(function() {
        submitBtn.innerHTML = originalContent;
        submitBtn.disabled = false;
    }, 3000);
});
</script>
@endpush
@endsection 