@extends('layouts.panel')

@section('title', 'Reportes SIGSA 3H')
@section('breadcrumb', 'Reportes SIGSA 3H')

@section('content')
<div class="container-fluid py-4">
    <!-- Encabezado -->
    <div class="row">
        <div class="col-12">
            <div class="card my-4">
                <div class="card-header p-0 position-relative mt-n4 mx-3 z-index-2">
                    <div class="bg-gradient-primary shadow-primary border-radius-lg pt-4 pb-3">
                        <h6 class="text-white text-capitalize ps-3">📊 Reportes SIGSA 3H</h6>
                        <p class="text-white text-sm ps-3 mb-0">
                            Sistema de Información Gerencial de Salud - Formato 3H Guatemala
                        </p>
                    </div>
                </div>
                <div class="card-body px-0 pb-2">
                    <!-- Dashboard de estadísticas -->
                    <div class="row px-4 mb-4">
                        <div class="col-xl-3 col-sm-6 mb-xl-0 mb-4">
                            <div class="card">
                                <div class="card-header p-3 pt-2">
                                    <div class="icon icon-lg icon-shape bg-gradient-dark shadow-dark text-center border-radius-xl mt-n4 position-absolute">
                                        <i class="fas fa-users opacity-10"></i>
                                    </div>
                                    <div class="text-end pt-1">
                                        <p class="text-sm mb-0 text-capitalize">Total Consultas</p>
                                        <h4 class="mb-0" id="total-consultations">...</h4>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-xl-3 col-sm-6 mb-xl-0 mb-4">
                            <div class="card">
                                <div class="card-header p-3 pt-2">
                                    <div class="icon icon-lg icon-shape bg-gradient-danger shadow-danger text-center border-radius-xl mt-n4 position-absolute">
                                        <i class="fas fa-ambulance opacity-10"></i>
                                    </div>
                                    <div class="text-end pt-1">
                                        <p class="text-sm mb-0 text-capitalize">Emergencias</p>
                                        <h4 class="mb-0" id="emergency-consultations">...</h4>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-xl-3 col-sm-6 mb-xl-0 mb-4">
                            <div class="card">
                                <div class="card-header p-3 pt-2">
                                    <div class="icon icon-lg icon-shape bg-gradient-success shadow-success text-center border-radius-xl mt-n4 position-absolute">
                                        <i class="fas fa-stethoscope opacity-10"></i>
                                    </div>
                                    <div class="text-end pt-1">
                                        <p class="text-sm mb-0 text-capitalize">Consulta Externa</p>
                                        <h4 class="mb-0" id="external-consultations">...</h4>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-xl-3 col-sm-6">
                            <div class="card">
                                <div class="card-header p-3 pt-2">
                                    <div class="icon icon-lg icon-shape bg-gradient-info shadow-info text-center border-radius-xl mt-n4 position-absolute">
                                        <i class="fas fa-user-plus opacity-10"></i>
                                    </div>
                                    <div class="text-end pt-1">
                                        <p class="text-sm mb-0 text-capitalize">Pacientes Nuevos</p>
                                        <h4 class="mb-0" id="new-patients">...</h4>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Formulario de generación de reportes -->
                    <div class="px-4">
                        <div class="card">
                            <div class="card-header">
                                <h6 class="mb-0">🔍 Filtros para Reporte SIGSA 3H</h6>
                            </div>
                            <div class="card-body">
                                <form method="POST" action="{{ route('reports.generate-sigsa') }}" id="reportForm">
                                    @csrf
                                    <div class="row">
                                        <!-- Fechas -->
                                        <div class="col-md-6">
                                            <div class="input-group input-group-outline mb-3">
                                                <label class="form-label">Fecha Inicio</label>
                                                <input type="date" name="start_date" class="form-control" required 
                                                       value="{{ old('start_date', now()->startOfMonth()->format('Y-m-d')) }}">
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="input-group input-group-outline mb-3">
                                                <label class="form-label">Fecha Fin</label>
                                                <input type="date" name="end_date" class="form-control" required
                                                       value="{{ old('end_date', now()->format('Y-m-d')) }}">
                                            </div>
                                        </div>

                                        <!-- Tipo de Atención -->
                                        <div class="col-md-6">
                                            <div class="input-group input-group-outline mb-3">
                                                <select name="attention_type" class="form-control">
                                                    <option value="">Todos los tipos de atención</option>
                                                    <option value="emergencia" {{ old('attention_type') == 'emergencia' ? 'selected' : '' }}>
                                                        🚨 Solo Emergencias
                                                    </option>
                                                    <option value="consulta_externa" {{ old('attention_type') == 'consulta_externa' ? 'selected' : '' }}>
                                                        🏥 Solo Consulta Externa
                                                    </option>
                                                </select>
                                            </div>
                                        </div>

                                        <!-- Especialidad -->
                                        <div class="col-md-6">
                                            <div class="input-group input-group-outline mb-3">
                                                <select name="specialty_id" class="form-control">
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
                                            <div class="input-group input-group-outline mb-3">
                                                <select name="control_type_id" class="form-control">
                                                    <option value="">Todos los tipos de control</option>
                                                    @foreach($controlTypes as $controlType)
                                                        <option value="{{ $controlType->id }}" {{ old('control_type_id') == $controlType->id ? 'selected' : '' }}>
                                                            {{ $controlType->name }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>

                                        <!-- Botones -->
                                        <div class="col-md-6 d-flex align-items-end">
                                            <div class="btn-group w-100" role="group">
                                                <button type="button" class="btn btn-outline-info" onclick="previewData()">
                                                    <i class="fas fa-eye me-1"></i> Vista Previa
                                                </button>
                                                <button type="submit" class="btn btn-success">
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
                    <div class="px-4 mt-4" id="previewSection" style="display: none;">
                        <div class="card">
                            <div class="card-header">
                                <h6 class="mb-0">👁️ Vista Previa del Reporte</h6>
                            </div>
                            <div class="card-body">
                                <div id="previewContent">
                                    <!-- Contenido dinámico de vista previa -->
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Información adicional -->
                    <div class="px-4 mt-4">
                        <div class="alert alert-info">
                            <div class="row">
                                <div class="col-md-8">
                                    <h6 class="alert-heading">ℹ️ Información del Reporte SIGSA 3H</h6>
                                    <p class="mb-0">
                                        El reporte SIGSA 3H incluye todos los campos requeridos por el Ministerio de Salud de Guatemala:
                                        datos del paciente, tipo de atención, diagnósticos, tratamientos, referencias y contra-referencias.
                                    </p>
                                </div>
                                <div class="col-md-4 text-end">
                                    <a href="{{ route('control-types.index') }}" class="btn btn-outline-primary btn-sm">
                                        <i class="fas fa-cogs me-1"></i> Gestionar Tipos de Control
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
// Cargar estadísticas al inicio
document.addEventListener('DOMContentLoaded', function() {
    loadStatistics();
});

// Función para cargar estadísticas
function loadStatistics() {
    fetch('{{ route("reports.statistics") }}')
        .then(response => response.json())
        .then(data => {
            document.getElementById('total-consultations').textContent = data.total_consultations;
            document.getElementById('emergency-consultations').textContent = data.emergency_consultations;
            document.getElementById('external-consultations').textContent = data.external_consultations;
            document.getElementById('new-patients').textContent = data.new_patients;
        })
        .catch(error => {
            console.error('Error loading statistics:', error);
        });
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
            <div class="spinner-border" role="status">
                <span class="visually-hidden">Cargando...</span>
            </div>
            <p class="mt-2">Generando vista previa...</p>
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
                <div class="alert alert-success mb-3">
                    <strong>📊 Total de registros encontrados: ${data.total_records}</strong>
                    <br>Mostrando los primeros ${data.preview_records} registros
                </div>
                <div class="table-responsive">
                    <table class="table table-sm">
                        <thead>
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
                        <td><span class="badge bg-gradient-${record.attention_type === 'Emergencia' ? 'danger' : 'success'}">${record.attention_type}</span></td>
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
                <div class="alert alert-warning">
                    <strong>⚠️ Sin datos</strong><br>
                    No se encontraron registros para el período y filtros seleccionados.
                </div>
            `;
        }
    })
    .catch(error => {
        console.error('Error:', error);
        previewContent.innerHTML = `
            <div class="alert alert-danger">
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
        alert('La fecha de inicio no puede ser mayor que la fecha de fin.');
        return false;
    }
    
    // Mostrar mensaje de generación
    const submitBtn = this.querySelector('button[type="submit"]');
    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i> Generando...';
    submitBtn.disabled = true;
});
</script>
@endpush
@endsection 