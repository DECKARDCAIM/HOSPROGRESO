@extends('layouts.panel')

@section('title', 'Editar Tipo de Horario')
@section('breadcrumb', 'Tipos de Horario / Editar')

@section('content')

    <div class="container-fluid py-4">
        <div class="row">
            <div class="col-12">
                <div class="card mb-4 border">
                    <div class="card-header pb-0 bg-brand-header">
                        <div class="row align-items-center">
                            <div class="col-md-8">
                                <h6 class="text-white mb-0">Editar Tipo de Horario</h6>
                            </div>
                            <div class="col-md-4 text-end">
                                <a href="{{ route('schedule-types.index') }}" class="btn btn-sm btn-white">
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
                                    <strong>Por favor!</strong> Revisa los siguientes errores:
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

                        <form action="{{ route('schedule-types.update', $scheduleType->id) }}" method="POST"
                            class="form-horizontal">
                            @csrf
                            @method('PUT')
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group mb-4">
                                        <label for="name" class="form-control-label mb-2">
                                            <i class="bi bi-tag text-info me-2"></i>Nombre del horario
                                        </label>
                                        <input type="text" name="name" id="name"
                                            class="form-control form-control-lg border border-2 border-info shadow-sm"
                                            placeholder="Nombre del horario" value="{{ old('name', $scheduleType->name) }}"
                                            required>
                                        <div class="form-text text-muted">Ingrese un nombre descriptivo para el horario
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group mb-4">
                                        <label for="specialty_id" class="form-control-label mb-2">
                                            <i class="bi bi-clipboard-pulse text-info me-2"></i>Especialidad
                                        </label>
                                        <select class="form-control form-control-lg border border-2 border-info shadow-sm"
                                            id="specialty_id" name="specialty_id" required>
                                            <option value="">Seleccione</option>
                                            @foreach ($specialties as $specialty)
                                                <option value="{{ $specialty->id }}"
                                                    {{ old('specialty_id', $scheduleType->specialty_id) == $specialty->id ? 'selected' : '' }}>
                                                    {{ $specialty->name }}</option>
                                            @endforeach
                                        </select>
                                        <div class="form-text text-muted">Seleccione la especialidad asociada</div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12 mb-4">
                                    <label class="form-label mb-2"><i class="bi bi-calendar-alt text-info me-2"></i>Días de
                                        la semana</label><br>
                                    @php
                                        $selectedDays = old('days_of_week', $scheduleType->days_of_week ?? []);
                                    @endphp
                                    @foreach ([1 => 'Lunes', 2 => 'Martes', 3 => 'Miércoles', 4 => 'Jueves', 5 => 'Viernes', 6 => 'Sábado', 7 => 'Domingo'] as $num => $day)
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="checkbox" name="days_of_week[]"
                                                id="day{{ $num }}" value="{{ $num }}"
                                                {{ in_array($num, $selectedDays) ? 'checked' : '' }}>
                                            <label class="form-check-label"
                                                for="day{{ $num }}">{{ $day }}</label>
                                        </div>
                                    @endforeach
                                    <div class="form-text text-muted">Seleccione los días en que aplica este horario</div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group mb-4">
                                        <label for="start_time" class="form-control-label mb-2">
                                            <i class="bi bi-clock text-info me-2"></i>Hora Inicio
                                        </label>
                                        <input type="time" name="start_time" id="start_time"
                                            class="form-control form-control-lg border border-2 border-info shadow-sm"
                                            value="{{ old('start_time', $scheduleType->start_time) }}" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group mb-4">
                                        <label for="end_time" class="form-control-label mb-2">
                                            <i class="bi bi-clock text-info me-2"></i>Hora Fin
                                        </label>
                                        <input type="time" name="end_time" id="end_time"
                                            class="form-control form-control-lg border border-2 border-info shadow-sm"
                                            value="{{ old('end_time', $scheduleType->end_time) }}" required>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group mb-4">
                                        <label for="max_patients" class="form-control-label mb-2">
                                            <i class="bi bi-persons text-info me-2"></i>Cupo máximo por día
                                        </label>
                                        <input type="number" name="max_patients" id="max_patients"
                                            class="form-control form-control-lg border border-2 border-info shadow-sm"
                                            min="1" value="{{ old('max_patients', $scheduleType->max_patients) }}"
                                            required>
                                        <div class="form-text text-muted">Cantidad máxima de pacientes por día</div>
                                    </div>
                                </div>
                            </div>
                            <div class="d-flex justify-content-end mt-4">
                                <button type="button" class="btn btn-secondary btn-lg me-2"
                                    onclick="window.location.href='{{ route('schedule-types.index') }}'">
                                    <i class="bi bi-x me-2"></i>Cancelar
                                </button>
                                <button type="submit" class="btn bg-brand-header btn-lg text-white">
                                    <i class="bi bi-check-circle me-2"></i>Actualizar horario
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection