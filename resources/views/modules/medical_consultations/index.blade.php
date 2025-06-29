@extends('layouts.panel')

@section('title', 'Historias Clínicas en Proceso')
@section('breadcrumb', 'Historias Clínicas')

@section('content')
    <div class="container-fluid py-4">
        <div class="row">
            <div class="col-12">
                <div class="card mb-4">
                    <div class="card-header pb-0 bg-info">
                        <div class="row align-items-center">
                            <div class="col-md-8">
                                <h6 class="text-white mb-0">Historias Clínicas en Proceso</h6>
                                <p class="text-sm text-white opacity-8 mb-0">
                                    Historias clínicas abiertas y en proceso de atención.
                                </p>
                            </div>
                            <div class="col-md-4 text-end">
                                <a href="{{ route('clinical-records.index') }}" class="btn btn-sm btn-white">
                                    <i class="fas fa-chevron-left me-2"></i>Ver Expedientes
                                </a>
                            </div>
                        </div>
                    </div>

                    <div class="card-body px-0 pt-0 pb-2">
                        <div class="table-responsive p-0">
                            <table class="table align-items-center mb-0" id="datatable-basic">
                                <thead>
                                    <tr>
                                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-3">Paciente</th>
                                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-3">Tipo de Atención</th>
                                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-3">Doctor</th>
                                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-3">Especialidad</th>
                                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-3">Fecha</th>
                                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-3">Estado</th>
                                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-3 text-center">Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($medicalConsultations as $consultation)
                                        <tr>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <div class="ms-3">
                                                        <h6 class="mb-0">{{ $consultation->clinicalRecord->full_name }}</h6>
                                                        <small class="text-muted">CUI: {{ $consultation->clinicalRecord->cui }}</small>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                @php
                                                    $attentionType = strtolower($consultation->attention_type ?? '');
                                                    $badgeClass = match($attentionType) {
                                                        'emergencia' => 'bg-danger',
                                                        'consulta_externa' => 'bg-success',
                                                        default => 'bg-light text-dark',
                                                    };
                                                @endphp
                                                <span class="badge {{ $badgeClass }}">{{ $consultation->getAttentionTypeLabel() }}</span>
                                            </td>
                                            <td>{{ $consultation->doctor->full_name ?? 'Pendiente' }}</td>
                                            <td>{{ $consultation->specialty->name ?? 'Pendiente' }}</td>
                                            <td>{{ $consultation->consultation_date->format('d/m/Y H:i') }}</td>
                                            <td>
                                                @php
                                                    $status = strtolower($consultation->status ?? '');
                                                    $badgeClass = match($status) {
                                                        'abierta' => 'bg-primary',
                                                        'en_proceso' => 'bg-warning',
                                                        'finalizada' => 'bg-success',
                                                        'cancelada' => 'bg-dark',
                                                        default => 'bg-light text-dark',
                                                    };
                                                    $statusText = match($status) {
                                                        'en_proceso' => 'En proceso',
                                                        default => ucfirst($consultation->status ?? '-')
                                                    };
                                                @endphp
                                                <span class="badge {{ $badgeClass }}">{{ $statusText }}</span>
                                            </td>
                                            <td class="align-middle text-center">
                                                @if($consultation->status === 'abierta')
                                                    <a href="{{ route('medical-consultations.process', $consultation) }}" class="btn btn-sm btn-info" title="Iniciar Proceso">
                                                        <i class="fas fa-play me-1"></i> Iniciar Proceso
                                                    </a>
                                                @elseif($consultation->status === 'en_proceso')
                                                    <a href="{{ route('medical-consultations.process', $consultation) }}" class="btn btn-sm btn-info" title="Continuar Proceso">
                                                        <i class="fas fa-forward me-1"></i> Continuar Proceso
                                                    </a>
                                                @endif
                                                <a href="{{ route('medical-consultations.show', $consultation) }}" class="btn btn-sm btn-secondary ms-1" title="Ver Detalles">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="7" class="text-center py-4">
                                                <span class="text-muted">No hay historias clínicas en proceso.</span>
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                        
                        @if($medicalConsultations->hasPages())
                            <div class="card-footer">
                                <div class="d-flex justify-content-center">
                                    {{ $medicalConsultations->links() }}
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Script para inicializar tooltips de Bootstrap -->
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
        var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl)
        });
        
        // Auto-cerrar alertas después de 5 segundos
        setTimeout(function() {
            var alertElement = document.getElementById('notification-alert');
            if (alertElement) {
                var alert = bootstrap.Alert.getInstance(alertElement);
                if (alert) {
                    alert.close();
                } else {
                    alertElement.classList.remove('show');
                    setTimeout(function() {
                        alertElement.remove();
                    }, 150);
                }
            }
        }, 5000);
    });
    </script>
@endsection 