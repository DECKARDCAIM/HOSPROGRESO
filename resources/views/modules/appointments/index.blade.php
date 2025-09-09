@extends('layouts.panel')

@section('title', 'Gestión de Citas Médicas')
@section('breadcrumb', 'Citas / Gestión')

@section('content')

<style>
@media (max-width: 1199px) {
    .card-body .table-responsive { border-radius: 8px; overflow: hidden; }
    .dataTable-container { border-radius: 8px; box-shadow: 0 2px 8px rgba(0,0,0,0.1); }
    .btn { margin: 2px; min-width: 80px; }
    .card-body { padding: 1rem; }
    .card-body .row { margin-bottom: 1rem; }
}
.stats-section {
    margin-bottom: 2rem;
}
.btn-group .btn {
    transition: none !important;
    border-radius: 0;
    white-space: nowrap;
    text-overflow: ellipsis;
    overflow: hidden;
}
.btn-group .btn:first-child {
    border-top-left-radius: 0.375rem;
    border-bottom-left-radius: 0.375rem;
}
.btn-group .btn:last-child {
    border-top-right-radius: 0.375rem;
    border-bottom-right-radius: 0.375rem;
}
@media (max-width: 768px) {
    .btn-group {
        flex-direction: column;
    }
    .btn-group .btn {
        border-radius: 0.375rem !important;
        margin-bottom: 5px;
    }
}
</style>

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
                                <i class="bi bi-calendar-date text-lg opacity-10"></i>
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
                                <i class="bi bi-clock text-lg opacity-10"></i>
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
                            <div class="icon icon-shape bg-brand-header shadow text-center border-radius-md">
                                <i class="bi bi-check-circle text-lg opacity-10"></i>
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
                                <i class="bi bi-person-check text-lg opacity-10"></i>
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
                                <i class="bi bi-person-x text-lg opacity-10"></i>
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
                                <i class="bi bi-x-circle text-lg opacity-10"></i>
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
                            <h6 class="text-white mb-0">Gestión de Citas Médicas</h6>
                            <p class="text-sm text-white opacity-8 mb-0">
                                Este módulo permite gestionar las citas médicas registradas en el sistema.
                            </p>
                        </div>
                        <div class="col-md-4 text-end">
                            <a href="{{ route('appointments.create') }}" class="btn btn-sm btn-white">
                                <i class="bi bi-plus me-2"></i>Nueva Cita
                            </a>
                        </div>
                        </div>
                    </div>

                <div class="card-body pt-3 pb-2">
                    <form action="{{ route('appointments.index') }}" method="GET" class="mb-0">
                        <div class="row g-2 align-items-end">
                            <div class="col-md-3">
                                <label class="form-label">Buscar (Paciente, CUI, N° Cita)</label>
                                <input type="text" name="q" class="form-control form-control-lg border border-info" placeholder="Buscar cita..." value="{{ request('q') }}">
                            </div>
                            <div class="col-md-2">
                                <label class="form-label">Estado</label>
                                <select name="status" class="form-select form-select-lg border border-info auto-submit">
                                    <option value="">Todos los estados</option>
                                    <option value="pendiente" {{ request('status') == 'pendiente' ? 'selected' : '' }}>Pendiente</option>
                                    <option value="confirmada" {{ request('status') == 'confirmada' ? 'selected' : '' }}>Confirmada</option>
                                    <option value="atendida" {{ request('status') == 'atendida' ? 'selected' : '' }}>Atendida</option>
                                    <option value="perdida" {{ request('status') == 'perdida' ? 'selected' : '' }}>Perdida</option>
                                    <option value="cancelada" {{ request('status') == 'cancelada' ? 'selected' : '' }}>Cancelada</option>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <label class="form-label">Fecha</label>
                                <input type="date" name="date" class="form-control form-control-lg border border-info auto-submit" value="{{ request('date') }}">
                            </div>
                            <div class="col-md-2">
                                <label class="form-label">Especialidad</label>
                                <select name="specialty_id" class="form-select form-select-lg border border-info auto-submit">
                                    <option value="">Todas las especialidades</option>
                                    @foreach($specialties as $specialty)
                                        <option value="{{ $specialty->id }}" {{ request('specialty_id') == $specialty->id ? 'selected' : '' }}>{{ $specialty->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Doctor</label>
                                <select name="doctor_id" class="form-select form-select-lg border border-info auto-submit">
                                    <option value="">Todos los doctores</option>
                                    @foreach($doctors as $doctor)
                                        <option value="{{ $doctor->id }}" {{ request('doctor_id') == $doctor->id ? 'selected' : '' }}>{{ $doctor->first_name }} {{ $doctor->first_lastname }}</option>
                                    @endforeach
                                </select>
                            </div>
                            </div>
                        <div class="row g-2 align-items-end mt-2">
                            <div class="col-md-12 d-flex align-items-end gap-2">
                                <button type="submit" class="btn bg-brand-header text-white btn-lg">
                                    <i class="bi bi-funnel me-2"></i>Filtrar
                                </button>
                                @php
                                    $hasFilters = !empty(request('q')) || !empty(request('status')) || !empty(request('date')) || !empty(request('specialty_id')) || !empty(request('doctor_id'));
                                @endphp
                                @if($hasFilters)
                                    <a href="{{ route('appointments.index') }}" class="btn btn-outline-secondary btn-lg">
                                        <i class="bi bi-x me-2"></i>Limpiar filtros
                                    </a>
                                @endif
                            </div>
                        </div>
                    </form>
                </div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Filtros dinámicos para elementos auto-submit
    const autoSubmitElements = document.querySelectorAll('.auto-submit');
    autoSubmitElements.forEach(element => {
        element.addEventListener('change', function() {
            this.form.submit();
        });
    });

    // Permitir buscar con Enter en el campo de búsqueda de texto
    const searchInput = document.querySelector('input[name="q"]');
    if (searchInput) {
        searchInput.addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                this.form.submit();
            }
        });
    }
});
</script>

                <div class="card-body px-0 pt-0 pb-2">
                    <div class="table-responsive p-0">
                        <table class="table align-items-center mb-0 dataTable-table" id="datatable-basic" data-datatable="true">
                            <thead>
                                <tr>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-3">N° Cita</th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-3">Paciente</th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-3">Especialidad</th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-3">Doctor</th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-3">Fecha y Hora</th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-3">Estado</th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-3 text-center">Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($appointments as $appointment)
                                    <tr>
                                        <td class="px-3 py-2">
                                            <div class="d-flex align-items-center">
                                                <div class="avatar avatar-sm me-3 bg-brand-header rounded-circle">
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
                                            <span class="badge {{ $appointment->status_badge }}">{{ $appointment->status_text }}</span>
                                        </td>
                                        <td class="align-middle text-center">
                                            <a href="{{ route('appointments.show', $appointment) }}" class="btn btn-primary rounded-pill px-3 py-2">
                                            <i class="bi bi-search me-1"></i>Ver
                                            </a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                    <td colspan="7" class="text-center py-4">
                                            <span class="text-muted">No hay citas registradas.</span>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    <div class="d-flex justify-content-center mt-4">
                        {{ $appointments->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
@if(isset($toastData) && $toastData)
<script>
document.addEventListener('DOMContentLoaded', function() {
    showToast('{{ $toastData['type'] }}', '{{ $toastData['title'] }}', '{{ $toastData['message'] }}');
});
</script>
@endif

<script>
document.addEventListener('DOMContentLoaded', function() {
    const specialtySelect = document.querySelector('select[name="specialty_id"]');
    const doctorSelect = document.querySelector('select[name="doctor_id"]');
    
    if (specialtySelect && doctorSelect) {
        // Función para cargar doctores por especialidad
        function loadDoctorsBySpecialty(specialtyId, preserveSelection = false) {
            if (specialtyId) {
                // Mostrar loading en el select de doctores
                doctorSelect.innerHTML = '<option value="">Cargando doctores...</option>';
                doctorSelect.disabled = true;
                
                // Hacer petición AJAX para obtener doctores por especialidad
                fetch('{{ route("appointments.get-doctors") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({ specialty_id: specialtyId })
                })
                .then(response => response.json())
                .then(doctors => {
                    doctorSelect.innerHTML = '<option value="">Todos los doctores</option>';
                    doctors.forEach(doctor => {
                        const option = document.createElement('option');
                        option.value = doctor.id;
                        option.textContent = `${doctor.first_name} ${doctor.first_lastname}`;
                        
                        // Preservar selección si se especifica
                        if (preserveSelection && '{{ request("doctor_id") }}' == doctor.id) {
                            option.selected = true;
                        }
                        
                        doctorSelect.appendChild(option);
                    });
                    doctorSelect.disabled = false;
                })
                .catch(error => {
                    console.error('Error cargando doctores:', error);
                    doctorSelect.innerHTML = '<option value="">Error cargando doctores</option>';
                    doctorSelect.disabled = false;
                });
            } else {
                // Si no hay especialidad seleccionada, cargar todos los doctores
                doctorSelect.innerHTML = '<option value="">Todos los doctores</option>';
                @foreach($doctors as $doctor)
                    const option{{ $doctor->id }} = document.createElement('option');
                    option{{ $doctor->id }}.value = '{{ $doctor->id }}';
                    option{{ $doctor->id }}.textContent = '{{ $doctor->first_name }} {{ $doctor->first_lastname }}';
                    @if(request('doctor_id') == $doctor->id)
                        option{{ $doctor->id }}.selected = true;
                    @endif
                    doctorSelect.appendChild(option{{ $doctor->id }});
                @endforeach
            }
        }
        
        // Event listener para cambios en especialidad
        specialtySelect.addEventListener('change', function() {
            loadDoctorsBySpecialty(this.value);
        });
        
        // Cargar doctores al inicializar si ya hay una especialidad seleccionada
        const selectedSpecialtyId = specialtySelect.value;
        if (selectedSpecialtyId) {
            loadDoctorsBySpecialty(selectedSpecialtyId, true);
        }
    }
});
</script>
@endpush

@endsection 