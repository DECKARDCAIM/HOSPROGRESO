@extends('layouts.panel')

@section('title', 'Departamentos')
@section('breadcrumb', 'Departamentos')

@section('content')
    <div class="container-fluid py-4">
        <div class="row">
            <div class="col-12">
                <div class="card mb-4">
                    <div class="card-header pb-0 bg-info">
                        <div class="row align-items-center">
                            <div class="col-md-8">
                                <h6 class="text-white mb-0">Departamentos</h6>
                                <p class="text-sm text-white opacity-8 mb-0">
                                    Este módulo permite gestionar los Departamentos.
                                </p>
                            </div>
                            <div class="col-md-4 text-end">
                                <a href="{{ url('/departamentos/create') }}" class="btn btn-sm btn-white">
                                    <i class="fas fa-plus me-2"></i>Agregar Departamento
                                </a>
                            </div>
                        </div>
                    </div>

                    <!-- Filtro por país mejorado -->
                    <div class="card-body pt-3 pb-2">
                        <form action="{{ url('/departamentos') }}" method="GET" class="mb-0">
                            @csrf
                            <div class="row align-items-center">
                                <div class="col-md-6 col-lg-4">
                                    <div class="input-group input-group-lg">
                                        <span class="input-group-text bg-info text-white border-info">
                                            <i class="fas fa-globe-americas"></i>
                                        </span>
                                        <select name="country_id" id="country_id" 
                                            class="form-select form-select-lg border border-info" 
                                            aria-label="Filtrar por país">
                                            <option value="">Todos los países</option>
                                            @foreach($countries as $country)
                                                <option value="{{ $country->id }}" 
                                                    {{ request('country_id') == $country->id ? 'selected' : '' }}>
                                                    {{ $country->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                        <button type="submit" class="btn bg-gradient-info text-white">
                                            <i class="fas fa-filter me-2"></i>Filtrar
                                        </button>
                                    </div>
                                </div>
                                @if(request('country_id'))
                                <div class="col-auto ms-2">
                                    <a href="{{ url('/departamentos') }}" class="btn btn-outline-secondary">
                                        <i class="fas fa-times me-2"></i>Limpiar filtro
                                    </a>
                                </div>
                                @endif
                            </div>
                        </form>
                    </div>

                    <div class="card-body px-0 pt-0 pb-2">
                        <div class="table-responsive p-0">
                            <table class="table align-items-center mb-0" id="datatable-basic">
                                <thead>
                                    <tr>
                                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-3">Nombre</th>
                                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-3">País</th>
                                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-3">Descripción</th>
                                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-3 text-center">Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($departments as $department)
                                        <tr>
                                            <td>
                                                <div class="d-flex px-3 py-2">
                                                    <div class="avatar avatar-sm me-3 bg-info rounded-circle">
                                                        <span class="text-white font-weight-bold">{{ substr($department->name, 0, 1) }}</span>
                                                    </div>
                                                    <div class="d-flex flex-column justify-content-center">
                                                        <h6 class="mb-0 text-sm">{{ $department->name }}</h6>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="px-3 py-2">
                                                <div class="d-flex align-items-center">
                                                    <span class="text-sm">{{ $department->country->name }}</span>
                                                </div>
                                            </td>
                                            <td class="px-3 py-2">
                                                <p class="text-sm text-secondary mb-0">
                                                    {{ Str::limit($department->description, 100) }}
                                                    @if (strlen($department->description) > 100)
                                                        <i class="fas fa-info-circle ms-1 text-info"
                                                            data-bs-toggle="tooltip" data-bs-placement="top"
                                                            title="{{ $department->description }}"></i>
                                                    @endif
                                                </p>
                                            </td>
                                            <td class="align-middle text-center">
                                                <a href="{{ url('/departamentos/' . $department->id . '/edit') }}"
                                                    class="btn btn-info rounded-pill px-3 py-2 me-2">
                                                    <i class="fas fa-edit me-1"></i> Editar
                                                </a>
                                                <form action="{{ url('/departamentos/' . $department->id) }}"
                                                    method="POST" class="d-inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-danger rounded-pill px-3 py-2">
                                                        <i class="fas fa-trash me-1"></i> Eliminar
                                                    </button>
                                                </form>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="4" class="text-center py-4">
                                                <div class="d-flex flex-column align-items-center py-4">
                                                    <i class="fas fa-folder-open text-info fa-3x mb-3"></i>
                                                    <span class="text-muted">No hay departamentos registrados.</span>
                                                    @if(request('country_id'))
                                                        <a href="{{ url('/departamentos') }}" class="btn btn-sm btn-outline-info mt-3">
                                                            <i class="fas fa-times me-2"></i>Limpiar filtro
                                                        </a>
                                                    @endif
                                                </div>
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

@endsection