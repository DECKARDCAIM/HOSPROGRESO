@extends('layouts.panel')

@section('title', 'Tipos de Horario')
@section('breadcrumb', 'Tipos de Horario')

@section('content')

<style>
@media (max-width: 1199px) {
    .card-body .table-responsive { border-radius: 8px; overflow: hidden; }
    .dataTable-container { border-radius: 8px; box-shadow: 0 2px 8px rgba(0,0,0,0.1); }
    .btn { margin: 2px; min-width: 80px; }
    .card-body { padding: 1rem; }
    .card-body .row { margin-bottom: 1rem; }
}
</style>

<div class="container-fluid py-4">
    <div class="row">
        <div class="col-12">
            <div class="card mb-4">
                <div class="card-header pb-0 bg-info">
                    <div class="row align-items-center">
                        <div class="col-md-8">
                            <h6 class="text-white mb-0">Tipos de Horario</h6>
                            <p class="text-sm text-white opacity-8 mb-0">
                                Este módulo permite gestionar los tipos de horario registrados en el sistema.
                            </p>
                        </div>
                        <div class="col-md-4 text-end">
                            <a href="{{ route('schedule-types.create') }}" class="btn btn-sm btn-white">
                                <i class="bi bi-plus me-2"></i>Nuevo Tipo de Horario
                            </a>
                        </div>
                    </div>
                </div>

                <div class="card-body pt-3 pb-2">
                    <form action="{{ url('/schedule-types') }}" method="GET" class="mb-0">
                        <div class="row align-items-center">
                            <div class="col-md-4 col-lg-3 mb-2 mb-md-0">
                                <select name="status" class="form-select form-select-lg border border-info" onchange="this.form.submit()">
                                    <option value="active" {{ $status === 'active' ? 'selected' : '' }}>Activos</option>
                                    <option value="inactive" {{ $status === 'inactive' ? 'selected' : '' }}>Inactivos</option>
                                </select>
                            </div>
                            <div class="col-md-6 col-lg-5 mb-2 mb-md-0">
                                <div class="input-group input-group-lg">
                                    <span class="input-group-text bg-info text-white border-info">
                                        <i class="bi bi-search"></i>
                                    </span>
                                    <input type="text" name="search" class="form-control border border-info" placeholder="Buscar por nombre..." value="{{ $search }}">
                                    <button type="submit" class="btn bg-gradient-info text-white">
                                        <i class="bi bi-funnel me-2"></i>Filtrar
                                    </button>
                                </div>
                            </div>
                            @if($search)
                            <div class="col-auto ms-2">
                                <a href="{{ url('/schedule-types?status=' . $status) }}" class="btn btn-outline-secondary">
                                    <i class="bi bi-x me-2"></i>Limpiar búsqueda
                                </a>
                            </div>
                            @endif
                        </div>
                    </form>
                </div>

                <div class="card-body px-0 pt-0 pb-2">
                    <div class="table-responsive p-0">
                        <table class="table align-items-center mb-0 dataTable-table" id="datatable-basic" data-datatable="true" style="overflow-x: auto; min-width: 900px;">
                            <thead>
                                <tr>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-3" style="min-width: 160px;">Nombre</th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-3" style="min-width: 120px;">Especialidad</th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-3" style="min-width: 180px;">Días de la Semana</th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-3" style="min-width: 100px;">Hora Inicio</th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-3" style="min-width: 100px;">Hora Fin</th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-3" style="min-width: 80px;">Cupos</th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-3" style="min-width: 80px;">Estado</th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-3 text-center" style="min-width: 140px;">Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($scheduleTypes as $scheduleType)
                                <tr>
                                    <td style="min-width: 160px;">
                                        <div class="d-flex px-3 py-2">
                                            <div class="avatar avatar-sm me-3 bg-info rounded-circle">
                                                <span class="text-white font-weight-bold">{{ substr($scheduleType->name, 0, 1) }}</span>
                                            </div>
                                            <div class="d-flex flex-column justify-content-center">
                                                <h6 class="mb-0 text-sm">{{ $scheduleType->name }}</h6>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-3 py-2" style="min-width: 120px;">
                                        @if($scheduleType->specialty)
                                            <span class="badge bg-primary">{{ $scheduleType->specialty->name }}</span>
                                        @else
                                            <span class="text-muted">Sin especialidad</span>
                                        @endif
                                    </td>
                                    <td class="px-3 py-2" style="min-width: 180px;">
                                        @php
                                            $dayNames = [
                                                1 => 'Lun',
                                                2 => 'Mar', 
                                                3 => 'Mié',
                                                4 => 'Jue',
                                                5 => 'Vie',
                                                6 => 'Sáb',
                                                7 => 'Dom'
                                            ];
                                            
                                            $selectedDays = [];
                                            $daysData = $scheduleType->days_of_week;
                                            
                                            // Debug temporal - remover después
                                            // dd($daysData, gettype($daysData), is_array($daysData));
                                            
                                            if($daysData) {
                                                // Si es string JSON, decodificar
                                                if(is_string($daysData)) {
                                                    $daysData = json_decode($daysData, true);
                                                }
                                                
                                                // Si ahora es array, procesar
                                                if(is_array($daysData)) {
                                                    foreach($daysData as $dayNumber) {
                                                        $dayNumber = (int) $dayNumber; // Asegurar que es entero
                                                        if(isset($dayNames[$dayNumber])) {
                                                            $selectedDays[] = $dayNames[$dayNumber];
                                                        }
                                                    }
                                                }
                                            }
                                        @endphp
                                        @if(count($selectedDays) > 0)
                                            @foreach($selectedDays as $day)
                                                <span class="badge bg-secondary me-0">{{ $day }}</span>
                                            @endforeach
                                        @else
                                            <span class="text-muted">No especificado</span>
                                        @endif
                                    </td>
                                    <td class="px-3 py-2" style="min-width: 100px;">
                                        @if($scheduleType->start_time)
                                            <span class="text-sm">{{ \Carbon\Carbon::parse($scheduleType->start_time)->format('H:i') }}</span>
                                        @else
                                            <span class="text-muted">--:--</span>
                                        @endif
                                    </td>
                                    <td class="px-3 py-2" style="min-width: 100px;">
                                        @if($scheduleType->end_time)
                                            <span class="text-sm">{{ \Carbon\Carbon::parse($scheduleType->end_time)->format('H:i') }}</span>
                                        @else
                                            <span class="text-muted">--:--</span>
                                        @endif
                                    </td>
                                    <td class="px-3 py-2" style="min-width: 80px;">
                                        @if($scheduleType->max_patients)
                                            <span class="badge bg-info">{{ $scheduleType->max_patients }}</span>
                                        @else
                                            <span class="text-muted">Sin límite</span>
                                        @endif
                                    </td>
                                    <td class="px-3 py-2" style="min-width: 80px;">
                                        @if($scheduleType->is_active)
                                            <span class="badge bg-success">Activo</span>
                                        @else
                                            <span class="badge bg-secondary">Inactivo</span>
                                        @endif
                                    </td>
                                    <td class="align-middle text-center" style="min-width: 140px;">
                                        @if ($status === 'active')
                                            <a href="{{ route('schedule-types.edit', $scheduleType) }}" class="btn btn-info rounded-pill px-3 py-2 me-2">
                                                <i class="bi bi-pencil me-1"></i>Editar
                                            </a>
                                            <form action="{{ route('schedule-types.destroy', $scheduleType) }}" method="POST" class="d-inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-danger rounded-pill px-3 py-2">
                                                    <i class="bi bi-trash me-1"></i>Eliminar
                                                </button>
                                            </form>
                                        @else
                                            <form action="{{ route('schedule-types.reactivate', $scheduleType->id) }}" method="POST" class="d-inline">
                                                @csrf
                                                <button type="submit" class="btn btn-success rounded-pill px-3 py-2">
                                                    <i class="bi bi-power me-1"></i>Reactivar
                                                </button>
                                            </form>
                                        @endif
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="8" class="text-center py-4">
                                        <span class="text-muted">No hay tipos de horario registrados.</span>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    <div class="d-flex justify-content-center mt-4">
                        {{ $scheduleTypes->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection 