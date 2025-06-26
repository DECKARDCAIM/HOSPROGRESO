@extends('layouts.panel')

@section('title', 'Consultas Médicas')
@section('breadcrumb', 'Consultas Médicas')

@section('content')
    <div class="container-fluid py-4">
        <div class="row">
            <div class="col-12">
                <div class="card mb-4">
                    <div class="card-header pb-0 bg-info">
                        <div class="row align-items-center">
                            <div class="col-md-8">
                                <h6 class="text-white mb-0">Consultas Médicas</h6>
                                <p class="text-sm text-white opacity-8 mb-0">
                                    Este módulo permite gestionar las consultas médicas de los pacientes.
                                </p>
                            </div>
                            <div class="col-md-4 text-end">
                                <a href="{{ route('medical-consultations.create') }}" class="btn btn-sm btn-white">
                                    <i class="fas fa-plus me-2"></i>Nueva Consulta
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
                                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-3">Doctor</th>
                                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-3">Especialidad</th>
                                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-3">Fecha</th>
                                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-3">Motivo</th>
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
                                                        <small class="text-muted">#{{ $consultation->clinicalRecord->history_number }}</small>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>{{ $consultation->doctor->full_name }}</td>
                                            <td>{{ $consultation->specialty->name }}</td>
                                            <td>{{ $consultation->consultation_date->format('d/m/Y H:i') }}</td>
                                            <td>
                                                <span class="text-truncate d-inline-block" style="max-width: 200px;" title="{{ $consultation->consultation_reason }}">
                                                    {{ $consultation->consultation_reason }}
                                                </span>
                                            </td>
                                            <td>
                                                <div class="btn-group" role="group">
                                                    <a href="{{ route('medical-consultations.show', $consultation) }}" 
                                                       class="btn btn-sm btn-info" title="Ver">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                    <a href="{{ route('medical-consultations.edit', $consultation) }}" 
                                                       class="btn btn-sm btn-warning" title="Editar">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                    <form action="{{ route('medical-consultations.destroy', $consultation) }}" 
                                                          method="POST" class="d-inline">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-sm btn-danger" 
                                                                onclick="return confirm('¿Estás seguro de eliminar esta consulta?')" title="Eliminar">
                                                            <i class="fas fa-trash"></i>
                                                        </button>
                                                    </form>
                                                </div>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="6" class="text-center">No hay consultas médicas registradas</td>
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