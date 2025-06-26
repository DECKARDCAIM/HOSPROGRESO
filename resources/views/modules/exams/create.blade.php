@extends('layouts.panel')

@section('title', 'Crear Examen')
@section('breadcrumb', 'Exámenes / Crear')

@section('content')
<div class="container-fluid py-4">
    <div class="row">
        <div class="col-12">
            <div class="card mb-4 border">
                <div class="card-header pb-0 bg-gradient-info">
                    <div class="row align-items-center">
                        <div class="col-md-8">
                            <h6 class="text-white mb-0">Nuevo Examen</h6>
                        </div>
                        <div class="col-md-4 text-end">
                            <a href="{{ route('exams.index') }}" class="btn btn-sm btn-white">
                                <i class="fas fa-chevron-left me-2"></i>Regresar
                            </a>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    @if ($errors->any())
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <span class="alert-icon"><i class="fas fa-exclamation-triangle"></i></span>
                        <span class="alert-text">
                            <strong>Por favor!</strong> Revisa los siguientes errores:
                            <ul class="mb-0">
                                @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </span>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                    @endif
                    <form action="{{ route('exams.store') }}" method="POST" class="form-horizontal">
                        @csrf
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group mb-4">
                                    <label for="name" class="form-control-label mb-2">
                                        <i class="fas fa-tag text-info me-2"></i>Nombre
                                    </label>
                                    <input type="text" name="name" id="name" class="form-control form-control-lg border border-2 border-info shadow-sm" placeholder="Nombre del examen" value="{{ old('name')}}" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group mb-4">
                                    <label for="description" class="form-control-label mb-2">
                                        <i class="fas fa-align-left text-info me-2"></i>Descripción
                                    </label>
                                    <input type="text" name="description" id="description" class="form-control form-control-lg border border-2 border-info shadow-sm" placeholder="Descripción (opcional)" value="{{ old('description')}}">
                                </div>
                            </div>
                        </div>
                        <div class="d-flex justify-content-end mt-4">
                            <button type="button" class="btn btn-secondary btn-lg me-2" onclick="window.location.href='{{ route('exams.index') }}'">
                                <i class="fas fa-times me-2"></i>Cancelar
                            </button>
                            <button type="submit" class="btn bg-gradient-info btn-lg text-white">
                                <i class="fas fa-save me-2"></i>Crear examen
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 