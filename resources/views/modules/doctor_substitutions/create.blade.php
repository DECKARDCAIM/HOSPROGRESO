@extends('layouts.panel')

@section('title', 'Nueva Sustitución de Doctor')
@section('breadcrumb', 'Sustituciones / Nueva')

@section('content')
<div class="container-fluid py-4">
    <div class="row">
        <div class="col-12">
            <div class="card mb-4 border">
                <div class="card-header pb-0 bg-brand-header">
                    <div class="row align-items-center">
                        <div class="col-md-8">
                            <h6 class="text-white mb-0">Nueva Sustitución de Doctor</h6>
                        </div>
                        <div class="col-md-4 text-end">
                            <a href="{{ route('doctor-substitutions.index') }}" class="btn btn-sm btn-white">
                                <i class="bi bi-arrow-left me-2"></i>Regresar
                            </a>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form action="{{ route('doctor-substitutions.store') }}" method="POST" class="form-horizontal">
                        @csrf
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group mb-4">
                                    <label for="original_doctor_id" class="form-control-label mb-2">
                                        <i class="bi bi-person-md text-info me-2"></i>Doctor Original
                                    </label>
                                    <select class="form-control form-control-lg border border-2 border-info shadow-sm"
                                            id="original_doctor_id" name="original_doctor_id" required>
                                        <option value="">Seleccione el doctor</option>
                                        @foreach ($doctors as $doctor)
                                            <option value="{{ $doctor->id }}" {{ old('original_doctor_id') == $doctor->id ? 'selected' : '' }}>
                                                {{ $doctor->full_name }} - {{ $doctor->specialty->name ?? 'Sin especialidad' }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <div class="form-text text-muted">Seleccione el doctor que será sustituido</div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group mb-4">
                                    <label for="substitute_doctor_id" class="form-control-label mb-2">
                                        <i class="bi bi-person-plus text-success me-2"></i>Doctor Suplente
                                    </label>
                                    <select class="form-control form-control-lg border border-2 border-info shadow-sm"
                                            id="substitute_doctor_id" name="substitute_doctor_id" required disabled>
                                        <option value="">Primero seleccione el doctor original</option>
                                    </select>
                                    <div class="form-text text-muted">Seleccione el doctor que cubrirá las citas</div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group mb-4">
                                    <label for="start_date" class="form-control-label mb-2">
                                        <i class="bi bi-calendar-alt text-info me-2"></i>Fecha de Inicio
                                    </label>
                                    <input type="date" name="start_date" id="start_date"
                                           class="form-control form-control-lg border border-2 border-info shadow-sm"
                                           value="{{ old('start_date') }}" required>
                                    <div class="form-text text-muted">Fecha en que inicia la sustitución</div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group mb-4">
                                    <label for="end_date" class="form-control-label mb-2">
                                        <i class="bi bi-calendar-alt text-info me-2"></i>Fecha de Fin
                                    </label>
                                    <input type="date" name="end_date" id="end_date"
                                           class="form-control form-control-lg border border-2 border-info shadow-sm"
                                           value="{{ old('end_date') }}" required>
                                    <div class="form-text text-muted">Fecha en que termina la sustitución</div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group mb-4">
                                    <label for="reason" class="form-control-label mb-2">
                                        <i class="bi bi-exclamation-triangle-circle text-warning me-2"></i>Razón de la Sustitución
                                    </label>
                                    <select class="form-control form-control-lg border border-2 border-info shadow-sm"
                                            id="reason" name="reason" required>
                                        <option value="">Seleccione una razón</option>
                                        <option value="vacaciones" {{ old('reason') == 'vacaciones' ? 'selected' : '' }}>
                                            <i class="bi bi-airplane me-2"></i>Vacaciones
                                        </option>
                                        <option value="licencia_medica" {{ old('reason') == 'licencia_medica' ? 'selected' : '' }}>
                                            <i class="bi bi-heartbeat me-2"></i>Licencia Médica
                                        </option>
                                        <option value="despido" {{ old('reason') == 'despido' ? 'selected' : '' }}>
                                            <i class="bi bi-person-times me-2"></i>Despido
                                        </option>
                                        <option value="otro" {{ old('reason') == 'otro' ? 'selected' : '' }}>
                                            <i class="bi bi-question-circle me-2"></i>Otro
                                        </option>
                                    </select>
                                    <div class="form-text text-muted">Motivo por el cual se requiere la sustitución</div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group mb-4">
                                    <label for="notes" class="form-control-label mb-2">
                                        <i class="fas fa-sticky-note text-info me-2"></i>Notas Adicionales
                                    </label>
                                    <textarea name="notes" id="notes" rows="4"
                                              class="form-control form-control-lg border border-2 border-info shadow-sm"
                                              placeholder="Información adicional sobre la sustitución...">{{ old('notes') }}</textarea>
                                    <div class="form-text text-muted">Información adicional que pueda ser útil</div>
                                </div>
                            </div>
                        </div>

                        <!-- Información de citas que serán reasignadas -->
                        <div class="row" id="appointments-info" style="display: none;">
                            <div class="col-md-12">
                                <div class="alert bg-brand-header text-white">
                                    <h6 class="text-white"><i class="bi bi-info-circle me-2"></i>Información de Reasignación</h6>
                                    <p class="mb-0" id="appointments-count">Se cargarán las citas que serán reasignadas...</p>
                                </div>
                            </div>
                        </div>

                        <div class="d-flex justify-content-end mt-4">
                            <button type="button" class="btn btn-secondary btn-lg me-2"
                                    onclick="window.location.href='{{ route('doctor-substitutions.index') }}'">
                                <i class="bi bi-x me-2"></i>Cancelar
                            </button>
                            <button type="submit" class="btn bg-brand-header btn-lg text-white">
                                <i class="bi bi-check-circle me-2"></i>Crear Sustitución
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const originalDoctorSelect = document.getElementById('original_doctor_id');
    const substituteDoctorSelect = document.getElementById('substitute_doctor_id');
    const startDateInput = document.getElementById('start_date');
    const endDateInput = document.getElementById('end_date');
    const appointmentsInfo = document.getElementById('appointments-info');
    const appointmentsCount = document.getElementById('appointments-count');

    // Establecer fecha mínima como hoy
    const today = new Date().toISOString().split('T')[0];
    startDateInput.min = today;
    endDateInput.min = today;

    // Manejar cambio de fecha de inicio
    startDateInput.addEventListener('change', function() {
        endDateInput.min = this.value;
        loadAvailableDoctors();
    });

    // Manejar cambio de fecha de fin
    endDateInput.addEventListener('change', function() {
        loadAvailableDoctors();
    });

    // Manejar cambio de doctor original
    originalDoctorSelect.addEventListener('change', function() {
        loadAvailableDoctors();
    });

    function loadAvailableDoctors() {
        const originalDoctorId = originalDoctorSelect.value;
        const startDate = startDateInput.value;
        const endDate = endDateInput.value;

        if (!originalDoctorId || !startDate || !endDate) {
            substituteDoctorSelect.innerHTML = '<option value="">Complete todos los campos</option>';
            substituteDoctorSelect.disabled = true;
            appointmentsInfo.style.display = 'none';
            return;
        }

        substituteDoctorSelect.innerHTML = '<option value="">Cargando doctores disponibles...</option>';
        substituteDoctorSelect.disabled = true;

        fetch('{{ route("doctor-substitutions.get-available-doctors") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({
                original_doctor_id: originalDoctorId,
                start_date: startDate,
                end_date: endDate
            })
        })
        .then(response => response.json())
        .then(data => {
            substituteDoctorSelect.innerHTML = '<option value="">Seleccione el doctor suplente</option>';
            
            data.forEach(doctor => {
                const option = document.createElement('option');
                option.value = doctor.id;
                option.textContent = `${doctor.first_name} ${doctor.first_lastname} - ${doctor.specialty ? doctor.specialty.name : 'Sin especialidad'}`;
                substituteDoctorSelect.appendChild(option);
            });
            
            substituteDoctorSelect.disabled = false;
            
            // Mostrar información de citas
            showAppointmentsInfo(originalDoctorId, startDate, endDate);
        })
        .catch(error => {
            console.error('Error:', error);
            substituteDoctorSelect.innerHTML = '<option value="">Error al cargar doctores</option>';
        });
    }

    function showAppointmentsInfo(doctorId, startDate, endDate) {
        // Aquí podrías hacer una petición AJAX para obtener el número de citas
        // Por simplicidad, mostramos un mensaje genérico
        appointmentsCount.textContent = `Las citas del doctor seleccionado entre ${startDate} y ${endDate} serán reasignadas al doctor suplente.`;
        appointmentsInfo.style.display = 'block';
    }
});
</script>
@endpush
@endsection
