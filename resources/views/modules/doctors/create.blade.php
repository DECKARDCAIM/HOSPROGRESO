@extends('layouts.panel')

@section('title', 'Agregar Doctor')
@section('breadcrumb', 'Agregar Doctor')

@section('content')
    <div class="container-fluid py-4">
        <div class="row">
            <div class="col-12">
                <div class="card mb-4 border">
                    <div class="card-header pb-0 bg-brand-header">
                        <div class="row align-items-center">
                            <div class="col-md-8">
                                <h6 class="text-white mb-0">Agregar Doctor</h6>
                            </div>
                            <div class="col-md-4 text-end">
                                <a href="{{ route('doctors.index') }}" class="btn btn-sm btn-white">
                                    <i class="bi bi-arrow-left me-2"></i>Regresar
                                </a>
                            </div>
                        </div>
                    </div>

                    <div class="card-body">
                        @if ($errors->any())
                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                <span class="alert-icon"><i class="bi bi-exclamation-triangle"></i></span>
                                <span class="alert-text">
                                    <strong>¡Por favor!</strong> Revisa los siguientes errores:
                                    <ul class="mb-0">
                                        @foreach ($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                </span>
                                <button type="button" class="btn-close" data-bs-dismiss="alert"
                                    aria-label="Close"></button>
                            </div>
                        @endif

                        <form action="{{ route('doctors.store') }}" method="POST">
                            @csrf

                            <div class="row">
                                <div class="col-md-4 mb-3">
                                    <label for="first_name" class="form-label">
                                        Primer Nombre <span class="text-danger">*</span>
                                    </label>
                                    <input type="text" name="first_name" id="first_name" class="form-control"
                                        value="{{ old('first_name') }}" required>
                                </div>

                                <div class="col-md-4 mb-3">
                                    <label for="second_name" class="form-label">Segundo Nombre</label>
                                    <input type="text" name="second_name" id="second_name" class="form-control"
                                        value="{{ old('second_name') }}">
                                </div>

                                <div class="col-md-4 mb-3">
                                    <label for="third_name" class="form-label">Tercer Nombre</label>
                                    <input type="text" name="third_name" id="third_name" class="form-control"
                                        value="{{ old('third_name') }}">
                                </div>

                                <div class="col-md-4 mb-3">
                                    <label for="first_lastname" class="form-label">
                                        Primer Apellido <span class="text-danger">*</span>
                                    </label>
                                    <input type="text" name="first_lastname" id="first_lastname" class="form-control"
                                        value="{{ old('first_lastname') }}" required>
                                </div>

                                <div class="col-md-4 mb-3">
                                    <label for="second_lastname" class="form-label">Segundo Apellido</label>
                                    <input type="text" name="second_lastname" id="second_lastname" class="form-control"
                                        value="{{ old('second_lastname') }}">
                                </div>

                                <div class="col-md-4 mb-3">
                                    <label for="married_lastname" class="form-label">Apellido de Casada</label>
                                    <input type="text" name="married_lastname" id="married_lastname" class="form-control"
                                        value="{{ old('married_lastname') }}">
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label for="cui" class="form-label">
                                        CUI <span class="text-danger">*</span>
                                    </label>
                                    <input type="text" name="cui" id="cui" class="form-control"
                                        value="{{ old('cui') }}" maxlength="13" required>
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label for="license_number" class="form-label">
                                        No. Colegiado <span class="text-danger">*</span>
                                    </label>
                                    <input type="text" name="license_number" id="license_number" class="form-control"
                                        value="{{ old('license_number') }}" required>
                                </div>

                                <div class="col-md-12 mb-3">
                                    <label for="specialty_id" class="form-label">
                                        Especialidad <span class="text-danger">*</span>
                                    </label>
                                    <select name="specialty_id" id="specialty_id" class="form-select" required>
                                        <option value="">Seleccione una especialidad</option>
                                        @foreach ($specialties as $specialty)
                                            <option value="{{ $specialty->id }}"
                                                {{ old('specialty_id') == $specialty->id ? 'selected' : '' }}>
                                                {{ $specialty->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="col-md-12 mb-4">
                                    <label for="schedule_type_id" class="form-label mb-2">
                                        <i class="bi bi-calendar-alt text-info me-2"></i>Tipo de Horario
                                    </label>
                                    <select name="schedule_type_id" id="schedule_type_id"
                                        class="form-select form-select-lg border border-2 border-info shadow-sm" required
                                        aria-describedby="scheduleHelp" disabled>
                                        <option value="">Primero seleccione una especialidad</option>
                                    </select>
                                    <div id="scheduleHelp" class="form-text text-muted">
                                        Seleccione el tipo de horario asignado al doctor.
                                    </div>
                                </div>
                            </div>

                            <div class="d-flex justify-content-end mt-4">
                                <button type="button" class="btn btn-secondary btn-lg me-2"
                                    onclick="window.location.href='{{ route('doctors.index') }}'">
                                    <i class="bi bi-x me-2"></i>Cancelar
                                </button>

                                <button type="submit" class="btn bg-brand-header btn-lg text-white">
                                    <i class="bi bi-check-circle me-2"></i>Guardar Doctor
                                </button>
                            </div>
                        </form>
                    </div>

                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const specialtySelect = document.getElementById('specialty_id');
    const scheduleTypeSelect = document.getElementById('schedule_type_id');
    
    // Manejar cambio de especialidad
    specialtySelect.addEventListener('change', function() {
        const specialtyId = this.value;
        
        // Limpiar y deshabilitar el select de tipos de horario
        scheduleTypeSelect.innerHTML = '<option value="">Cargando tipos de horario...</option>';
        scheduleTypeSelect.disabled = true;
        
        if (specialtyId) {
            // Hacer petición AJAX para obtener tipos de horario
            fetch('{{ route("schedule-types.get-by-specialty") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({
                    specialty_id: specialtyId
                })
            })
            .then(response => response.json())
            .then(data => {
                // Limpiar opciones
                scheduleTypeSelect.innerHTML = '<option value="">Seleccione un tipo de horario</option>';
                
                // Agregar opciones de tipos de horario
                data.forEach(scheduleType => {
                    const option = document.createElement('option');
                    option.value = scheduleType.id;
                    option.textContent = `${scheduleType.name} (${scheduleType.start_time} - ${scheduleType.end_time})`;
                    scheduleTypeSelect.appendChild(option);
                });
                
                // Habilitar el select
                scheduleTypeSelect.disabled = false;
            })
            .catch(error => {
                console.error('Error:', error);
                scheduleTypeSelect.innerHTML = '<option value="">Error al cargar tipos de horario</option>';
            });
        } else {
            scheduleTypeSelect.innerHTML = '<option value="">Primero seleccione una especialidad</option>';
        }
    });
    
    // Si hay una especialidad preseleccionada (por old input), cargar sus tipos de horario
    if (specialtySelect.value) {
        specialtySelect.dispatchEvent(new Event('change'));
    }
});
</script>
@endpush