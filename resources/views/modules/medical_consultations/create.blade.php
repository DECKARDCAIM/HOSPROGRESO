@extends('layouts.panel')

@section('title', 'Nueva Historia Clínica')
@section('breadcrumb', 'Historias Clínicas / Nueva')

@section('content')
    <div class="container-fluid py-4">
        <div class="row">
            <div class="col-12">
                <div class="card mb-4">
                    <div class="card-header pb-0 bg-info">
                        <div class="row align-items-center">
                            <div class="col-md-8">
                            <h6 class="text-white mb-0">Nueva Historia Clínica</h6>
                                <p class="text-sm text-white opacity-8 mb-0">
                                Paciente: {{ $clinicalRecord->full_name }} | CUI: {{ $clinicalRecord->cui }}
                                </p>
                            </div>
                            <div class="col-md-4 text-end">
                            <a href="{{ route('clinical-records.show', $clinicalRecord->id) }}" class="btn btn-sm btn-white">
                                    <i class="fas fa-arrow-left me-2"></i>Volver
                                </a>
                            </div>
                        </div>
                    </div>

                    <div class="card-body">
                    <div class="row justify-content-center">
                        <div class="col-md-8">
                            <div class="text-center mb-4">
                                <h5 class="text-info">Confirmar Tipo de Atención</h5>
                                <p class="text-muted">Se creará una nueva historia clínica con el siguiente tipo de atención:</p>
                            </div>

                            <div class="card border-info">
                                <div class="card-body text-center">
                                    <div class="mb-3">
                                        <i class="fas fa-user-md fa-3x text-info"></i>
                                    </div>
                                    <h4 class="text-info">
                                        @if($attentionType == 'emergencia')
                                            <i class="fas fa-ambulance me-2"></i>Emergencia
                                        @else
                                            <i class="fas fa-stethoscope me-2"></i>Consulta Externa
                                        @endif
                                    </h4>
                                    <p class="text-muted">
                                        @if($attentionType == 'emergencia')
                                            Atención médica de urgencia para casos que requieren intervención inmediata.
                                        @else
                                            Atención médica programada para consultas regulares y seguimiento.
                                        @endif
                                    </p>
                                </div>
                            </div>

                            <form action="{{ route('medical-consultations.store') }}" method="POST" class="mt-4">
                                @csrf
                                <input type="hidden" name="clinical_record_id" value="{{ $clinicalRecord->id }}">
                                <input type="hidden" name="attention_type" value="{{ $attentionType }}">

                            <div class="row">
                                <div class="col-md-6">
                                        <a href="{{ route('clinical-records.show', $clinicalRecord->id) }}" class="btn btn-secondary w-100">
                                            <i class="fas fa-times me-2"></i>Cancelar
                                        </a>
                                    </div>
                                <div class="col-md-6">
                                        <button type="submit" class="btn btn-info w-100">
                                            <i class="fas fa-plus me-2"></i>Crear Historia Clínica
                                        </button>
                                    </div>
                                </div>
                            </form>
                            </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 