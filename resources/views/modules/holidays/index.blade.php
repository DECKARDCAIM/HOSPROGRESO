@extends('layouts.panel')

@section('title', 'Días Festivos')
@section('breadcrumb', 'Días Festivos')

@section('content')

    <div class="container-fluid py-4">
        <div class="row">
            <div class="col-12">
                <div class="card mb-4">
                    <div class="card-header pb-0 bg-brand-header">
                        <div class="row align-items-center">
                            <div class="col-md-8">
                                <h6 class="text-white mb-0">Días Festivos</h6>
                                <p class="text-sm text-white opacity-8 mb-0">
                                    Este módulo permite gestionar los días no laborables del hospital.
                                </p>
                            </div>
                            <div class="col-md-4 text-end">
                                <a href="{{ route('holidays.create') }}" class="btn btn-sm btn-white">
                                    <i class="bi bi-plus me-2"></i>Nuevo Día Festivo
                                </a>
                            </div>
                        </div>
                    </div>

                    <div class="card-body pt-3 pb-2">
                        <form action="{{ route('holidays.index') }}" method="GET" class="mb-0">
                            <div class="row align-items-center">
                                <div class="col-md-3 col-lg-2 mb-2 mb-md-0">
                                    <select name="status" class="form-select form-select-lg border border-info"
                                        onchange="this.form.submit()">
                                        <option value="active" {{ $status === 'active' ? 'selected' : '' }}>Activos</option>
                                        <option value="inactive" {{ $status === 'inactive' ? 'selected' : '' }}>Inactivos
                                        </option>
                                    </select>
                                </div>
                                <div class="col-md-3 col-lg-2 mb-2 mb-md-0">
                                    <select name="year" class="form-select form-select-lg border border-info"
                                        onchange="this.form.submit()">
                                        @if(count($availableYears) > 0)
                                            @foreach($availableYears as $availableYear)
                                                <option value="{{ $availableYear }}" {{ $year == $availableYear ? 'selected' : '' }}>
                                                    {{ $availableYear }}
                                                </option>
                                            @endforeach
                                        @else
                                            <option value="{{ date('Y') }}" selected>{{ date('Y') }}</option>
                                        @endif
                                    </select>
                                </div>
                                <div class="col-md-4 col-lg-4 mb-2 mb-md-0">
                                    <div class="input-group input-group-lg">
                                        <span class="input-group-text bg-brand-header text-white border-info">
                                            
                                        </span>
                                        <input type="text" name="search" class="form-control border border-info"
                                            placeholder="Buscar por nombre o descripción..." value="{{ $search }}">
                                        <button type="submit" class="btn bg-brand-header text-white">
                                            <i class="bi bi-funnel me-2"></i>Filtrar
                                        </button>
                                    </div>
                                </div>
                                @if ($search)
                                    <div class="col-auto ms-2">
                                        <a href="{{ route('holidays.index', ['status' => $status, 'year' => $year]) }}"
                                            class="btn btn-outline-secondary">
                                            <i class="bi bi-x me-2"></i>Limpiar búsqueda
                                        </a>
                                    </div>
                                @endif
                            </div>
                        </form>
                    </div>

                    <div class="card-body px-0 pt-0 pb-2">
                        <div class="table-responsive p-0">
                            <table class="table align-items-center mb-0 dataTable-table" id="datatable-basic"
                                data-datatable="true">
                                <thead>
                                    <tr>
                                        <th
                                            class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-3">
                                            Nombre</th>
                                        <th
                                            class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-3">
                                            Fecha</th>
                                        <th
                                            class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-3">
                                            Tipo</th>
                                        <th
                                            class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-3">
                                            Estado</th>
                                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-3"
                                            style="width: 35%; min-width: 300px; max-width: 500px;">Descripción</th>
                                        <th
                                            class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-3 text-center">
                                            Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($holidays as $holiday)
                                        <tr>
                                            <td>
                                                <div class="d-flex px-3 py-2">
                                                    <div class="avatar avatar-sm me-3 bg-brand-header rounded-circle">
                                                        <span
                                                            class="text-white font-weight-bold">{{ substr($holiday->name, 0, 1) }}</span>
                                                    </div>
                                                    <div class="d-flex flex-column justify-content-center">
                                                        <h6 class="mb-0 text-sm">{{ $holiday->name }}</h6>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="px-3 py-2">
                                                <span class="badge bg-light text-dark">
                                                    {{ \Carbon\Carbon::parse($holiday->date)->format('d/m/Y') }}
                                                </span>
                                            </td>
                                            <td class="px-3 py-2">
                                                @if($holiday->is_recurring)
                                                    <span class="badge bg-secondary">
                                                        <i class="fas fa-redo me-1"></i>Recurrente
                                                    </span>
                                                @else
                                                    <span class="badge bg-secondary">
                                                        <i class="fas fa-calendar-day me-1"></i>Específico
                                                    </span>
                                                @endif
                                            </td>
                                            <td class="px-3 py-2">
                                                @if ($holiday->is_active)
                                                    <span class="badge bg-success">Activo</span>
                                                @else
                                                    <span class="badge bg-secondary">Inactivo</span>
                                                @endif
                                            </td>
                                            <td class="px-3 py-2"
                                                style="width: 35%; min-width: 300px; max-width: 500px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                                                <p class="text-sm text-secondary mb-0 d-flex align-items-center"
                                                    style="white-space: nowrap; overflow: hidden; text-overflow: ellipsis; max-width: 100%;">
                                                    {{ Str::limit($holiday->description, 60) }}
                                                    @if($holiday->description)
                                                        <span class="ms-2">
                                                            <i class="bi bi-info-circle text-info" data-bs-toggle="tooltip"
                                                                data-bs-placement="top" title="{{ $holiday->description }}"></i>
                                                        </span>
                                                    @endif
                                                </p>
                                            </td>
                                            <td class="align-middle text-center">
                                                @if ($status === 'active')
                                                    <a href="{{ route('holidays.edit', $holiday) }}"
                                                        class="btn bg-brand-header rounded-pill px-3 py-2 me-2 text-white">
                                                        <i class="bi bi-pencil me-1"></i>Editar
                                                    </a>
                                                    <form action="{{ route('holidays.destroy', $holiday) }}"
                                                        method="POST" class="d-inline">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit"
                                                            class="btn btn-danger rounded-pill px-3 py-2 text-white">
                                                            <i class="bi bi-trash me-1"></i>Eliminar
                                                        </button>
                                                    </form>
                                                @else
                                                    <form
                                                        action="{{ route('holidays.reactivate', $holiday->id) }}"
                                                        method="POST" class="d-inline">
                                                        @csrf
                                                        <button type="submit"
                                                            class="btn btn-success rounded-pill px-3 py-2 text-white">
                                                            <i class="bi bi-power me-1"></i>Reactivar
                                                        </button>
                                                    </form>
                                                @endif
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="6" class="text-center py-4">
                                                <span class="text-muted">No hay días festivos registrados.</span>
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                        <div class="d-flex justify-content-center mt-4">
                            {{ $holidays->links() }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
