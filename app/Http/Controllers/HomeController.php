<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ClinicalRecord;
use App\Models\Appointment;
use App\Models\MedicalConsultation;
use App\Models\Doctor;
use App\Models\Specialty;
use App\Models\User;
use App\Models\Role;
use App\Models\TemporaryPatient;
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
    public function index(Request $request)
    {
        $user = auth()->user();
        $userRole = $user->getRoleName();
        
        // Generar estadísticas específicas por rol
        $dashboardData = $this->getDashboardDataByRole($user, $userRole, $request);
        
        // Si es una petición AJAX, devolver JSON
        if ($request->ajax() || $request->get('ajax')) {
            return response()->json($dashboardData);
        }
        
        return view('home', $dashboardData);
    }

    /**
     * Obtener datos del dashboard según el rol del usuario
     */
    private function getDashboardDataByRole($user, $roleName, $request = null)
    {
        // Sin cache para datos en tiempo real
        switch ($roleName) {
            case 'Administrador':
                return $this->getAdminDashboard($request);
            case 'Consulta Externa':
                return $this->getConsultationDashboard($request);
            case 'Emergencia':
                return $this->getEmergencyDashboard($request);
            case 'Estadística':
                return $this->getStatisticsDashboard($request);
            default:
                return $this->getBasicDashboard($user, $request);
        }
    }

    /**
     * Dashboard para Administrador - Vista completa del sistema
     */
    private function getAdminDashboard($request = null)
    {
        // Obtener filtros de fecha
        $year = $request ? $request->get('year', date('Y')) : date('Y');
        $month = $request ? $request->get('month') : date('n'); // Usar mes actual por defecto
        
        // Obtener meses con datos reales para el año seleccionado
        $monthsWithData = $this->getMonthsWithData($year);
        
        // Obtener años con datos reales
        $yearsWithData = $this->getYearsWithData();
        
        // KPIs que SIEMPRE se mantienen (no se filtran por fecha)
        $stats = [
            'expedientes_activos' => ClinicalRecord::count(),
            'expedientes_temporales' => TemporaryPatient::count(),
            'doctores_activos' => Doctor::where('is_active', true)->count(),
            'especialidades_activas' => Specialty::where('is_active', true)->count(),
            'usuarios_activos' => User::where('is_active', true)->count(),
        ];
        
        // KPIs que SÍ se filtran por fecha
        $stats['citas_hoy'] = Appointment::whereDate('appointment_date', today())
                                    ->whereIn('status', ['pendiente', 'confirmada'])
                                    ->count();
        
        $stats['consultas_mes'] = MedicalConsultation::when($month, function($query) use ($month, $year) {
            return $query->whereMonth('consultation_date', $month)->whereYear('consultation_date', $year);
        }, function($query) use ($year) {
            return $query->whereYear('consultation_date', $year);
        })->count();
        
        // Pacientes por género - Verificar si hay consultas médicas en el mes actual
        $hasConsultationsThisMonth = MedicalConsultation::whereMonth('consultation_date', $month)
            ->whereYear('consultation_date', $year)
            ->exists();
            
        if ($month && $hasConsultationsThisMonth) {
            // Si hay filtro de mes Y hay consultas en ese mes, contar solo pacientes con consultas en ese mes
            $stats['pacientes_hombres'] = MedicalConsultation::join('clinical_records', 'medical_consultations.clinical_record_id', '=', 'clinical_records.id')
                ->join('sexes', 'clinical_records.sex_id', '=', 'sexes.id')
                ->where(function($query) {
                    $query->where('sexes.name', 'LIKE', '%masculino%')
                          ->orWhere('sexes.name', 'LIKE', '%hombre%');
                })
                ->whereMonth('medical_consultations.consultation_date', $month)
                ->whereYear('medical_consultations.consultation_date', $year)
                ->distinct('clinical_records.id')
                ->count('clinical_records.id');
                
            $stats['pacientes_mujeres'] = MedicalConsultation::join('clinical_records', 'medical_consultations.clinical_record_id', '=', 'clinical_records.id')
                ->join('sexes', 'clinical_records.sex_id', '=', 'sexes.id')
                ->where(function($query) {
                    $query->where('sexes.name', 'LIKE', '%femenino%')
                          ->orWhere('sexes.name', 'LIKE', '%mujer%');
                })
                ->whereMonth('medical_consultations.consultation_date', $month)
                ->whereYear('medical_consultations.consultation_date', $year)
                ->distinct('clinical_records.id')
                ->count('clinical_records.id');
        } else {
            // Si no hay filtro de mes O no hay consultas en el mes actual, contar todos los expedientes del año
            $stats['pacientes_hombres'] = ClinicalRecord::join('sexes', 'clinical_records.sex_id', '=', 'sexes.id')
                ->where(function($query) {
                    $query->where('sexes.name', 'LIKE', '%masculino%')
                          ->orWhere('sexes.name', 'LIKE', '%hombre%');
                })
                ->whereYear('clinical_records.created_at', $year)
                ->count();
                
            $stats['pacientes_mujeres'] = ClinicalRecord::join('sexes', 'clinical_records.sex_id', '=', 'sexes.id')
                ->where(function($query) {
                    $query->where('sexes.name', 'LIKE', '%femenino%')
                          ->orWhere('sexes.name', 'LIKE', '%mujer%');
                })
                ->whereYear('clinical_records.created_at', $year)
                ->count();
        }
        
        $stats['nuevos_expedientes_mes'] = ClinicalRecord::when($month, function($query) use ($month, $year) {
            return $query->whereMonth('created_at', $month)->whereYear('created_at', $year);
        }, function($query) use ($year) {
            return $query->whereYear('created_at', $year);
        })->count();

        // Consultas por especialidad (con filtros)
        $consultasPorEspecialidad = MedicalConsultation::join('specialties', 'medical_consultations.specialty_id', '=', 'specialties.id')
            ->when($month, function($query) use ($month, $year) {
                return $query->whereMonth('consultation_date', $month)->whereYear('consultation_date', $year);
            }, function($query) use ($year) {
                return $query->whereYear('consultation_date', $year);
            })
            ->whereNotNull('medical_consultations.specialty_id')
            ->select('specialties.name', DB::raw('count(*) as total'))
            ->groupBy('specialties.id', 'specialties.name')
            ->orderBy('total', 'desc')
            ->limit(6)
            ->get();

        // Expedientes por mes del año
        $expedientesPorMes = [];
        for ($m = 1; $m <= 12; $m++) {
            $expedientesPorMes[] = [
                'mes' => $m,
                'total' => ClinicalRecord::whereMonth('created_at', $m)
                                       ->whereYear('created_at', $year)
                                       ->count()
            ];
        }


        // Actividad reciente
        $actividadReciente = MedicalConsultation::with(['clinicalRecord', 'doctor', 'specialty'])
            ->orderBy('consultation_date', 'desc')
            ->limit(10)
            ->get();

        return [
            'role' => 'Administrador',
            'stats' => $stats,
            'consultasPorEspecialidad' => $consultasPorEspecialidad,
            'expedientesPorMes' => $expedientesPorMes,
            'actividadReciente' => $actividadReciente,
            'monthsWithData' => $monthsWithData,
            'yearsWithData' => $yearsWithData
        ];
    }


    /**
     * Dashboard para Consulta Externa
     */
    private function getConsultationDashboard()
    {
        $stats = [
            'citas_hoy' => Appointment::whereDate('appointment_date', today())
                                    ->whereIn('status', ['pendiente', 'confirmada'])
                                    ->count(),
            'consultas_mes' => MedicalConsultation::where('attention_type', 'consulta_externa')
                                                 ->whereMonth('consultation_date', now()->month)
                                                 ->whereYear('consultation_date', now()->year)
                                                 ->count(),
            'pacientes_nuevos_mes' => MedicalConsultation::where('attention_type', 'consulta_externa')
                                                        ->where('is_new_patient', true)
                                                        ->whereMonth('consultation_date', now()->month)
                                                        ->whereYear('consultation_date', now()->year)
                                                        ->count(),
            'citas_mes' => Appointment::whereMonth('appointment_date', now()->month)
                                    ->whereYear('appointment_date', now()->year)
                                    ->count()
        ];

        // Consultas por especialidad (consulta externa)
        $consultasPorEspecialidad = MedicalConsultation::join('specialties', 'medical_consultations.specialty_id', '=', 'specialties.id')
            ->where('attention_type', 'consulta_externa')
            ->whereMonth('consultation_date', now()->month)
            ->whereYear('consultation_date', now()->year)
            ->select('specialties.name', DB::raw('count(*) as total'))
            ->groupBy('specialties.id', 'specialties.name')
            ->orderBy('total', 'desc')
            ->limit(6)
            ->get();

        // Citas por estado
        $citasPorEstado = Appointment::select('status', DB::raw('count(*) as total'))
            ->whereMonth('appointment_date', now()->month)
            ->whereYear('appointment_date', now()->year)
            ->groupBy('status')
            ->get();

        return [
            'role' => 'Consulta Externa',
            'stats' => $stats,
            'consultasPorEspecialidad' => $consultasPorEspecialidad,
            'citasPorEstado' => $citasPorEstado
        ];
    }

    /**
     * Dashboard para Emergencia
     */
    private function getEmergencyDashboard()
    {
        $stats = [
            'consultas_emergencia_hoy' => MedicalConsultation::where('attention_type', 'emergencia')
                                                            ->whereDate('consultation_date', today())
                                                            ->count(),
            'consultas_emergencia_mes' => MedicalConsultation::where('attention_type', 'emergencia')
                                                             ->whereMonth('consultation_date', now()->month)
                                                             ->whereYear('consultation_date', now()->year)
                                                             ->count(),
            'pacientes_hospitalizados' => MedicalConsultation::where('attention_type', 'emergencia')
                                                             ->where('final_status', 'hospitalizado')
                                                             ->whereMonth('consultation_date', now()->month)
                                                             ->whereYear('consultation_date', now()->year)
                                                             ->count(),
            'pacientes_referidos' => MedicalConsultation::where('attention_type', 'emergencia')
                                                        ->where('final_status', 'referido')
                                                        ->whereMonth('consultation_date', now()->month)
                                                        ->whereYear('consultation_date', now()->year)
                                                        ->count()
        ];

        // Consultas de emergencia por día (última semana)
        $emergenciasSemana = [];
        for ($i = 6; $i >= 0; $i--) {
            $fecha = now()->subDays($i);
            $emergenciasSemana[] = [
                'fecha' => $fecha->format('Y-m-d'),
                'dia' => $fecha->translatedFormat('D'),
                'total' => MedicalConsultation::where('attention_type', 'emergencia')
                                            ->whereDate('consultation_date', $fecha)
                                            ->count()
            ];
        }

        // Estados finales de pacientes
        $estadosFinales = MedicalConsultation::where('attention_type', 'emergencia')
            ->whereMonth('consultation_date', now()->month)
            ->whereYear('consultation_date', now()->year)
            ->select('final_status', DB::raw('count(*) as total'))
            ->groupBy('final_status')
            ->get();

        return [
            'role' => 'Emergencia',
            'stats' => $stats,
            'emergenciasSemana' => $emergenciasSemana,
            'estadosFinales' => $estadosFinales
        ];
    }

    /**
     * Dashboard para Estadística
     */
    private function getStatisticsDashboard($request = null)
    {
        // Obtener filtros de fecha
        $year = $request ? $request->get('year', date('Y')) : date('Y');
        $month = $request ? $request->get('month') : null;
        
        // KPIs que SIEMPRE se mantienen (no se filtran por fecha)
        $stats = [
            'total_expedientes' => ClinicalRecord::count(),
            'expedientes_temporales' => TemporaryPatient::count(),
        ];
        
        // KPIs que SÍ se filtran por fecha
        $stats['consultas_mes'] = MedicalConsultation::when($month, function($query) use ($month, $year) {
            return $query->whereMonth('consultation_date', $month)->whereYear('consultation_date', $year);
        }, function($query) use ($year) {
            return $query->whereYear('consultation_date', $year);
        })->count();
        
        // Pacientes por género - Verificar si hay consultas médicas en el mes actual
        $hasConsultationsThisMonth = $month ? MedicalConsultation::whereMonth('consultation_date', $month)
            ->whereYear('consultation_date', $year)
            ->exists() : false;
            
        if ($month && $hasConsultationsThisMonth) {
            // Si hay filtro de mes Y hay consultas en ese mes, contar solo pacientes con consultas en ese mes
            $stats['pacientes_hombres'] = MedicalConsultation::join('clinical_records', 'medical_consultations.clinical_record_id', '=', 'clinical_records.id')
                ->join('sexes', 'clinical_records.sex_id', '=', 'sexes.id')
                ->where(function($query) {
                    $query->where('sexes.name', 'LIKE', '%masculino%')
                          ->orWhere('sexes.name', 'LIKE', '%hombre%');
                })
                ->whereMonth('medical_consultations.consultation_date', $month)
                ->whereYear('medical_consultations.consultation_date', $year)
                ->distinct('clinical_records.id')
                ->count('clinical_records.id');
                
            $stats['pacientes_mujeres'] = MedicalConsultation::join('clinical_records', 'medical_consultations.clinical_record_id', '=', 'clinical_records.id')
                ->join('sexes', 'clinical_records.sex_id', '=', 'sexes.id')
                ->where(function($query) {
                    $query->where('sexes.name', 'LIKE', '%femenino%')
                          ->orWhere('sexes.name', 'LIKE', '%mujer%');
                })
                ->whereMonth('medical_consultations.consultation_date', $month)
                ->whereYear('medical_consultations.consultation_date', $year)
                ->distinct('clinical_records.id')
                ->count('clinical_records.id');
        } else {
            // Si no hay filtro de mes O no hay consultas en el mes actual, contar todos los expedientes del año
            $stats['pacientes_hombres'] = ClinicalRecord::join('sexes', 'clinical_records.sex_id', '=', 'sexes.id')
                ->where(function($query) {
                    $query->where('sexes.name', 'LIKE', '%masculino%')
                          ->orWhere('sexes.name', 'LIKE', '%hombre%');
                })
                ->whereYear('clinical_records.created_at', $year)
                ->count();
                
            $stats['pacientes_mujeres'] = ClinicalRecord::join('sexes', 'clinical_records.sex_id', '=', 'sexes.id')
                ->where(function($query) {
                    $query->where('sexes.name', 'LIKE', '%femenino%')
                          ->orWhere('sexes.name', 'LIKE', '%mujer%');
                })
                ->whereYear('clinical_records.created_at', $year)
                ->count();
        }
        
        $stats['pacientes_menores'] = MedicalConsultation::join('clinical_records', 'medical_consultations.clinical_record_id', '=', 'clinical_records.id')
                                                     ->whereRaw('TIMESTAMPDIFF(YEAR, clinical_records.birth_date, CURDATE()) < 18')
                                                     ->when($month, function($query) use ($month, $year) {
                                                         return $query->whereMonth('consultation_date', $month)->whereYear('consultation_date', $year);
                                                     }, function($query) use ($year) {
                                                         return $query->whereYear('consultation_date', $year);
                                                     })
                                                     ->distinct('clinical_records.id')
                                                     ->count('clinical_records.id');
                                                     
        $stats['pacientes_adultos'] = MedicalConsultation::join('clinical_records', 'medical_consultations.clinical_record_id', '=', 'clinical_records.id')
                                                     ->whereRaw('TIMESTAMPDIFF(YEAR, clinical_records.birth_date, CURDATE()) >= 18')
                                                     ->when($month, function($query) use ($month, $year) {
                                                         return $query->whereMonth('consultation_date', $month)->whereYear('consultation_date', $year);
                                                     }, function($query) use ($year) {
                                                         return $query->whereYear('consultation_date', $year);
                                                     })
                                                     ->distinct('clinical_records.id')
                                                     ->count('clinical_records.id');

        // Distribución por sexo (con filtros)
        $distribucionSexo = MedicalConsultation::join('clinical_records', 'medical_consultations.clinical_record_id', '=', 'clinical_records.id')
            ->join('sexes', 'clinical_records.sex_id', '=', 'sexes.id')
            ->when($month, function($query) use ($month, $year) {
                return $query->whereMonth('consultation_date', $month)->whereYear('consultation_date', $year);
            }, function($query) use ($year) {
                return $query->whereYear('consultation_date', $year);
            })
            ->select('sexes.name', DB::raw('count(*) as total'))
            ->groupBy('sexes.id', 'sexes.name')
            ->get();
            
        // Expedientes por mes del año
        $expedientesPorMes = [];
        for ($m = 1; $m <= 12; $m++) {
            $expedientesPorMes[] = [
                'mes' => $m,
                'total' => ClinicalRecord::whereMonth('created_at', $m)
                                       ->whereYear('created_at', $year)
                                       ->count()
            ];
        }

        // Distribución por grupos de edad
        $gruposEdad = MedicalConsultation::join('clinical_records', 'medical_consultations.clinical_record_id', '=', 'clinical_records.id')
            ->whereMonth('consultation_date', now()->month)
            ->whereYear('consultation_date', now()->year)
            ->selectRaw('
                CASE 
                    WHEN TIMESTAMPDIFF(YEAR, clinical_records.birth_date, CURDATE()) < 1 THEN "0-1 años"
                    WHEN TIMESTAMPDIFF(YEAR, clinical_records.birth_date, CURDATE()) BETWEEN 1 AND 4 THEN "1-4 años"
                    WHEN TIMESTAMPDIFF(YEAR, clinical_records.birth_date, CURDATE()) BETWEEN 5 AND 14 THEN "5-14 años"
                    WHEN TIMESTAMPDIFF(YEAR, clinical_records.birth_date, CURDATE()) BETWEEN 15 AND 24 THEN "15-24 años"
                    WHEN TIMESTAMPDIFF(YEAR, clinical_records.birth_date, CURDATE()) BETWEEN 25 AND 44 THEN "25-44 años"
                    WHEN TIMESTAMPDIFF(YEAR, clinical_records.birth_date, CURDATE()) BETWEEN 45 AND 64 THEN "45-64 años"
                    ELSE "65+ años"
                END as grupo_edad,
                count(*) as total
            ')
            ->groupBy('grupo_edad')
            ->orderBy('total', 'desc')
            ->get();

        // Consultas por tipo de control
        $tiposControl = MedicalConsultation::join('control_types', 'medical_consultations.control_type_id', '=', 'control_types.id')
            ->whereMonth('consultation_date', now()->month)
            ->whereYear('consultation_date', now()->year)
            ->select('control_types.name', DB::raw('count(*) as total'))
            ->groupBy('control_types.id', 'control_types.name')
            ->orderBy('total', 'desc')
            ->get();

        return [
            'role' => 'Estadística',
            'stats' => $stats,
            'distribucionSexo' => $distribucionSexo,
            'expedientesPorMes' => $expedientesPorMes,
            'gruposEdad' => $gruposEdad,
            'tiposControl' => $tiposControl
        ];
    }

    /**
     * Dashboard básico para otros roles
     */
    private function getBasicDashboard($user)
    {
        $stats = [
            'expedientes_activos' => ClinicalRecord::count(),
            'citas_hoy' => Appointment::whereDate('appointment_date', today())
                                    ->whereIn('status', ['pendiente', 'confirmada'])
                                    ->count(),
            'consultas_mes' => MedicalConsultation::whereMonth('consultation_date', now()->month)
                                                 ->whereYear('consultation_date', now()->year)
                                                 ->count()
        ];

        return [
            'role' => $user->getRoleName(),
            'stats' => $stats
        ];
    }

    /**
     * Obtener los meses que tienen datos reales para un año específico
     */
    private function getMonthsWithData($year)
    {
        // Obtener meses con consultas médicas
        $monthsWithConsultations = MedicalConsultation::whereYear('consultation_date', $year)
            ->selectRaw('MONTH(consultation_date) as month')
            ->distinct()
            ->pluck('month')
            ->toArray();

        // Obtener meses con expedientes creados
        $monthsWithRecords = ClinicalRecord::whereYear('created_at', $year)
            ->selectRaw('MONTH(created_at) as month')
            ->distinct()
            ->pluck('month')
            ->toArray();

        // Obtener meses con citas
        $monthsWithAppointments = Appointment::whereYear('appointment_date', $year)
            ->selectRaw('MONTH(appointment_date) as month')
            ->distinct()
            ->pluck('month')
            ->toArray();

        // Combinar todos los meses únicos
        $allMonths = array_unique(array_merge(
            $monthsWithConsultations,
            $monthsWithRecords,
            $monthsWithAppointments
        ));

        // Ordenar los meses
        sort($allMonths);

        return $allMonths;
    }

    /**
     * Obtener los años que tienen datos reales
     */
    private function getYearsWithData()
    {
        // Obtener años con consultas médicas
        $yearsWithConsultations = MedicalConsultation::selectRaw('YEAR(consultation_date) as year')
            ->distinct()
            ->pluck('year')
            ->toArray();

        // Obtener años con expedientes creados
        $yearsWithRecords = ClinicalRecord::selectRaw('YEAR(created_at) as year')
            ->distinct()
            ->pluck('year')
            ->toArray();

        // Obtener años con citas
        $yearsWithAppointments = Appointment::selectRaw('YEAR(appointment_date) as year')
            ->distinct()
            ->pluck('year')
            ->toArray();

        // Combinar todos los años únicos
        $allYears = array_unique(array_merge(
            $yearsWithConsultations,
            $yearsWithRecords,
            $yearsWithAppointments
        ));

        // Ordenar los años de mayor a menor
        rsort($allYears);

        return $allYears;
    }
}
