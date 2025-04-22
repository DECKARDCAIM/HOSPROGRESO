@extends('layouts.panel')

@section('tittle', 'Inicio')

@section('content')
    <div class="container-fluid py-2">
        <div class="row">
            <div class="container">
                <div class="row">
                    <div class="col-12">
                        <div class="card mt-3">
                            <div class="card-header">Bienvenido {{ Auth::user()->name }} Al Sistema de Gestion de Pacientes</div>

                            <div class="card-body">
                                @if (session('status'))
                                    <div class="alert alert-success" role="alert">
                                        {{ session('status') }}
                                    </div>
                                @endif

                                {{ __('You are logged in!') }}
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-12 position-relative z-index-2 mt-4">
                    <div class="ms-3">
                        <h3 class="mb-0 h4 font-weight-bolder">Dashboard</h3>
                        <p class="mb-4">
                            Este modulo permite ver graficamente los resultados estadisticos.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
