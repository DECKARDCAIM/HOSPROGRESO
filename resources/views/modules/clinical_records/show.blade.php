@extends('layouts.panel')

@section('title', 'Expediente Clínico')
@section('breadcrumb', 'Expedientes Clínicos / Ver')

@section('content')
<div class="container-fluid py-4">
    <div class="row">
        <div class="col-12">
            <div class="card mb-4">
                <div class="card-header pb-0 bg-brand-header">
                    <div class="row align-items-center">
                        <div class="col-md-8">
                            <h6 class="text-white mb-0">Expediente: {{ $clinicalRecord->record_number }}</h6>
                            <p class="text-sm text-white opacity-8 mb-0">
                                {{ $clinicalRecord->full_name }} | CUI: {{ $clinicalRecord->cui }}
                            </p>
                        </div>
                        <div class="col-md-4 text-end">
                            <a href="{{ route('clinical-records.index') }}" class="btn btn-sm btn-white">
                                <i class="fas fa-chevron-left me-2"></i>Regresar
                            </a>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <!-- Pestañas -->
                    <ul class="nav nav-tabs mb-3" id="recordTabs" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active" id="histories-tab" data-bs-toggle="tab" data-bs-target="#histories" type="button" role="tab" aria-controls="histories" aria-selected="true">
                                <i class="fas fa-notes-medical me-2"></i>Historias Clínicas
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="appointments-tab" data-bs-toggle="tab" data-bs-target="#appointments" type="button" role="tab" aria-controls="appointments" aria-selected="false">
                                <i class="fas fa-calendar-alt me-2"></i>Citas Médicas
                            </button>
                        </li>
                    </ul>

                    <div class="tab-content" id="recordTabsContent">
                        <!-- Pestaña de Historias Clínicas -->
                        <div class="tab-pane fade show active" id="histories" role="tabpanel" aria-labelledby="histories-tab">
                            <div class="d-flex justify-content-end my-3">
                                @canany(['emergencia.consultas.crear', 'consulta_externa.consultas.crear'])
                                    <button type="button" class="btn bg-brand-header text-white" id="createConsultationBtn">
                                        <i class="fas fa-notes-medical me-2"></i>Crear Historia Clínica
                                    </button>
                                @endcanany
                            </div>
                            <div class="p-0" style="overflow-x: auto; width: 100%;">
                                <table class="table align-items-center mb-0 dataTable-table" id="histories-table" data-datatable="true" style="min-width: 1000px; width: 100%;">
                                    <thead>
                                        <tr>
                                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-3" style="min-width: 120px;">Fecha</th>
                                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-3" style="min-width: 180px;">Médico</th>
                                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-3" style="min-width: 150px;">Especialidad</th>
                                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-3" style="min-width: 200px;">Motivo</th>
                                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-3" style="min-width: 140px;">Tipo de Atención</th>
                                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-3" style="min-width: 100px;">Estado</th>
                                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-3" style="min-width: 120px;">Estado Final</th>
                                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-3 text-center" style="min-width: 200px;">Acciones</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($clinicalRecord->medicalConsultations->sortByDesc('consultation_date') as $history)
                                        <tr>
                                            <td class="px-3 py-2" style="min-width: 120px;">
                                                <span class="text-sm text-secondary mb-0">{{ $history->consultation_date->format('d/m/Y H:i') }}</span>
                                            </td>
                                            <td class="px-3 py-2" style="min-width: 180px;">
                                                <span class="text-sm text-secondary mb-0">{{ $history->doctor->full_name ?? '-' }}</span>
                                            </td>
                                            <td class="px-3 py-2" style="min-width: 150px;">
                                                <span class="text-sm text-secondary mb-0">{{ $history->specialty->name ?? '-' }}</span>
                                            </td>
                                            <td class="px-3 py-2" style="min-width: 200px;">
                                                <span class="text-sm text-secondary mb-0">{{ $history->consultation_reason }}</span>
                                            </td>
                                            <td class="px-3 py-2" style="min-width: 140px;">
                                                @php
                                                    $attentionType = strtolower($history->attention_type ?? '');
                                                    $badgeClass = match($attentionType) {
                                                        'emergencia' => 'bg-danger',
                                                        'consulta_externa' => 'bg-success',
                                                        default => 'bg-light text-dark',
                                                    };
                                                @endphp
                                                <span class="badge {{ $badgeClass }}">{{ $history->getAttentionTypeLabel() }}</span>
                                            </td>
                                            <td class="px-3 py-2" style="min-width: 100px;">
                                                @php
                                                    $status = strtolower($history->status ?? '');
                                                    $badgeClass = match($status) {
                                                        'abierta' => 'bg-primary',
                                                        'en_proceso' => 'bg-warning',
                                                        'finalizada' => 'bg-success',
                                                        'cancelada' => 'bg-secondary',
                                                        default => 'bg-light text-dark',
                                                    };
                                                @endphp
                                                <span class="badge {{ $badgeClass }}">{{ ucfirst($history->status ?? '-') }}</span>
                                            </td>
                                            <td class="px-3 py-2" style="min-width: 120px;">
                                                @if($history->final_status)
                                                    @php
                                                        $finalStatus = strtolower($history->final_status ?? '');
                                                        $badgeClass = match($finalStatus) {
                                                            'hospitalizado' => 'bg-brand-header',
                                                            'egresado' => 'bg-success',
                                                            default => 'bg-light text-dark',
                                                        };
                                                    @endphp
                                                    <span class="badge {{ $badgeClass }}">{{ $history->getFinalStatusLabel() }}</span>
                                                @else
                                                    <span class="text-muted">-</span>
                                                @endif
                                            </td>
                                            <td class="align-middle text-center" style="min-width: 200px;">
                                                <a href="{{ route('medical-consultations.show', $history->id) }}" class="btn bg-brand-header btn-sm rounded-pill px-3 py-2 me-2">
                                                    <i class="fas fa-eye me-1"></i> Ver Detalle
                                                </a>
                                                <a href="{{ route('medical-consultations.print', $history->id) }}" class="btn btn-secondary btn-sm rounded-pill px-3 py-2" target="_blank">
                                                    <i class="fas fa-print me-1"></i> Imprimir
                                                </a>
                                            </td>
                                        </tr>
                                        @empty
                                        <tr>
                                            <td colspan="8" class="text-center py-4">
                                                <span class="text-muted">No hay historias clínicas registradas para este expediente.</span>
                                            </td>
                                        </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <!-- Pestaña de Citas Médicas -->
                        <div class="tab-pane fade" id="appointments" role="tabpanel" aria-labelledby="appointments-tab">
                            <div class="d-flex justify-content-end my-3">
                                <a href="{{ route('appointments.create', ['clinical_record_id' => $clinicalRecord->id]) }}" class="btn bg-brand-header">
                                    <i class="fas fa-calendar-plus me-2"></i>Agendar Cita
                                </a>
                            </div>
                            <div class="p-0" style="overflow-x: auto; width: 100%;">
                                <table class="table align-items-center mb-0 dataTable-table" id="appointments-table" data-datatable="true" style="min-width: 800px; width: 100%;">
                                    <thead>
                                        <tr>
                                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-3" style="min-width: 100px;">N° Cita</th>
                                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-3" style="min-width: 140px;">Fecha</th>
                                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-3" style="min-width: 120px;">Médico</th>
                                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-3" style="min-width: 120px;">Especialidad</th>
                                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-3" style="min-width: 120px;">Tipo</th>
                                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-3" style="min-width: 100px;">Estado</th>
                                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-3 text-center" style="min-width: 140px;">Acciones</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($clinicalRecord->appointments->sortByDesc('appointment_date') as $appointment)
                                        <tr>
                                            <td class="px-3 py-2" style="min-width: 100px;">
                                                <span class="text-sm font-weight-bold">{{ $appointment->appointment_number }}</span>
                                            </td>
                                            <td class="px-3 py-2" style="min-width: 140px;">
                                                <span class="text-sm text-secondary mb-0">{{ $appointment->appointment_date->format('d/m/Y H:i') }}</span>
                                            </td>
                                            <td class="px-3 py-2" style="min-width: 120px;">
                                                <span class="text-sm text-secondary mb-0">{{ $appointment->doctor->full_name ?? '-' }}</span>
                                            </td>
                                            <td class="px-3 py-2" style="min-width: 120px;">
                                                <span class="text-sm text-secondary mb-0">{{ $appointment->specialty->name ?? '-' }}</span>
                                            </td>
                                            <td class="px-3 py-2" style="min-width: 120px;">
                                                @php
                                                    $attentionType = strtolower($appointment->attention_type ?? '');
                                                    $badgeClass = match($attentionType) {
                                                        'emergencia' => 'bg-danger',
                                                        'urgencia' => 'bg-warning',
                                                        'consulta_externa' => 'bg-success',
                                                        default => 'bg-light text-dark',
                                                    };
                                                @endphp
                                                <span class="badge {{ $badgeClass }}">{{ ucwords(str_replace('_', ' ', $appointment->attention_type)) }}</span>
                                            </td>
                                            <td class="px-3 py-2" style="min-width: 100px;">
                                                <span class="badge {{ $appointment->status_badge }}">{{ $appointment->status_text }}</span>
                                            </td>
                                            <td class="align-middle text-center" style="min-width: 140px;">
                                                <a href="{{ route('appointments.show', $appointment->id) }}" class="btn bg-brand-header btn-sm rounded-pill px-3 py-2">
                                                    <i class="fas fa-eye me-1"></i> Ver Detalles
                                                </a>
                                            </td>
                                        </tr>
                                        @empty
                                        <tr>
                                            <td colspan="7" class="text-center py-4">
                                                <span class="text-muted">No hay citas médicas registradas para este expediente.</span>
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
    </div>
</div>

<!-- Modal para actualizar estado de cita -->
<div class="modal fade" id="statusModal" tabindex="-1" aria-labelledby="statusModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="statusModalLabel">Actualizar Estado de Cita</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="statusForm" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="notes" class="form-label">Notas (opcional)</label>
                        <textarea class="form-control" id="notes" name="notes" rows="3"></textarea>
                    </div>
                    <div class="mb-3" id="cancelledReasonDiv" style="display: none;">
                        <label for="cancelled_reason" class="form-label">Motivo de cancelación</label>
                        <input type="text" class="form-control" id="cancelled_reason" name="cancelled_reason">
                    </div>
                    <input type="hidden" id="status" name="status">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary" id="confirmButton">Confirmar</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal de Selección de Tipo de Atención (Solo para Administradores) -->
<div id="attentionTypeModal" class="search-modal" style="display:none; padding-top:14vh;">
    <div class="search-modal-overlay"></div>
    <div class="search-modal-content" style="max-width: 600px;">
        <div class="search-header bg-brand-header">
            <div class="search-icon"><i class="bi bi-heart-pulse text-white"></i></div>
            <div class="text-white fw-bold">Seleccionar Tipo de Atención</div>
            <button class="search-close ms-auto" id="closeAttentionModal" type="button"><i class="bi bi-x text-white"></i></button>
        </div>
        <div class="search-results" style="max-height:none; padding: 24px;">
            <p class="text-center mb-4">Elija el tipo de atención médica que necesita el paciente</p>
            
            <div class="row g-4">
                <div class="col-6">
                    <button type="button" class="btn attention-type-btn w-100 h-100 bg-brand-header text-white" 
                            data-attention="emergencia" style="min-height: 140px; border: none; border-radius: 20px; box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15); transition: all 0.3s ease;">
                        <div class="d-flex flex-column align-items-center">
                            <div class="mb-3" style="background: rgba(255,255,255,0.2); border-radius: 50%; width: 60px; height: 60px; display: flex; align-items: center; justify-content: center;">
                                <i class="bi bi-truck fa-2x text-white"></i>
                            </div>
                            <h4 class="fw-bold mb-2 text-white">EMERGENCIA</h4>
                            <p class="mb-0 opacity-90 text-white">Atención médica urgente</p>
                        </div>
                    </button>
                </div>
                <div class="col-6">
                    <button type="button" class="btn attention-type-btn w-100 h-100 bg-brand-header text-white" 
                            data-attention="consulta_externa" style="min-height: 140px; border: none; border-radius: 20px; box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15); transition: all 0.3s ease;">
                        <div class="d-flex flex-column align-items-center">
                            <div class="mb-3" style="background: rgba(255,255,255,0.2); border-radius: 50%; width: 60px; height: 60px; display: flex; align-items: center; justify-content: center;">
                                <i class="bi bi-calendar-check fa-2x text-white"></i>
                            </div>
                            <h4 class="fw-bold mb-2 text-white">CONSULTA EXTERNA</h4>
                            <p class="mb-0 opacity-90 text-white">Atención médica programada</p>
                        </div>
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal de Tipo de Profesional -->
<div id="professionalTypeModal" class="search-modal" style="display:none; padding-top:14vh;">
    <div class="search-modal-overlay"></div>
    <div class="search-modal-content" style="max-width: 520px;">
        <div class="search-header bg-brand-header">
            <div class="search-icon"><i class="bi bi-person-badge text-white"></i></div>
            <div class="text-white fw-bold">Tipo de Atención Profesional</div>
            <button class="search-close ms-auto" id="closeProfessionalModal" type="button"><i class="bi bi-x text-white"></i></button>
        </div>
        <div class="search-results" style="max-height:none; padding: 24px;">
            <p class="text-center mb-4">¿Quién atenderá al paciente?</p>
            
            <div class="row g-4 justify-content-center">
                <div class="col-6">
                    <button type="button" class="btn professional-type-btn w-100 h-100 bg-brand-header text-white" 
                            data-type="doctor" style="min-height: 120px; border: none; border-radius: 20px; box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15); transition: all 0.3s ease;">
                        <div class="d-flex flex-column align-items-center">
                            <div class="mb-3" style="background: rgba(255,255,255,0.2); border-radius: 50%; width: 60px; height: 60px; display: flex; align-items: center; justify-content: center;">
                                <i class="bi bi-person-fill-check fa-2x text-white"></i>
                            </div>
                            <h5 class="fw-bold mb-1 text-white">DOCTOR</h5>
                            <small class="opacity-90 text-white">Evaluación médica</small>
                        </div>
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>


<!-- Modal de Selección de Especialidad y Doctor -->
<div id="doctorSelectionModal" class="search-modal" style="display:none; padding-top:8vh;">
    <div class="search-modal-overlay"></div>
    <div class="search-modal-content" style="max-width: 900px;">
        <div class="search-header bg-brand-header">
            <div class="search-icon"><i class="bi bi-person-badge text-white"></i></div>
            <div class="text-white fw-bold">Selección de Especialidad y Doctor</div>
            <button class="search-close ms-auto" id="closeDoctorModal" type="button"><i class="bi bi-x text-white"></i></button>
        </div>
        <div class="search-results" style="max-height: 70vh; overflow-y: auto; padding: 24px;">
            <form id="consultationForm" action="{{ route('medical-consultations.store') }}" method="POST">
                @csrf
                <input type="hidden" name="clinical_record_id" value="{{ $clinicalRecord->id }}">
                <input type="hidden" name="attention_type" id="selectedAttentionType">
                <input type="hidden" name="professional_type" id="selectedProfessionalType">


                <!-- Datos de Enfermería -->
                <div class="row g-3 mb-4">
                    <div class="col-12">
                        <h6 class="bg-brand-header text-white px-3 py-2 rounded mb-3"><i class="fas fa-user-nurse me-2"></i>Datos de Enfermería</h6>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="consultation_reason" class="form-control-label">Motivo de Consulta *</label>
                            <textarea class="form-control @error('consultation_reason') is-invalid @enderror" 
                                      id="consultation_reason" name="consultation_reason" rows="3" required
                                      placeholder="Describa el motivo de la consulta...">{{ old('consultation_reason') }}</textarea>
                            @error('consultation_reason')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="nursing_note" class="form-control-label">Notas de Enfermería</label>
                            <textarea class="form-control @error('nursing_note') is-invalid @enderror" 
                                      id="nursing_note" name="nursing_note" rows="3"
                                      placeholder="Observaciones y notas de enfermería...">{{ old('nursing_note') }}</textarea>
                            @error('nursing_note')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Signos Vitales -->
                <div class="row g-3 mb-4">
                    <div class="col-12">
                        <h6 class="bg-brand-header text-white px-3 py-2 rounded mb-3"><i class="fas fa-heartbeat me-2"></i>Signos Vitales</h6>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="presion_arterial" class="form-control-label">Presión Arterial</label>
                            <input type="text" class="form-control @error('presion_arterial') is-invalid @enderror" 
                                   id="presion_arterial" name="presion_arterial" 
                                   value="{{ old('presion_arterial') }}"
                                   placeholder="Ej: 120/80">
                            @error('presion_arterial')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="frecuencia_cardiaca" class="form-control-label">Frecuencia Cardíaca</label>
                            <input type="text" class="form-control @error('frecuencia_cardiaca') is-invalid @enderror" 
                                   id="frecuencia_cardiaca" name="frecuencia_cardiaca" 
                                   value="{{ old('frecuencia_cardiaca') }}"
                                   placeholder="Ej: 80 lpm">
                            @error('frecuencia_cardiaca')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="temperatura" class="form-control-label">Temperatura (°C)</label>
                            <input type="text" class="form-control @error('temperatura') is-invalid @enderror" 
                                   id="temperatura" name="temperatura" 
                                   value="{{ old('temperatura') }}"
                                   placeholder="Ej: 36.5">
                            @error('temperatura')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="frecuencia_respiratoria" class="form-control-label">Frecuencia Respiratoria</label>
                            <input type="text" class="form-control @error('frecuencia_respiratoria') is-invalid @enderror" 
                                   id="frecuencia_respiratoria" name="frecuencia_respiratoria" 
                                   value="{{ old('frecuencia_respiratoria') }}"
                                   placeholder="Ej: 16 rpm">
                            @error('frecuencia_respiratoria')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="peso" class="form-control-label">Peso (kg)</label>
                            <input type="text" class="form-control @error('peso') is-invalid @enderror" 
                                   id="peso" name="peso" 
                                   value="{{ old('peso') }}"
                                   placeholder="Ej: 70">
                            @error('peso')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="talla" class="form-control-label">Talla (cm)</label>
                            <input type="text" class="form-control @error('talla') is-invalid @enderror" 
                                   id="talla" name="talla" 
                                   value="{{ old('talla') }}"
                                   placeholder="Ej: 170">
                            @error('talla')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="saturacion_o2" class="form-control-label">Saturación O2 (%)</label>
                            <input type="text" class="form-control @error('saturacion_o2') is-invalid @enderror" 
                                   id="saturacion_o2" name="saturacion_o2" 
                                   value="{{ old('saturacion_o2') }}"
                                   placeholder="Ej: 98">
                            @error('saturacion_o2')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="glicemia" class="form-control-label">Glicemia (mg/dl)</label>
                            <input type="text" class="form-control @error('glicemia') is-invalid @enderror" 
                                   id="glicemia" name="glicemia" 
                                   value="{{ old('glicemia') }}"
                                   placeholder="Ej: 90">
                            @error('glicemia')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Datos del Acompañante -->
                <div class="row g-3 mb-4">
                    <div class="col-12">
                        <h6 class="bg-brand-header text-white px-3 py-2 rounded mb-3"><i class="fas fa-user-friends me-2"></i>Datos del Acompañante (Opcional)</h6>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="companion_name" class="form-control-label">Nombre del Acompañante</label>
                            <input type="text" class="form-control @error('companion_name') is-invalid @enderror" 
                                   id="companion_name" name="companion_name" 
                                   value="{{ old('companion_name') }}"
                                   placeholder="Nombre completo">
                            @error('companion_name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="companion_phone" class="form-control-label">Teléfono</label>
                            <input type="tel" class="form-control @error('companion_phone') is-invalid @enderror" 
                                   id="companion_phone" name="companion_phone" 
                                   value="{{ old('companion_phone') }}"
                                   placeholder="Ej: 12345678" maxlength="8">
                            @error('companion_phone')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="companion_email" class="form-control-label">Email</label>
                            <input type="email" class="form-control @error('companion_email') is-invalid @enderror" 
                                   id="companion_email" name="companion_email" 
                                   value="{{ old('companion_email') }}"
                                   placeholder="ejemplo@email.com">
                            @error('companion_email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="companion_relationship_id" class="form-control-label">Relación</label>
                            <select class="form-control @error('companion_relationship_id') is-invalid @enderror" 
                                    id="companion_relationship_id" name="companion_relationship_id">
                                <option value="">Seleccionar relación...</option>
                                @foreach($companionRelationships as $relationship)
                                    <option value="{{ $relationship->id }}" 
                                            {{ old('companion_relationship_id') == $relationship->id ? 'selected' : '' }}>
                                        {{ $relationship->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('companion_relationship_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="companion_dpi" class="form-control-label">DPI/CUI</label>
                            <input type="text" class="form-control @error('companion_dpi') is-invalid @enderror" 
                                   id="companion_dpi" name="companion_dpi" 
                                   value="{{ old('companion_dpi') }}"
                                   placeholder="Ej: 1234567890123" maxlength="13">
                            @error('companion_dpi')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-9">
                        <div class="form-group">
                            <label for="companion_address" class="form-control-label">Dirección</label>
                            <input type="text" class="form-control @error('companion_address') is-invalid @enderror" 
                                   id="companion_address" name="companion_address" 
                                   value="{{ old('companion_address') }}"
                                   placeholder="Dirección completa del acompañante">
                            @error('companion_address')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Notas Adicionales de Enfermería -->
                <div class="row g-3 mb-4">
                    <div class="col-12">
                        <h6 class="bg-brand-header text-white px-3 py-2 rounded mb-3"><i class="fas fa-sticky-note me-2"></i>Notas Adicionales de Enfermería</h6>
                    </div>
                    <div class="col-12">
                        <div class="form-group">
                            <label for="notas_adicionales" class="form-control-label">Observaciones Adicionales</label>
                            <textarea class="form-control @error('notas_adicionales') is-invalid @enderror" 
                                      id="notas_adicionales" name="notas_adicionales" rows="3"
                                      placeholder="Cualquier observación adicional sobre el estado del paciente...">{{ old('notas_adicionales') }}</textarea>
                            @error('notas_adicionales')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Selección de Especialidad y Doctor -->
                <div class="row g-3 mb-4">
                    <div class="col-12">
                        <h6 class="bg-brand-header text-white px-3 py-2 rounded mb-3"><i class="fas fa-user-md me-2"></i>Selección de Especialidad y Doctor</h6>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="specialty_id" class="form-control-label">Especialidad *</label>
                            <select class="form-control @error('specialty_id') is-invalid @enderror" 
                                    id="specialty_id" name="specialty_id" required>
                                <option value="">Seleccionar especialidad...</option>
                                @foreach($specialties as $specialty)
                                    <option value="{{ $specialty->id }}">{{ $specialty->name }}</option>
                                @endforeach
                            </select>
                            @error('specialty_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="doctor_id" class="form-control-label">Doctor *</label>
                            <select class="form-control @error('doctor_id') is-invalid @enderror" 
                                    id="doctor_id" name="doctor_id" required disabled>
                                <option value="">Primero seleccione una especialidad</option>
                            </select>
                            @error('doctor_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="d-flex justify-content-end mt-4">
                    <button type="button" class="btn btn-secondary me-2" id="cancelDoctorModal">
                        <i class="fas fa-times me-2"></i>Cancelar
                    </button>
                    @canany(['emergencia.consultas.crear', 'consulta_externa.consultas.crear'])
                        <button type="submit" class="btn bg-brand-header text-white">
                            <i class="fas fa-plus me-2"></i>Crear Consulta
                        </button>
                    @endcanany
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<style>
/* Efectos hover para botones */
.attention-type-btn:hover {
    transform: translateY(-5px) !important;
    box-shadow: 0 15px 35px rgba(0, 0, 0, 0.2) !important;
}

.professional-type-btn:hover {
    transform: translateY(-5px) !important;
    box-shadow: 0 15px 35px rgba(0, 0, 0, 0.2) !important;
}

/* Animación de entrada para modales */
.search-modal-content {
    animation: modalSlideIn 0.3s ease-out;
}

@keyframes modalSlideIn {
    from {
        opacity: 0;
        transform: scale(0.9) translateY(-20px);
    }
    to {
        opacity: 1;
        transform: scale(1) translateY(0);
    }
}
</style>
<script>
// Función para mostrar modal personalizado
function showModal(modalId) {
    const modal = document.getElementById(modalId);
    modal.style.display = 'flex';
    modal.classList.add('show');
    
    // Aplicar animación
    setTimeout(() => {
        const content = modal.querySelector('.search-modal-content');
        if (content) {
            content.style.transform = 'scale(1) translateY(0)';
            content.style.opacity = '1';
        }
    }, 10);
}

// Función para ocultar modal personalizado
function hideModal(modalId) {
    const modal = document.getElementById(modalId);
    const content = modal.querySelector('.search-modal-content');
    
    if (content) {
        content.style.transform = 'scale(0.9) translateY(-20px)';
        content.style.opacity = '0';
    }
    
    setTimeout(() => {
        modal.style.display = 'none';
        modal.classList.remove('show');
    }, 300);
}

// Detección automática de rol
const userRoleName = @json(auth()->user()->getRoleName());
const isAdmin = @json(auth()->user()->isAdmin());

// Manejo del botón "Crear Historia Clínica"
document.getElementById('createConsultationBtn').addEventListener('click', function() {
    if (isAdmin) {
        // Si es administrador, mostrar modal de selección de tipo de atención
        showModal('attentionTypeModal');
    } else {
        // Si no es administrador, mostrar modal de tipo de profesional directamente
        const attentionType = userRoleName === 'Emergencia' ? 'emergencia' : 'consulta_externa';
        document.getElementById('selectedAttentionType').value = attentionType;
        showModal('professionalTypeModal');
    }
});

// Event listeners para botones de cerrar
document.getElementById('closeAttentionModal').addEventListener('click', function() {
    hideModal('attentionTypeModal');
});

// Manejo de selección de tipo de atención (solo para administradores)
document.querySelectorAll('.attention-type-btn').forEach(btn => {
    btn.addEventListener('click', function() {
        const attentionType = this.dataset.attention;
        document.getElementById('selectedAttentionType').value = attentionType;
        hideModal('attentionTypeModal');
        showModal('professionalTypeModal');
    });
});

// Event listeners para botones de cerrar
document.getElementById('closeProfessionalModal').addEventListener('click', function() {
    hideModal('professionalTypeModal');
});

document.getElementById('closeDoctorModal').addEventListener('click', function() {
    hideModal('doctorSelectionModal');
});

document.getElementById('cancelDoctorModal').addEventListener('click', function() {
    hideModal('doctorSelectionModal');
});


// Manejo de selección de tipo de profesional
document.querySelectorAll('.professional-type-btn').forEach(btn => {
    btn.addEventListener('click', function() {
        const professionalType = this.dataset.type;
        document.getElementById('selectedProfessionalType').value = professionalType;
        
        // Cerrar modal actual
        hideModal('professionalTypeModal');
        
        // Mostrar el modal de selección de especialidad y doctor
        showModal('doctorSelectionModal');
        
        // Habilitar selección de especialidad y doctor para doctores
        document.getElementById('specialty_id').disabled = false;
        document.getElementById('doctor_id').disabled = true; // Se habilita cuando se selecciona especialidad
        document.getElementById('specialty_id').required = true;
        document.getElementById('doctor_id').required = true;
    });
});

// Datos de doctores y especialidades
const doctors = @json($doctors);
const specialties = @json($specialties);

// Filtrar doctores por especialidad
document.getElementById('specialty_id').addEventListener('change', function() {
    const specialtyId = this.value;
    const doctorSelect = document.getElementById('doctor_id');
    
    // Limpiar opciones
    doctorSelect.innerHTML = '<option value="">Primero seleccione una especialidad</option>';
    
    if (specialtyId) {
        // Habilitar select
        doctorSelect.disabled = false;
        
        // Filtrar doctores por especialidad
        const filteredDoctors = doctors.filter(doctor => doctor.specialty_id == specialtyId);
        
        // Agregar opciones
        filteredDoctors.forEach(doctor => {
            const option = document.createElement('option');
            option.value = doctor.id;
            option.textContent = `${doctor.first_name} ${doctor.first_lastname}`;
            doctorSelect.appendChild(option);
        });
    } else {
        doctorSelect.disabled = true;
    }
});


function updateStatus(appointmentId, status) {
    

    const statusTexts = {
        'confirmada': 'confirmar',
        'atendida': 'marcar como atendida',
        'perdida': 'marcar como perdida',
        'cancelada': 'cancelar'
    };

    const modalTitle = document.getElementById('statusModalLabel');
    const confirmButton = document.getElementById('confirmButton');
    const statusForm = document.getElementById('statusForm');
    const statusInput = document.getElementById('status');
    const cancelledReasonDiv = document.getElementById('cancelledReasonDiv');
    const cancelledReasonInput = document.getElementById('cancelled_reason');
    const notesInput = statusForm.querySelector('textarea[name="notes"]');

    modalTitle.textContent = `¿Desea ${statusTexts[status]} esta cita?`;
    confirmButton.textContent = 'Confirmar';
    statusForm.action = `{{ url('appointments') }}/${appointmentId}/status`;
    statusInput.value = status;

    

    // Limpiar notas anteriores
    if (notesInput) {
        notesInput.value = '';
    }

    // Mostrar/ocultar campo de motivo de cancelación
    if (status === 'cancelada') {
        cancelledReasonDiv.style.display = 'block';
        cancelledReasonInput.required = true;
    } else {
        cancelledReasonDiv.style.display = 'none';
        cancelledReasonInput.required = false;
        cancelledReasonInput.value = '';
    }

    // Mostrar modal
    const modal = new bootstrap.Modal(document.getElementById('statusModal'));
    modal.show();
}

// Manejar envío del formulario
document.addEventListener('DOMContentLoaded', function() {
    document.getElementById('statusForm').addEventListener('submit', function(e) {
        e.preventDefault();
        
        const statusInput = document.getElementById('status');
        const cancelReasonInput = document.getElementById('cancelled_reason');
        const notesInput = this.querySelector('textarea[name="notes"]');
        
        // Validación para cancelación
        if (statusInput.value === 'cancelada') {
            if (!cancelReasonInput.value || cancelReasonInput.value.trim() === '') {
                showErrorToast('Debe especificar un motivo para cancelar la cita', 'Campo Requerido');
                cancelReasonInput.focus();
                return;
            }
        }
        
        const submitBtn = this.querySelector('button[type="submit"]');
        const originalText = submitBtn.innerHTML;
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Procesando...';
        
        // Crear FormData manualmente para control total
        const formData = new FormData();
        formData.append('_token', document.querySelector('meta[name="csrf-token"]').getAttribute('content'));
        formData.append('_method', 'PUT');
        formData.append('status', statusInput.value);
        formData.append('notes', notesInput.value || '');
        
        if (statusInput.value === 'cancelada') {
            formData.append('cancelled_reason', cancelReasonInput.value);
        }

        
        fetch(this.action, {
            method: 'POST',
            body: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            }
        })
        .then(response => {
            if (!response.ok) {
                return response.json().then(err => Promise.reject(err));
            }
            return response.json();
        })
        .then(data => {
            if (data.success) {
                const modal = bootstrap.Modal.getInstance(document.getElementById('statusModal'));
                modal.hide();
                
                const statusTexts = {
                    'confirmada': 'confirmada',
                    'atendida': 'atendida', 
                    'perdida': 'perdida',
                    'cancelada': 'cancelada'
                };
                
                showSuccessToast(
                    `La cita ha sido ${statusTexts[statusInput.value]} correctamente`,
                    'Estado Actualizado'
                );
                
                setTimeout(() => {
                    window.location.reload();
                }, 1500);
            } else {
                showErrorToast(data.message || 'Error desconocido', 'Error');
            }
        })
        .catch(error => {
            console.error('Error completo:', error);
            if (error.errors) {
                let errorMsg = '';
                Object.keys(error.errors).forEach(key => {
                    errorMsg += `${error.errors[key].join(', ')} `;
                });
                showErrorToast(errorMsg.trim(), 'Errores de Validación');
            } else {
                showErrorToast(error.message || 'Error inesperado', 'Error');
            }
        })
        .finally(() => {
            submitBtn.disabled = false;
            submitBtn.innerHTML = originalText;
        });
    });
});

// Las funciones de toast ahora están globalmente disponibles en el layout
</script>
@endpush 