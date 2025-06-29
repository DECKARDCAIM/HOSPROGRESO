@extends('layouts.panel')
@section('title', 'Tipos de Horario')
@section('breadcrumb', 'Tipos de Horario')
@section('content')
<div class="container-fluid py-4">
    <div class="row">
        <div class="col-12">
            <div class="card mb-4">
                <div class="card-header pb-0 bg-info">
                    <div class="row align-items-center">
                        <div class="col-md-8">
                            <h6 class="text-white mb-0">Tipos de Horario</h6>
                            <p class="text-sm text-white opacity-8 mb-0">
                                Este módulo permite gestionar los tipos de horario de los médicos.
                            </p>
                        </div>
                        <div class="col-md-4 text-end">
                            <a href="{{ route('schedule-types.create') }}" class="btn btn-sm btn-white">
                                <i class="fas fa-plus me-2"></i>Nuevo Tipo de Horario
                            </a>
                        </div>
                    </div>
                </div>
                <div class="card-body px-0 pt-0 pb-2">
                    <div class="table-responsive p-0">
                        <table class="table align-items-center mb-0">
                            <thead>
                                <tr>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-3">Nombre</th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-3">Especialidad</th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-3">Días</th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-3">Hora Inicio</th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-3">Hora Fin</th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-3">Cupo Máximo</th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-3 text-center">Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($scheduleTypes as $type)
                                    <tr>
                                        <td class="px-3 py-2">
                                            <div class="d-flex align-items-center">
                                                <div class="avatar avatar-sm me-3 bg-info rounded-circle">
                                                    <span class="text-white font-weight-bold">{{ substr($type->name, 0, 1) }}</span>
                                                </div>
                                                <div class="d-flex flex-column justify-content-center">
                                                    <h6 class="mb-0 text-sm">{{ $type->name }}</h6>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-3 py-2">{{ $type->specialty->name ?? '-' }}</td>
                                        <td class="px-3 py-2">
                                            @php
                                                $dias = is_array($type->days_of_week) ? $type->days_of_week : (json_decode($type->days_of_week, true) ?: []);
                                            @endphp
                                            @foreach($dias as $day)
                                                <span class="badge bg-gradient-info me-1">{{ [1=>'LUN',2=>'MAR',3=>'MIÉ',4=>'JUE',5=>'VIE',6=>'SÁB',7=>'DOM'][$day] }}</span>
                                            @endforeach
                                        </td>
                                        <td class="px-3 py-2">{{ $type->start_time }}</td>
                                        <td class="px-3 py-2">{{ $type->end_time }}</td>
                                        <td class="px-3 py-2">{{ $type->max_patients }}</td>
                                        <td class="align-middle text-center">
                                            <a href="{{ route('schedule-types.edit', $type->id) }}" class="btn btn-info rounded-pill px-3 py-2 me-2">
                                                <i class="fas fa-edit me-1"></i> Editar
                                            </a>
                                            <form action="{{ route('schedule-types.destroy', $type->id) }}" method="POST" class="d-inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-danger rounded-pill px-3 py-2">
                                                    <i class="fas fa-trash me-1"></i> Eliminar
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                @empty
                                    <tr><td colspan="7" class="text-center py-4"><span class="text-muted">No hay tipos de horario registrados.</span></td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 