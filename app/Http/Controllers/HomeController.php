<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ClinicalRecord;
use App\Models\Appointment;
use App\Models\MedicalConsultation;
use App\Models\Doctor;
use App\Models\Specialty;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;

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
        $stats = Cache::tags(['dashboard','reportes'])->remember('dashboard:home:stats:v1', now()->addMinutes(60), function () {
            return [
                'expedientes_activos' => ClinicalRecord::count(),
                'citas_hoy' => Appointment::whereDate('appointment_date', today())
                                        ->whereIn('status', ['pendiente', 'confirmada'])
                                        ->count(),
                'consultas_mes' => MedicalConsultation::whereMonth('consultation_date', now()->month)
                                                     ->whereYear('consultation_date', now()->year)
                                                     ->count(),
                'doctores_activos' => Doctor::where('is_active', true)->count(),
                'especialidades_activas' => Specialty::where('is_active', true)->count(),
                'total_citas' => Appointment::count(),
                'citas_mes' => Appointment::whereMonth('appointment_date', now()->month)
                                        ->whereYear('appointment_date', now()->year)
                                        ->count()
            ];
        });

        // Consultas por especialidad (último mes) - mejorado
        $consultasPorEspecialidad = Cache::tags(['dashboard','reportes'])->remember('dashboard:home:consultasPorEspecialidad:v1', now()->addMinutes(60), function () {
            if (MedicalConsultation::count() === 0) {
                return collect();
            }
            return MedicalConsultation::join('specialties', 'medical_consultations.specialty_id', '=', 'specialties.id')
                ->whereMonth('consultation_date', now()->month)
                ->whereYear('consultation_date', now()->year)
                ->whereNotNull('medical_consultations.specialty_id')
                ->select('specialties.name', DB::raw('count(*) as total'))
                ->groupBy('specialties.id', 'specialties.name')
                ->orderBy('total', 'desc')
                ->limit(6)
                ->get();
        });

        // Si no hay consultas, mostrar especialidades disponibles
        if ($consultasPorEspecialidad->isEmpty()) {
            $consultasPorEspecialidad = Specialty::where('is_active', true)
                ->select('name', DB::raw('0 as total'))
                ->limit(6)
                ->get();
        }

        // Citas por estado (últimos 30 días)
        $citasPorEstado = Cache::tags(['dashboard','reportes'])->remember('dashboard:home:citasPorEstado:v1', now()->addMinutes(60), function () {
            return Appointment::select('status', DB::raw('count(*) as total'))
                ->whereDate('appointment_date', '>=', now()->subDays(30))
                ->groupBy('status')
                ->get();
        });

        // Si no hay citas, mostrar estados por defecto
        if ($citasPorEstado->isEmpty()) {
            $citasPorEstado = collect([
                (object)['status' => 'pendiente', 'total' => 0],
                (object)['status' => 'confirmada', 'total' => 0],
                (object)['status' => 'atendida', 'total' => 0]
            ]);
        }

        // Consultas por día (últimos 7 días)
        $consultasUltimaSemana = Cache::tags(['dashboard','reportes'])->remember('dashboard:home:consultas7d:v1', now()->addMinutes(60), function () {
            $data = [];
            for ($i = 6; $i >= 0; $i--) {
                $fecha = now()->subDays($i);
                $total = MedicalConsultation::whereDate('consultation_date', $fecha)->count();
                $data[] = [
                    'fecha' => $fecha->format('Y-m-d'),
                    'dia' => $fecha->translatedFormat('D'),
                    'total' => $total
                ];
            }
            return $data;
        });

        // Citas por día (últimos 7 días)
        $citasUltimaSemana = Cache::tags(['dashboard','reportes'])->remember('dashboard:home:citas7d:v1', now()->addMinutes(60), function () {
            $data = [];
            for ($i = 6; $i >= 0; $i--) {
                $fecha = now()->subDays($i);
                $total = Appointment::whereDate('appointment_date', $fecha)->count();
                $data[] = [
                    'fecha' => $fecha->format('Y-m-d'),
                    'dia' => $fecha->translatedFormat('D'),
                    'total' => $total
                ];
            }
            return $data;
        });

        // Distribución por tipo de atención
        $tiposAtencion = Cache::tags(['dashboard','reportes'])->remember('dashboard:home:tiposAtencion:v1', now()->addMinutes(60), function () {
            return MedicalConsultation::select('attention_type', DB::raw('count(*) as total'))
                ->whereMonth('consultation_date', now()->month)
                ->whereYear('consultation_date', now()->year)
                ->groupBy('attention_type')
                ->get();
        });

        // Si no hay datos, mostrar tipos por defecto
        if ($tiposAtencion->isEmpty()) {
            $tiposAtencion = collect([
                (object)['attention_type' => 'emergencia', 'total' => 0],
                (object)['attention_type' => 'consulta_externa', 'total' => 0]
            ]);
        }

        // Médicos por especialidad
        $doctoresPorEspecialidad = Cache::tags(['dashboard','reportes'])->remember('dashboard:home:doctoresPorEspecialidad:v1', now()->addMinutes(60), function () {
            return Doctor::join('specialties', 'doctors.specialty_id', '=', 'specialties.id')
                ->where('doctors.is_active', true)
                ->whereNotNull('doctors.specialty_id')
                ->select('specialties.name', DB::raw('count(*) as total'))
                ->groupBy('specialties.id', 'specialties.name')
                ->orderBy('total', 'desc')
                ->get();
        });

        // Si no hay doctores con especialidad, mostrar especialidades disponibles
        if ($doctoresPorEspecialidad->isEmpty()) {
            $doctoresPorEspecialidad = Specialty::where('is_active', true)
                ->select('name', DB::raw('0 as total'))
                ->get();
        }

        // Actividad reciente (últimas 10 consultas o citas si no hay consultas)
        $actividadReciente = Cache::tags(['dashboard','reportes'])->remember('dashboard:home:actividadReciente:v1', now()->addMinutes(10), function () {
            return MedicalConsultation::with(['clinicalRecord', 'doctor', 'specialty'])
                ->orderBy('consultation_date', 'desc')
                ->limit(10)
                ->get();
        });

        // Si no hay consultas, mostrar citas recientes
        if ($actividadReciente->isEmpty()) {
            $actividadReciente = Appointment::with(['clinicalRecord', 'doctor', 'specialty'])
                ->orderBy('appointment_date', 'desc')
                ->limit(10)
                ->get();
        }

        return view('home', compact(
            'stats',
            'consultasPorEspecialidad',
            'citasPorEstado',
            'consultasUltimaSemana',
            'citasUltimaSemana',
            'tiposAtencion',
            'doctoresPorEspecialidad',
            'actividadReciente'
        ));
    }
}
