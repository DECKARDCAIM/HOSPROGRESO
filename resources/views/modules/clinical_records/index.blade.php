@extends('layouts.panel')

@section('title', 'Expedientes Clínicos')
@section('breadcrumb', 'Expedientes Clínicos')

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
                            <h6 class="text-white mb-0">Expedientes Clínicos</h6>
                            <p class="text-sm text-white opacity-8 mb-0">
                                Este módulo permite gestionar los expedientes clínicos registrados en el sistema.
                            </p>
                        </div>
                        <div class="col-md-4 text-end">
                            <a href="{{ route('clinical-records.create') }}" class="btn btn-sm btn-white">
                                <i class="fas fa-plus me-2"></i>Nuevo Expediente
                            </a>
                        </div>
                    </div>
                </div>

                <div class="card-body pt-3 pb-2">
                    <form method="GET" class="mb-0" id="filtersForm">
                        <div class="row align-items-center">
                            <div class="col-md-4 col-lg-3 mb-2 mb-md-0">
                                <input type="text" name="q" class="form-control form-control-lg border border-info" placeholder="Buscar por nombre, CUI, expediente..." value="{{ request('q') }}">
                            </div>
                            <div class="col-md-6 col-lg-5 mb-2 mb-md-0">
                                <div class="input-group input-group-lg">
                                    <select name="country_id" class="form-select border border-info">
                                        <option value="">Todos los países</option>
                                        @foreach($countries as $country)
                                            <option value="{{ $country->id }}" {{ request('country_id') == $country->id ? 'selected' : '' }}>{{ $country->name }}</option>
                                        @endforeach
                                    </select>
                                    <button type="submit" class="btn bg-gradient-info text-white">
                                        <i class="fas fa-filter me-2"></i>Filtrar
                                    </button>
                                </div>
                            </div>
                            @if(request('q') || request('country_id'))
                            <div class="col-auto ms-2">
                                <a href="{{ route('clinical-records.index') }}" class="btn btn-outline-secondary">
                                    <i class="fas fa-times me-2"></i>Limpiar búsqueda
                                </a>
                            </div>
                            @endif
                        </div>
                    </form>
                </div>

                <div class="card-body px-0 pt-0 pb-2">
                    <div class="table-responsive p-0">
                        <table class="table align-items-center mb-0 dataTable-table" id="datatable-basic" data-datatable="true">
                            <thead>
                                <tr>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-3">Número de Expediente</th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-3">Nombre Completo</th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-3">CUI</th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-3">Edad</th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-3" style="width: 35%; min-width: 300px; max-width: 500px;">Ubicación</th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-3 text-center">Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($clinicalRecords as $record)
                                <tr>
                                    <td>
                                        <div class="d-flex px-3 py-2">
                                            <div class="avatar avatar-sm me-3 bg-info rounded-circle">
                                                <span class="text-white font-weight-bold">{{ substr($record->first_name, 0, 1) }}</span>
                                            </div>
                                            <div class="d-flex flex-column justify-content-center">
                                                <h6 class="mb-0 text-sm">{{ $record->record_number }}</h6>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-3 py-2">
                                        <p class="text-sm font-weight-bold mb-0">{{ $record->full_name }}</p>
                                    </td>
                                    <td class="px-3 py-2">
                                        <p class="text-sm text-secondary mb-0">{{ $record->cui }}</p>
                                    </td>
                                    <td class="px-3 py-2">
                                        <p class="text-sm text-secondary mb-0">{{ $record->age }} años</p>
                                    </td>
                                    <td class="px-3 py-2" style="width: 35%; min-width: 300px; max-width: 500px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                                        <p class="text-sm text-secondary mb-0 d-flex align-items-center" style="white-space: nowrap; overflow: hidden; text-overflow: ellipsis; max-width: 100%;">
                                            {{ $record->municipality->name ?? '-' }}, {{ $record->department->name ?? '-' }}, {{ $record->country->name ?? '-' }}
                                            <span class="ms-2">
                                                <i class="fas fa-map-marker-alt text-info" data-bs-toggle="tooltip" data-bs-placement="top" title="{{ $record->municipality->name ?? '-' }}, {{ $record->department->name ?? '-' }}, {{ $record->country->name ?? '-' }}"></i>
                                            </span>
                                        </p>
                                    </td>
                                    <td class="align-middle text-center">
                                        <a href="{{ route('clinical-records.show', $record) }}" class="btn btn-primary rounded-pill px-3 py-2 me-2">
                                            <i class="fas fa-eye me-1"></i>Ver
                                        </a>
                                        <a href="{{ route('clinical-records.edit', $record) }}" class="btn btn-info rounded-pill px-3 py-2 me-2">
                                            <i class="fas fa-edit me-1"></i>Editar
                                        </a>
                                        <a href="{{ route('clinical-records.print', $record->id) }}" class="btn btn-secondary rounded-pill px-3 py-2" target="_blank">
                                            <i class="fas fa-print me-1"></i>Imprimir
                                        </a>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="6" class="text-center py-4">
                                        <span class="text-muted">No hay expedientes clínicos registrados.</span>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    <div class="d-flex justify-content-center mt-4">
                        {{ $clinicalRecords->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection 