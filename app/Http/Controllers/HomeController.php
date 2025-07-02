<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        // Estadísticas básicas para evitar errores
        $stats = [
            'expedientes_activos' => 0,
            'citas_hoy' => 0,
            'consultas_mes' => 0,
            'doctores_activos' => 0
        ];

        // Arrays vacíos para las gráficas
        $citasPorEspecialidad = [];
        $citasPorEstado = [];
        $citasUltimaSemana = [];
        $consultasUltimaSemana = [];
        $tiposControlSigsa = [];
        $usuariosPorRol = [];
        $doctoresPorEspecialidad = [];
        $actividadReciente = collect();

        return view('home', compact(
            'stats',
            'citasPorEspecialidad',
            'citasPorEstado', 
            'citasUltimaSemana',
            'consultasUltimaSemana',
            'tiposControlSigsa',
            'usuariosPorRol',
            'doctoresPorEspecialidad',
            'actividadReciente'
        ));
    }
}
