@extends('layouts.panel')

@section('title', 'Detalles de Sustitución')
@section('breadcrumb', 'Sustituciones / Detalles')

@section('content')
<div class="container-fluid py-4">
    <div class="row">
        <div class="col-12">
            <div class="card mb-4 border">
                <div class="card-header pb-0 bg-brand-header">
                    <div class="row align-items-center">
                        <div class="col-md-8">
                            <h6 class="text-white mb-0">Detalles de Sustitución</h6>
                        </div>
                        <div class="col-md-4 text-end">
                            <a href="{{ route('doctor-substitutions.index') }}" class="btn btn-sm btn-white">
                                <i class="fas fa-chevron-left me-2"></i>Volver al listado
                            </a>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <!-- Información General -->
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <div class="card border">
                                <div class="card-header bg-light">
                                    <h6 class="mb-0"><i class="fas fa-user-md me-2"></i>Doctor Original</h6>
                                </div>
                                <div class="card-body">
                                    <div class="d-flex align-items-center">
                                        <div class="avatar-lg bg-brand-header rounded-circle d-flex align-items-center justify-content-center me-3">
                                            <span class="text-white fw-bold fs-4">
                                                {{ substr($substitution->originalDoctor->first_name, 0, 1) }}
                                            </span>
                                        </div>
                                        <div>
                                            <h5 class="mb-1">{{ $substitution->originalDoctor->full_name }}</h5>
                                            <p class="text-muted mb-1">
                                                <strong>Especialidad:</strong> {{ $substitution->originalDoctor->specialty->name ?? 'Sin especialidad' }}
                                            </p>
                                            <p class="text-muted mb-0">
                                                <strong>CUI:</strong> {{ $substitution->originalDoctor->cui }}
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card border">
                                <div class="card-header bg-light">
                                    <h6 class="mb-0"><i class="fas fa-user-plus me-2"></i>Doctor Suplente</h6>
                                </div>
                                <div class="card-body">
                                    <div class="d-flex align-items-center">
                                        <div class="avatar-lg bg-brand-header rounded-circle d-flex align-items-center justify-content-center me-3">
                                            <span class="text-white fw-bold fs-4">
                                                {{ substr($substitution->substituteDoctor->first_name, 0, 1) }}
                                            </span>
                                        </div>
                                        <div>
                                            <h5 class="mb-1">{{ $substitution->substituteDoctor->full_name }}</h5>
                                            <p class="text-muted mb-1">
                                                <strong>Especialidad:</strong> {{ $substitution->substituteDoctor->specialty->name ?? 'Sin especialidad' }}
                                            </p>
                                            <p class="text-muted mb-0">
                                                <strong>CUI:</strong> {{ $substitution->substituteDoctor->cui }}
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Información de la Sustitución -->
                    <div class="row mb-4">
                        <div class="col-md-12">
                            <div class="card border">
                                <div class="card-header bg-light">
                                    <h6 class="mb-0"><i class="fas fa-info-circle me-2"></i>Información de la Sustitución</h6>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-3">
                                            <strong>Período:</strong><br>
                                            <span class="text-secondary">
                                                {{ \Carbon\Carbon::parse($substitution->start_date)->format('d/m/Y') }}
                                            </span>
                                            <br>
                                            <small class="text-muted">hasta</small>
                                            <br>
                                            <span class="text-secondary">
                                                {{ \Carbon\Carbon::parse($substitution->end_date)->format('d/m/Y') }}
                                            </span>
                                        </div>
                                        <div class="col-md-3">
                                            <strong>Duración:</strong><br>
                                            <span class="text-info">{{ $statistics['duration_days'] }} días</span>
                                        </div>
                                        <div class="col-md-3">
                                            <strong>Razón:</strong><br>
                                            <span class="badge bg-brand-header">
                                                @if($substitution->reason == 'vacaciones')
                                                    <i class="fas fa-plane me-1"></i>Vacaciones
                                                @elseif($substitution->reason == 'licencia_medica')
                                                    <i class="fas fa-heartbeat me-1"></i>Licencia Médica
                                                @elseif($substitution->reason == 'despido')
                                                    <i class="fas fa-user-times me-1"></i>Despido
                                                @elseif($substitution->reason == 'otro')
                                                    <i class="fas fa-question me-1"></i>Otro
                                                @else
                                                    <i class="fas fa-question me-1"></i>{{ $substitution->reason ?? 'Sin razón' }}
                                                @endif
                                            </span>
                                        </div>
                                        <div class="col-md-3">
                                            <strong>Estado:</strong><br>
                                            @if($substitution->status === 'programada')
                                                <span class="badge bg-warning text-dark">
                                                    <i class="fas fa-clock me-1"></i>Programada
                                                </span>
                                            @elseif($substitution->status === 'activa')
                                                <span class="badge bg-success">
                                                    <i class="fas fa-check-circle me-1"></i>Activa
                                                </span>
                                                @if($substitution->isNearExpiration())
                                                    <br><small class="text-warning">
                                                        <i class="fas fa-exclamation-triangle me-1"></i>
                                                        Próxima a vencer
                                                    </small>
                                                @endif
                                            @elseif($substitution->status === 'completada')
                                                <span class="badge bg-primary">
                                                    <i class="fas fa-flag-checkered me-1"></i>Completada
                                                </span>
                                            @elseif($substitution->status === 'cancelada')
                                                <span class="badge bg-danger">
                                                    <i class="fas fa-times-circle me-1"></i>Cancelada
                                                </span>
                                            @else
                                                <span class="badge bg-secondary">
                                                    <i class="fas fa-question me-1"></i>{{ $substitution->status ?? 'Sin estado' }}
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                    @if($substitution->notes)
                                    <div class="row mt-3">
                                        <div class="col-md-12">
                                            <strong>Notas:</strong><br>
                                            <p class="text-muted">{{ $substitution->notes }}</p>
                                        </div>
                                    </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Estadísticas -->
                    <div class="row mb-4">
                        <div class="col-md-12">
                            <div class="card border">
                                <div class="card-header bg-light">
                                    <h6 class="mb-0"><i class="fas fa-chart-bar me-2"></i>Estadísticas</h6>
                                </div>
                                <div class="card-body">
                                    <div class="row text-center">
                                        <div class="col-md-4">
                                            <div class="border rounded p-3">
                                                <h3 class="text-secondary mb-1">{{ $statistics['appointments_count'] }}</h3>
                                                <p class="text-muted mb-0">Citas Reasignadas</p>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="border rounded p-3">
                                                <h3 class="text-success mb-1">{{ $statistics['consultations_count'] }}</h3>
                                                <p class="text-muted mb-0">Consultas Reasignadas</p>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="border rounded p-3">
                                                <h3 class="text-info mb-1">{{ $statistics['duration_days'] }}</h3>
                                                <p class="text-muted mb-0">Días de Duración</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Citas Reasignadas -->
                    @if($substitution->appointments->count() > 0)
                    <div class="row mb-4">
                        <div class="col-md-12">
                            <div class="card border">
                                <div class="card-header bg-light">
                                    <h6 class="mb-0"><i class="fas fa-calendar-check me-2"></i>Citas Reasignadas</h6>
                                </div>
                                <div class="card-body">
                                    <div class="table-responsive">
                                        <table class="table table-sm">
                                            <thead>
                                                <tr>
                                                    <th>Número</th>
                                                    <th>Paciente</th>
                                                    <th>Fecha y Hora</th>
                                                    <th>Estado</th>
                                                    <th>Reasignada</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($substitution->appointments as $appointment)
                                                <tr>
                                                    <td>{{ $appointment->appointment_number }}</td>
                                                    <td>{{ $appointment->clinicalRecord->full_name ?? 'N/A' }}</td>
                                                    <td>{{ $appointment->appointment_date->format('d/m/Y H:i') }}</td>
                                                    <td>
                                                        <span class="badge bg-{{ $appointment->status === 'pendiente' ? 'warning' : ($appointment->status === 'confirmada' ? 'info' : 'success') }}">
                                                            {{ ucfirst($appointment->status) }}
                                                        </span>
                                                    </td>
                                                    <td>
                                                        <small class="text-muted">
                                                            {{ $appointment->substituted_at ? $appointment->substituted_at->format('d/m/Y H:i') : 'N/A' }}
                                                        </small>
                                                    </td>
                                                </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endif

                    <!-- Acciones -->
                    @if($substitution->status === 'activa')
                    <div class="row">
                        <div class="col-md-12">
                            <div class="card border">
                                <div class="card-header bg-light">
                                    <h6 class="mb-0"><i class="fas fa-cogs me-2"></i>Acciones Disponibles</h6>
                                </div>
                                <div class="card-body">
                                    <div class="d-flex gap-2">
                                        <form method="POST" action="{{ route('doctor-substitutions.complete', $substitution) }}" 
                                              style="display: inline;" 
                                              onsubmit="return confirm('¿Está seguro de finalizar esta sustitución? Las citas serán devueltas al doctor original.')">
                                            @csrf
                                            <button type="submit" class="btn bg-brand-header rounded-pill px-3 py-2 text-white">
                                                <i class="bi bi-check-circle me-1"></i>Finalizar Sustitución
                                            </button>
                                        </form>
                                        
                                        <form method="POST" action="{{ route('doctor-substitutions.cancel', $substitution) }}" 
                                              style="display: inline;" 
                                              onsubmit="return confirm('¿Está seguro de cancelar esta sustitución? Las citas serán devueltas al doctor original.')">
                                            @csrf
                                            <button type="submit" class="btn btn-secondary rounded-pill px-3 py-2 text-white">
                                                <i class="bi bi-x-circle me-1"></i>Cancelar Sustitución
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endif

                    <!-- Asignar nuevo doctor (para despidos completados) -->
                    @if($substitution->status === 'completada' && $substitution->reason === 'despido')
                    <div class="row">
                        <div class="col-md-12">
                            <div class="card border">
                                <div class="card-header bg-warning">
                                    <h6 class="mb-0"><i class="fas fa-user-plus me-2"></i>Asignar Nuevo Doctor al Horario</h6>
                                </div>
                                <div class="card-body">
                                    <p class="text-muted mb-3">
                                        El doctor despedido permanece desactivado. Puede asignar un nuevo doctor de la misma especialidad para tomar su horario.
                                    </p>
                                    
                                    <form method="POST" action="{{ route('doctor-substitutions.assign-new-doctor', $substitution) }}">
                                        @csrf
                                        <div class="row">
                                            <div class="col-md-8">
                                                <select name="new_doctor_id" class="form-select" required>
                                                    <option value="">Seleccionar nuevo doctor...</option>
                                                    @foreach(\App\Models\Doctor::where('specialty_id', $substitution->originalDoctor->specialty_id)
                                                                                ->where('is_active', true)
                                                                                ->where('id', '!=', $substitution->substitute_doctor_id)
                                                                                ->get() as $doctor)
                                                        <option value="{{ $doctor->id }}">
                                                            {{ $doctor->first_name }} {{ $doctor->first_lastname }}
                                                            @if($doctor->scheduleType)
                                                                - Horario: {{ $doctor->scheduleType->name }}
                                                            @else
                                                                - Sin horario asignado
                                                            @endif
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div class="col-md-4">
                                                <button type="submit" class="btn btn-warning rounded-pill px-3 py-2 text-white w-100"
                                                        onclick="return confirm('¿Está seguro de asignar este doctor al horario? Las citas pendientes serán transferidas.')">
                                                    <i class="fas fa-user-plus me-1"></i>Asignar Doctor
                                                </button>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
