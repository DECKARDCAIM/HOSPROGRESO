@extends('layouts.panel')

@section('title', 'Expedientes Clínicos')
@section('breadcrumb', 'Expedientes Clínicos')

@section('content')
<div class="container-fluid py-4">
    <div class="row">
        <div class="col-12">
            <div class="card mb-4">
                <div class="card-header pb-0 bg-info">
                    <div class="row align-items-center">
                        <div class="col-md-8">
                            <h6 class="text-white mb-0">Expedientes Clínicos</h6>
                            <p class="text-sm text-white opacity-8 mb-0">
                                Este módulo permite gestionar los expedientes clínicos de los pacientes.
                            </p>
                        </div>
                        <div class="col-md-4 text-end">
                            <a href="{{ route('clinical-records.create') }}" class="btn btn-sm btn-white">
                                <i class="fas fa-plus me-2"></i>Nuevo Expediente
                            </a>
                        </div>
                    </div>
                </div>
                <div class="card-body px-0 pt-0 pb-2">
                    <div class="table-responsive p-0">
                        <table class="table align-items-center mb-0">
                            <thead>
                                <tr>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-3">Número de Expediente</th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-3">Nombre Completo</th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-3">CUI</th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-3">Edad</th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-3">Ubicación</th>
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
                                    <td class="px-3 py-2">
                                        <p class="text-sm text-secondary mb-0">
                                            {{ $record->municipality->name ?? '-' }}, {{ $record->department->name ?? '-' }}, {{ $record->country->name ?? '-' }}
                                        </p>
                                    </td>
                                    <td class="align-middle text-center">
                                        <a href="{{ route('clinical-records.show', $record) }}" class="btn btn-primary rounded-pill px-3 py-2 me-2">
                                            <i class="fas fa-eye me-1"></i> Ver
                                        </a>
                                        <a href="{{ route('clinical-records.edit', $record) }}" class="btn btn-info rounded-pill px-3 py-2 me-2">
                                            <i class="fas fa-edit me-1"></i> Editar
                                        </a>
                                        <form action="{{ route('clinical-records.destroy', $record) }}" method="POST" class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger rounded-pill px-3 py-2" onclick="return confirm('¿Está seguro de eliminar este expediente?')">
                                                <i class="fas fa-trash me-1"></i> Eliminar
                                            </button>
                                        </form>
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
                    <div class="d-flex justify-content-center mt-3">
                        {{ $clinicalRecords->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 