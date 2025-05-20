@extends('layouts.panel')

@section('tittle', 'Paises')
@section('breadcrumb', 'Paises')

@section('content')
    <div class="container-fluid py-4">

        @if (session('notification'))
            <div class="alert alert-{{ session('notification')['alert-type'] == 'Eliminación Éxitosa' ? 'danger' : (session('notification')['alert-type'] == 'Actualización Éxitosa' ? 'info' : 'success') }} text-white"
                role="alert" id="notification-alert">
                <strong>{{ ucfirst(session('notification')['alert-type']) }}!</strong>
                {{ session('notification')['message'] }}
            </div>
        @endif

        <div class="row">
            <div class="col-12">
                <div class="card mb-4">
                    <div class="card-header pb-0 bg-info">
                        <div class="row align-items-center">
                            <div class="col-md-8">
                                <h6 class="text-white mb-0">Paises</h6>
                                <p class="text-sm text-white opacity-8 mb-0">
                                    Este módulo permite gestionar los paises.
                                </p>
                            </div>
                            <div class="col-md-4 text-end">
                                <a href="{{ url('/paises/create') }}" class="btn btn-sm btn-white">
                                    <i class="fas fa-plus me-2"></i>Agregar Pais
                                </a>
                            </div>
                        </div>
                    </div>
                    <div class="card-body px-0 pt-0 pb-2">
                        <div class="table-responsive p-0">
                            <table class="table align-items-center mb-0" id="datatable-basic">
                                <thead>
                                    <tr>
                                        <th
                                            class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-3">
                                            Nombre</th>
                                        <th
                                            class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-3">
                                            Descripción</th>
                                        <th
                                            class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-3 text-center">
                                            Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($countries as $pais)
                                        <tr>
                                            <td>
                                                <div class="d-flex px-3 py-2">
                                                    <div class="avatar avatar-sm me-3 bg-info rounded-circle">
                                                        <span
                                                            class="text-white font-weight-bold">{{ substr($pais->name, 0, 1) }}</span>
                                                    </div>
                                                    <div class="d-flex flex-column justify-content-center">
                                                        <h6 class="mb-0 text-sm">{{ $pais->name }}</h6>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="px-3 py-2">
                                                <p class="text-sm text-secondary mb-0">
                                                    {{ Str::limit($pais->description, 100) }}
                                                    @if (strlen($pais->description) > 100)
                                                        <i class="fas fa-info-circle ms-1 text-info"
                                                            data-bs-toggle="tooltip" data-bs-placement="top"
                                                            title="{{ $pais->description }}"></i>
                                                    @endif
                                                </p>
                                            </td>
                                            <td class="align-middle text-center">
                                                <a href="{{ url('/paises/' . $pais->id . '/edit') }}"
                                                    class="btn btn-info rounded-pill px-3 py-2 me-2">
                                                    <i class="fas fa-edit me-1"></i> Editar
                                                </a>
                                                <form action="{{ url('/paises/' . $pais->id) }}"
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
                                            <td colspan="3" class="text-center py-4">
                                                <span class="text-muted">No hay paises registrados.</span>
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
