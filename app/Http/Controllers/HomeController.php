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
        
        // Si es una petición AJAX, determinar qué dashboard cargar
        if ($request->ajax() || $request->get('ajax')) {
            $dashboardType = $request->get('dashboard');
            
            // Si se especifica un dashboard específico, usar ese rol
            if ($dashboardType) {
                switch ($dashboardType) {
                    case 'admin':
                        $dashboardData = $this->getAdminDashboard($request);
                        break;
                    case 'consultation':
                        $dashboardData = $this->getConsultationDashboard($request);
                        break;
                    case 'emergency':
                        $dashboardData = $this->getEmergencyDashboard($request);
                        break;
                    case 'statistics':
                        $dashboardData = $this->getStatisticsDashboard($request);
                        break;
                    case 'basic':
                        $dashboardData = $this->getBasicDashboard($user, $request);
                        break;
                    default:
        $dashboardData = $this->getDashboardDataByRole($user, $userRole, $request);
                }
            } else {
                // Si no se especifica, usar el rol del usuario
                $dashboardData = $this->getDashboardDataByRole($user, $userRole, $request);
            }
        
            return response()->json($dashboardData);
        }
        
        // Generar estadísticas específicas por rol para la vista inicial
        $dashboardData = $this->getDashboardDataByRole($user, $userRole, $request);
        
        return view('home', $dashboardData);
    }

    /**
     * Obtener datos del dashboard según el rol del usuario
     */
    private function getDashboardDataByRole($user, $roleName, $request = null)
    {
        // Generar clave de cache única basada en rol, filtros y usuario
        $cacheKey = $this->generateCacheKey($user, $roleName, $request);
        
        // Verificar si se solicita refresh manual
        $forceRefresh = $request ? $request->get('refresh', false) : false;
        
        // Si no es refresh forzado, intentar obtener de cache
        if (!$forceRefresh) {
            $cachedData = Cache::get($cacheKey);
            if ($cachedData) {
                return $cachedData;
            }
        }
        
        // Generar datos frescos
        $dashboardData = $this->generateDashboardData($user, $roleName, $request);
        
        // Cachear por 1 hora (3600 segundos)
        Cache::put($cacheKey, $dashboardData, 3600);
        
        return $dashboardData;
    }
    
    /**
     * Generar datos del dashboard sin cache
     */
    private function generateDashboardData($user, $roleName, $request = null)
    {
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
     * Generar clave de cache única
     */
    private function generateCacheKey($user, $roleName, $request = null)
    {
        $year = $request ? $request->get('year', date('Y')) : date('Y');
        $month = $request ? $request->get('month', '') : '';
        $dateFrom = $request ? $request->get('date_from', '') : '';
        $dateTo = $request ? $request->get('date_to', '') : '';
        
        return "dashboard_{$roleName}_{$user->id}_{$year}_{$month}_{$dateFrom}_{$dateTo}";
    }

    /**
     * Dashboard para Administrador - Vista completa del sistema
     */
    private function getAdminDashboard($request = null)
    {
        // Obtener filtros de fecha
        $year = $request ? $request->get('year', date('Y')) : date('Y');
        $month = $request ? $request->get('month') : date('n'); // Usar mes actual por defecto
        $dateFrom = $request ? $request->get('date_from') : null;
        $dateTo = $request ? $request->get('date_to') : null;
        
        // Si month está vacío, significa "todos los meses" del año
        $useMonthFilter = !empty($month);
        
        // Si hay filtros de rango de fechas, usar esos en lugar de mes/año
        $useDateRangeFilter = !empty($dateFrom) && !empty($dateTo);
        
        // Obtener meses con datos reales para el año seleccionado
        $monthsWithData = $this->getMonthsWithData($year);
        
        // Obtener años con datos reales
        $yearsWithData = $this->getYearsWithData();
        
        // KPIs que SIEMPRE se mantienen (no se filtran por fecha)
        $stats = [
            'expedientes_activos' => ClinicalRecord::count(),
            'expedientes_temporales' => TemporaryPatient::count(),
        ];
        
        // KPIs que SÍ se filtran por fecha
        $stats['citas_hoy'] = Appointment::when($useDateRangeFilter, function($query) use ($dateFrom, $dateTo) {
            return $query->whereBetween('appointment_date', [$dateFrom, $dateTo]);
        }, function($query) use ($useMonthFilter, $month, $year) {
            if ($useMonthFilter) {
                return $query->whereMonth('appointment_date', $month)->whereYear('appointment_date', $year);
            } else {
                return $query->whereYear('appointment_date', $year);
            }
        })
                                    ->count();
        
        $stats['consultas_mes'] = MedicalConsultation::when($useDateRangeFilter, function($query) use ($dateFrom, $dateTo) {
            return $query->whereBetween('consultation_date', [$dateFrom, $dateTo]);
        }, function($query) use ($useMonthFilter, $month, $year) {
            if ($useMonthFilter) {
            return $query->whereMonth('consultation_date', $month)->whereYear('consultation_date', $year);
            } else {
            return $query->whereYear('consultation_date', $year);
            }
        })->count();
        
        // USUARIOS ACTIVOS - Métrica mensual (usuarios que se registraron en el período)
        $stats['usuarios_activos'] = User::when($useDateRangeFilter, function($query) use ($dateFrom, $dateTo) {
            return $query->whereBetween('created_at', [$dateFrom, $dateTo]);
        }, function($query) use ($useMonthFilter, $month, $year) {
            if ($useMonthFilter) {
                return $query->whereMonth('created_at', $month)->whereYear('created_at', $year);
            } else {
                return $query->whereYear('created_at', $year);
            }
        })->count();
        
        // DOCTORES ACTIVOS - Contar solo doctores activos actualmente
        $stats['doctores_activos'] = Doctor::where('is_active', true)->count();
        
        // Pacientes por género - Siempre contar expedientes clínicos registrados según filtro de fecha
        $stats['pacientes_hombres'] = ClinicalRecord::join('sexes', 'clinical_records.sex_id', '=', 'sexes.id')
            ->where(function($query) {
                $query->where('sexes.name', 'LIKE', '%masculino%')
                      ->orWhere('sexes.name', 'LIKE', '%hombre%');
            })
            ->when($useDateRangeFilter, function($query) use ($dateFrom, $dateTo) {
                return $query->whereBetween('clinical_records.created_at', [$dateFrom, $dateTo]);
            }, function($query) use ($useMonthFilter, $month, $year) {
                if ($useMonthFilter) {
                    return $query->whereMonth('clinical_records.created_at', $month)->whereYear('clinical_records.created_at', $year);
                } else {
                    return $query->whereYear('clinical_records.created_at', $year);
                }
            })
            ->count();
            
        $stats['pacientes_mujeres'] = ClinicalRecord::join('sexes', 'clinical_records.sex_id', '=', 'sexes.id')
            ->where(function($query) {
                $query->where('sexes.name', 'LIKE', '%femenino%')
                      ->orWhere('sexes.name', 'LIKE', '%mujer%');
            })
            ->when($useDateRangeFilter, function($query) use ($dateFrom, $dateTo) {
                return $query->whereBetween('clinical_records.created_at', [$dateFrom, $dateTo]);
            }, function($query) use ($useMonthFilter, $month, $year) {
                if ($useMonthFilter) {
                    return $query->whereMonth('clinical_records.created_at', $month)->whereYear('clinical_records.created_at', $year);
                } else {
                    return $query->whereYear('clinical_records.created_at', $year);
                }
            })
            ->count();
        
        $stats['nuevos_expedientes_mes'] = ClinicalRecord::when($useDateRangeFilter, function($query) use ($dateFrom, $dateTo) {
            return $query->whereBetween('created_at', [$dateFrom, $dateTo]);
        }, function($query) use ($useMonthFilter, $month, $year) {
            if ($useMonthFilter) {
            return $query->whereMonth('created_at', $month)->whereYear('created_at', $year);
            } else {
            return $query->whereYear('created_at', $year);
            }
        })->count();
        
        // Métricas adicionales para administrador
        $stats['pacientes_estables'] = MedicalConsultation::join('patient_statuses', 'medical_consultations.patient_status_id', '=', 'patient_statuses.id')
                                                         ->where('patient_statuses.name', 'Estable')
                                                         ->when($useDateRangeFilter, function($query) use ($dateFrom, $dateTo) {
                                                             return $query->whereBetween('consultation_date', [$dateFrom, $dateTo]);
                                                         }, function($query) use ($useMonthFilter, $month, $year) {
                                                             if ($useMonthFilter) {
                                                                 return $query->whereMonth('consultation_date', $month)->whereYear('consultation_date', $year);
                                                             } else {
                                                                 return $query->whereYear('consultation_date', $year);
                                                             }
                                                         })
                                                         ->count();
        
        $stats['pacientes_delicados'] = MedicalConsultation::join('patient_statuses', 'medical_consultations.patient_status_id', '=', 'patient_statuses.id')
                                                          ->where('patient_statuses.name', 'Delicado')
                                                          ->when($useDateRangeFilter, function($query) use ($dateFrom, $dateTo) {
                                                              return $query->whereBetween('consultation_date', [$dateFrom, $dateTo]);
                                                          }, function($query) use ($useMonthFilter, $month, $year) {
                                                              if ($useMonthFilter) {
                                                                  return $query->whereMonth('consultation_date', $month)->whereYear('consultation_date', $year);
                                                              } else {
                                                                  return $query->whereYear('consultation_date', $year);
                                                              }
                                                          })
                                                          ->count();
        
        $stats['pacientes_fallecidos'] = MedicalConsultation::join('patient_statuses', 'medical_consultations.patient_status_id', '=', 'patient_statuses.id')
                                                           ->where('patient_statuses.name', 'Fallecido')
                                                           ->when($useDateRangeFilter, function($query) use ($dateFrom, $dateTo) {
                                                               return $query->whereBetween('consultation_date', [$dateFrom, $dateTo]);
                                                           }, function($query) use ($useMonthFilter, $month, $year) {
                                                               if ($useMonthFilter) {
                                                                   return $query->whereMonth('consultation_date', $month)->whereYear('consultation_date', $year);
                                                               } else {
                                                                   return $query->whereYear('consultation_date', $year);
                                                               }
                                                           })
                                                           ->count();
        
        $stats['pacientes_hospitalizados'] = MedicalConsultation::where('final_status', 'hospitalizado')
                                                               ->when($useDateRangeFilter, function($query) use ($dateFrom, $dateTo) {
                                                                   return $query->whereBetween('consultation_date', [$dateFrom, $dateTo]);
                                                               }, function($query) use ($useMonthFilter, $month, $year) {
                                                                   if ($useMonthFilter) {
                                                                       return $query->whereMonth('consultation_date', $month)->whereYear('consultation_date', $year);
                                                                   } else {
                                                                       return $query->whereYear('consultation_date', $year);
                                                                   }
                                                               })
                                                               ->count();
        
        $stats['pacientes_referidos'] = MedicalConsultation::where('final_status', 'referido')
                                                          ->when($useDateRangeFilter, function($query) use ($dateFrom, $dateTo) {
                                                              return $query->whereBetween('consultation_date', [$dateFrom, $dateTo]);
                                                          }, function($query) use ($useMonthFilter, $month, $year) {
                                                              if ($useMonthFilter) {
                                                                  return $query->whereMonth('consultation_date', $month)->whereYear('consultation_date', $year);
                                                              } else {
                                                                  return $query->whereYear('consultation_date', $year);
                                                              }
                                                          })
                                                          ->count();
        
        // Niños (0-13 años)
        $stats['ninos'] = MedicalConsultation::join('clinical_records', 'medical_consultations.clinical_record_id', '=', 'clinical_records.id')
                                            ->whereRaw('TIMESTAMPDIFF(YEAR, clinical_records.birth_date, CURDATE()) BETWEEN 0 AND 13')
                                            ->when($useDateRangeFilter, function($query) use ($dateFrom, $dateTo) {
                                                return $query->whereBetween('consultation_date', [$dateFrom, $dateTo]);
                                            }, function($query) use ($useMonthFilter, $month, $year) {
                                                if ($useMonthFilter) {
                                                    return $query->whereMonth('consultation_date', $month)->whereYear('consultation_date', $year);
                                                } else {
                                                    return $query->whereYear('consultation_date', $year);
                                                }
                                            })
                                            ->distinct('clinical_records.id')
                                            ->count('clinical_records.id');
        
        // Adolescentes (14-17 años)
        $stats['adolescentes'] = MedicalConsultation::join('clinical_records', 'medical_consultations.clinical_record_id', '=', 'clinical_records.id')
                                                  ->whereRaw('TIMESTAMPDIFF(YEAR, clinical_records.birth_date, CURDATE()) BETWEEN 14 AND 17')
                                                  ->when($useDateRangeFilter, function($query) use ($dateFrom, $dateTo) {
                                                      return $query->whereBetween('consultation_date', [$dateFrom, $dateTo]);
                                                  }, function($query) use ($useMonthFilter, $month, $year) {
                                                      if ($useMonthFilter) {
                                                          return $query->whereMonth('consultation_date', $month)->whereYear('consultation_date', $year);
                                                      } else {
                                                          return $query->whereYear('consultation_date', $year);
                                                      }
                                                  })
                                                  ->distinct('clinical_records.id')
                                                  ->count('clinical_records.id');
        
        // Adultos (18-59 años)
        $stats['adultos'] = MedicalConsultation::join('clinical_records', 'medical_consultations.clinical_record_id', '=', 'clinical_records.id')
                                             ->whereRaw('TIMESTAMPDIFF(YEAR, clinical_records.birth_date, CURDATE()) BETWEEN 18 AND 59')
                                             ->when($useDateRangeFilter, function($query) use ($dateFrom, $dateTo) {
                                                 return $query->whereBetween('consultation_date', [$dateFrom, $dateTo]);
                                             }, function($query) use ($useMonthFilter, $month, $year) {
                                                 if ($useMonthFilter) {
                                                     return $query->whereMonth('consultation_date', $month)->whereYear('consultation_date', $year);
                                                 } else {
                                                     return $query->whereYear('consultation_date', $year);
                                                 }
                                             })
                                             ->distinct('clinical_records.id')
                                             ->count('clinical_records.id');
        
        // Tercera edad (60+ años)
        $stats['tercera_edad'] = MedicalConsultation::join('clinical_records', 'medical_consultations.clinical_record_id', '=', 'clinical_records.id')
                                                   ->whereRaw('TIMESTAMPDIFF(YEAR, clinical_records.birth_date, CURDATE()) >= 60')
                                                   ->when($useDateRangeFilter, function($query) use ($dateFrom, $dateTo) {
                                                       return $query->whereBetween('consultation_date', [$dateFrom, $dateTo]);
                                                   }, function($query) use ($useMonthFilter, $month, $year) {
                                                       if ($useMonthFilter) {
                                                           return $query->whereMonth('consultation_date', $month)->whereYear('consultation_date', $year);
                                                       } else {
                                                           return $query->whereYear('consultation_date', $year);
                                                       }
                                                   })
                                                   ->distinct('clinical_records.id')
                                                   ->count('clinical_records.id');
        
        // Cantidad de especialidades
        $stats['cantidad_especialidades'] = Specialty::count();

        // Consultas por especialidad (con filtros)
        $consultasPorEspecialidad = MedicalConsultation::join('specialties', 'medical_consultations.specialty_id', '=', 'specialties.id')
            ->when($useDateRangeFilter, function($query) use ($dateFrom, $dateTo) {
                return $query->whereBetween('consultation_date', [$dateFrom, $dateTo]);
            }, function($query) use ($useMonthFilter, $month, $year) {
                if ($useMonthFilter) {
                return $query->whereMonth('consultation_date', $month)->whereYear('consultation_date', $year);
                } else {
                return $query->whereYear('consultation_date', $year);
                }
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
    private function getConsultationDashboard($request = null)
    {
        // Obtener filtros de fecha
        $year = $request ? $request->get('year', date('Y')) : date('Y');
        $month = $request ? $request->get('month') : date('n');
        $dateFrom = $request ? $request->get('date_from') : null;
        $dateTo = $request ? $request->get('date_to') : null;
        
        // Si month está vacío, significa "todos los meses" del año
        $useMonthFilter = !empty($month);
        
        // Si hay filtros de rango de fechas, usar esos en lugar de mes/año
        $useDateRangeFilter = !empty($dateFrom) && !empty($dateTo);
        
        $stats = [
            'citas_hoy' => Appointment::when($useDateRangeFilter, function($query) use ($dateFrom, $dateTo) {
                return $query->whereBetween('appointment_date', [$dateFrom, $dateTo]);
            }, function($query) use ($useMonthFilter, $month, $year) {
                if ($useMonthFilter) {
                    return $query->whereMonth('appointment_date', $month)->whereYear('appointment_date', $year);
                } else {
                    return $query->whereYear('appointment_date', $year);
                }
            })
                                    ->count(),
            'consultas_mes' => MedicalConsultation::where('attention_type', 'consulta_externa')
                                                 ->when($useDateRangeFilter, function($query) use ($dateFrom, $dateTo) {
                                                     return $query->whereBetween('consultation_date', [$dateFrom, $dateTo]);
                                                 }, function($query) use ($useMonthFilter, $month, $year) {
                                                     if ($useMonthFilter) {
                                                         return $query->whereMonth('consultation_date', $month)->whereYear('consultation_date', $year);
                                                     } else {
                                                         return $query->whereYear('consultation_date', $year);
                                                     }
                                                 })
                                                 ->count(),
            'pacientes_nuevos_mes' => ClinicalRecord::when($useDateRangeFilter, function($query) use ($dateFrom, $dateTo) {
                return $query->whereBetween('created_at', [$dateFrom, $dateTo]);
            }, function($query) use ($useMonthFilter, $month, $year) {
                if ($useMonthFilter) {
                    return $query->whereMonth('created_at', $month)->whereYear('created_at', $year);
                } else {
                    return $query->whereYear('created_at', $year);
                }
            })->count(),
            'citas_mes' => Appointment::when($useDateRangeFilter, function($query) use ($dateFrom, $dateTo) {
                return $query->whereBetween('appointment_date', [$dateFrom, $dateTo]);
            }, function($query) use ($useMonthFilter, $month, $year) {
                if ($useMonthFilter) {
                    return $query->whereMonth('appointment_date', $month)->whereYear('appointment_date', $year);
                } else {
                    return $query->whereYear('appointment_date', $year);
                }
            })->count(),
            'pacientes_estables' => MedicalConsultation::where('final_status', 'estable')
                                                     ->when($useDateRangeFilter, function($query) use ($dateFrom, $dateTo) {
                                                         return $query->whereBetween('consultation_date', [$dateFrom, $dateTo]);
                                                     }, function($query) use ($useMonthFilter, $month, $year) {
                                                         if ($useMonthFilter) {
                                                             return $query->whereMonth('consultation_date', $month)->whereYear('consultation_date', $year);
                                                         } else {
                                                             return $query->whereYear('consultation_date', $year);
                                                         }
                                                     })
                                                     ->count(),
            'pacientes_delicados' => MedicalConsultation::where('final_status', 'delicado')
                                                      ->when($useDateRangeFilter, function($query) use ($dateFrom, $dateTo) {
                                                          return $query->whereBetween('consultation_date', [$dateFrom, $dateTo]);
                                                      }, function($query) use ($useMonthFilter, $month, $year) {
                                                          if ($useMonthFilter) {
                                                              return $query->whereMonth('consultation_date', $month)->whereYear('consultation_date', $year);
                                                          } else {
                                                              return $query->whereYear('consultation_date', $year);
                                                          }
                                                      })
                                                      ->count(),
            'pacientes_fallecidos' => MedicalConsultation::where('final_status', 'fallecido')
                                                       ->when($useDateRangeFilter, function($query) use ($dateFrom, $dateTo) {
                                                           return $query->whereBetween('consultation_date', [$dateFrom, $dateTo]);
                                                       }, function($query) use ($useMonthFilter, $month, $year) {
                                                           if ($useMonthFilter) {
                                                               return $query->whereMonth('consultation_date', $month)->whereYear('consultation_date', $year);
                                                           } else {
                                                               return $query->whereYear('consultation_date', $year);
                                                           }
                                                       })
                                                       ->count(),
            'pacientes_hospitalizados' => MedicalConsultation::where('final_status', 'hospitalizado')
                                                            ->when($useDateRangeFilter, function($query) use ($dateFrom, $dateTo) {
                                                                return $query->whereBetween('consultation_date', [$dateFrom, $dateTo]);
                                                            }, function($query) use ($useMonthFilter, $month, $year) {
                                                                if ($useMonthFilter) {
                                                                    return $query->whereMonth('consultation_date', $month)->whereYear('consultation_date', $year);
                                                                } else {
                                                                    return $query->whereYear('consultation_date', $year);
                                                                }
                                                            })
                                                            ->count(),
            'pacientes_referidos' => MedicalConsultation::where('final_status', 'referido')
                                                       ->when($useDateRangeFilter, function($query) use ($dateFrom, $dateTo) {
                                                           return $query->whereBetween('consultation_date', [$dateFrom, $dateTo]);
                                                       }, function($query) use ($useMonthFilter, $month, $year) {
                                                           if ($useMonthFilter) {
                                                               return $query->whereMonth('consultation_date', $month)->whereYear('consultation_date', $year);
                                                           } else {
                                                               return $query->whereYear('consultation_date', $year);
                                                           }
                                                       })
                                                       ->count(),
            'ninos' => MedicalConsultation::join('clinical_records', 'medical_consultations.clinical_record_id', '=', 'clinical_records.id')
                                         ->whereRaw('TIMESTAMPDIFF(YEAR, clinical_records.birth_date, CURDATE()) BETWEEN 0 AND 13')
                                         ->when($useDateRangeFilter, function($query) use ($dateFrom, $dateTo) {
                                             return $query->whereBetween('consultation_date', [$dateFrom, $dateTo]);
                                         }, function($query) use ($useMonthFilter, $month, $year) {
                                             if ($useMonthFilter) {
                                                 return $query->whereMonth('consultation_date', $month)->whereYear('consultation_date', $year);
                                             } else {
                                                 return $query->whereYear('consultation_date', $year);
                                             }
                                         })
                                         ->distinct('clinical_records.id')
                                         ->count('clinical_records.id'),
            'adolescentes' => MedicalConsultation::join('clinical_records', 'medical_consultations.clinical_record_id', '=', 'clinical_records.id')
                                               ->whereRaw('TIMESTAMPDIFF(YEAR, clinical_records.birth_date, CURDATE()) BETWEEN 14 AND 17')
                                               ->when($useDateRangeFilter, function($query) use ($dateFrom, $dateTo) {
                                                   return $query->whereBetween('consultation_date', [$dateFrom, $dateTo]);
                                               }, function($query) use ($useMonthFilter, $month, $year) {
                                                   if ($useMonthFilter) {
                                                       return $query->whereMonth('consultation_date', $month)->whereYear('consultation_date', $year);
                                                   } else {
                                                       return $query->whereYear('consultation_date', $year);
                                                   }
                                               })
                                               ->distinct('clinical_records.id')
                                               ->count('clinical_records.id'),
            'adultos' => MedicalConsultation::join('clinical_records', 'medical_consultations.clinical_record_id', '=', 'clinical_records.id')
                                         ->whereRaw('TIMESTAMPDIFF(YEAR, clinical_records.birth_date, CURDATE()) BETWEEN 18 AND 59')
                                         ->when($useDateRangeFilter, function($query) use ($dateFrom, $dateTo) {
                                             return $query->whereBetween('consultation_date', [$dateFrom, $dateTo]);
                                         }, function($query) use ($useMonthFilter, $month, $year) {
                                             if ($useMonthFilter) {
                                                 return $query->whereMonth('consultation_date', $month)->whereYear('consultation_date', $year);
                                             } else {
                                                 return $query->whereYear('consultation_date', $year);
                                             }
                                         })
                                         ->distinct('clinical_records.id')
                                         ->count('clinical_records.id'),
            'tercera_edad' => MedicalConsultation::join('clinical_records', 'medical_consultations.clinical_record_id', '=', 'clinical_records.id')
                                               ->whereRaw('TIMESTAMPDIFF(YEAR, clinical_records.birth_date, CURDATE()) >= 60')
                                               ->when($useDateRangeFilter, function($query) use ($dateFrom, $dateTo) {
                                                   return $query->whereBetween('consultation_date', [$dateFrom, $dateTo]);
                                               }, function($query) use ($useMonthFilter, $month, $year) {
                                                   if ($useMonthFilter) {
                                                       return $query->whereMonth('consultation_date', $month)->whereYear('consultation_date', $year);
                                                   } else {
                                                       return $query->whereYear('consultation_date', $year);
                                                   }
                                               })
                                               ->distinct('clinical_records.id')
                                               ->count('clinical_records.id')
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
    private function getEmergencyDashboard($request = null)
    {
        // Obtener filtros de fecha
        $year = $request ? $request->get('year', date('Y')) : date('Y');
        $month = $request ? $request->get('month') : date('n');
        $dateFrom = $request ? $request->get('date_from') : null;
        $dateTo = $request ? $request->get('date_to') : null;
        
        // Si month está vacío, significa "todos los meses" del año
        $useMonthFilter = !empty($month);
        
        // Si hay filtros de rango de fechas, usar esos en lugar de mes/año
        $useDateRangeFilter = !empty($dateFrom) && !empty($dateTo);
        
        $stats = [
            'consultas_emergencia_hoy' => MedicalConsultation::where('attention_type', 'emergencia')
                                                            ->when($useDateRangeFilter, function($query) use ($dateFrom, $dateTo) {
                                                                return $query->whereBetween('consultation_date', [$dateFrom, $dateTo]);
                                                            }, function($query) use ($useMonthFilter, $month, $year) {
                                                                if ($useMonthFilter) {
                                                                    return $query->whereMonth('consultation_date', $month)->whereYear('consultation_date', $year);
                                                                } else {
                                                                    return $query->whereYear('consultation_date', $year);
                                                                }
                                                            })
                                                            ->count(),
            'pacientes_estables' => MedicalConsultation::where('attention_type', 'emergencia')
                                                      ->where('final_status', 'estable')
                                                      ->when($useDateRangeFilter, function($query) use ($dateFrom, $dateTo) {
                                                          return $query->whereBetween('consultation_date', [$dateFrom, $dateTo]);
                                                      }, function($query) use ($useMonthFilter, $month, $year) {
                                                          if ($useMonthFilter) {
                                                              return $query->whereMonth('consultation_date', $month)->whereYear('consultation_date', $year);
                                                          } else {
                                                              return $query->whereYear('consultation_date', $year);
                                                          }
                                                      })
                                                      ->count(),
            'pacientes_delicados' => MedicalConsultation::where('attention_type', 'emergencia')
                                                       ->where('final_status', 'delicado')
                                                       ->when($useDateRangeFilter, function($query) use ($dateFrom, $dateTo) {
                                                           return $query->whereBetween('consultation_date', [$dateFrom, $dateTo]);
                                                       }, function($query) use ($useMonthFilter, $month, $year) {
                                                           if ($useMonthFilter) {
                                                               return $query->whereMonth('consultation_date', $month)->whereYear('consultation_date', $year);
                                                           } else {
                                                               return $query->whereYear('consultation_date', $year);
                                                           }
                                                       })
                                                             ->count(),
            'pacientes_hospitalizados' => MedicalConsultation::where('attention_type', 'emergencia')
                                                             ->where('final_status', 'hospitalizado')
                                                             ->when($useDateRangeFilter, function($query) use ($dateFrom, $dateTo) {
                                                                 return $query->whereBetween('consultation_date', [$dateFrom, $dateTo]);
                                                             }, function($query) use ($useMonthFilter, $month, $year) {
                                                                 if ($useMonthFilter) {
                                                                     return $query->whereMonth('consultation_date', $month)->whereYear('consultation_date', $year);
                                                                 } else {
                                                                     return $query->whereYear('consultation_date', $year);
                                                                 }
                                                             })
                                                             ->count(),
            'pacientes_referidos' => MedicalConsultation::where('attention_type', 'emergencia')
                                                        ->where('final_status', 'referido')
                                                        ->when($useDateRangeFilter, function($query) use ($dateFrom, $dateTo) {
                                                            return $query->whereBetween('consultation_date', [$dateFrom, $dateTo]);
                                                        }, function($query) use ($useMonthFilter, $month, $year) {
                                                            if ($useMonthFilter) {
                                                                return $query->whereMonth('consultation_date', $month)->whereYear('consultation_date', $year);
                                                            } else {
                                                                return $query->whereYear('consultation_date', $year);
                                                            }
                                                        })
                                                        ->count(),
            'pacientes_fallecidos' => MedicalConsultation::where('attention_type', 'emergencia')
                                                         ->where('final_status', 'fallecido')
                                                         ->when($useDateRangeFilter, function($query) use ($dateFrom, $dateTo) {
                                                             return $query->whereBetween('consultation_date', [$dateFrom, $dateTo]);
                                                         }, function($query) use ($useMonthFilter, $month, $year) {
                                                             if ($useMonthFilter) {
                                                                 return $query->whereMonth('consultation_date', $month)->whereYear('consultation_date', $year);
                                                             } else {
                                                                 return $query->whereYear('consultation_date', $year);
                                                             }
                                                         })
                                                         ->count(),
            'ninos' => MedicalConsultation::join('clinical_records', 'medical_consultations.clinical_record_id', '=', 'clinical_records.id')
                                         ->where('attention_type', 'emergencia')
                                         ->whereRaw('TIMESTAMPDIFF(YEAR, clinical_records.birth_date, CURDATE()) BETWEEN 0 AND 13')
                                         ->when($useDateRangeFilter, function($query) use ($dateFrom, $dateTo) {
                                             return $query->whereBetween('consultation_date', [$dateFrom, $dateTo]);
                                         }, function($query) use ($useMonthFilter, $month, $year) {
                                             if ($useMonthFilter) {
                                                 return $query->whereMonth('consultation_date', $month)->whereYear('consultation_date', $year);
                                             } else {
                                                 return $query->whereYear('consultation_date', $year);
                                             }
                                         })
                                         ->distinct('clinical_records.id')
                                         ->count('clinical_records.id'),
            'adolescentes' => MedicalConsultation::join('clinical_records', 'medical_consultations.clinical_record_id', '=', 'clinical_records.id')
                                               ->where('attention_type', 'emergencia')
                                               ->whereRaw('TIMESTAMPDIFF(YEAR, clinical_records.birth_date, CURDATE()) BETWEEN 14 AND 17')
                                               ->when($useDateRangeFilter, function($query) use ($dateFrom, $dateTo) {
                                                   return $query->whereBetween('consultation_date', [$dateFrom, $dateTo]);
                                               }, function($query) use ($useMonthFilter, $month, $year) {
                                                   if ($useMonthFilter) {
                                                       return $query->whereMonth('consultation_date', $month)->whereYear('consultation_date', $year);
                                                   } else {
                                                       return $query->whereYear('consultation_date', $year);
                                                   }
                                               })
                                               ->distinct('clinical_records.id')
                                               ->count('clinical_records.id'),
            'adultos' => MedicalConsultation::join('clinical_records', 'medical_consultations.clinical_record_id', '=', 'clinical_records.id')
                                         ->where('attention_type', 'emergencia')
                                         ->whereRaw('TIMESTAMPDIFF(YEAR, clinical_records.birth_date, CURDATE()) BETWEEN 18 AND 59')
                                         ->when($useDateRangeFilter, function($query) use ($dateFrom, $dateTo) {
                                             return $query->whereBetween('consultation_date', [$dateFrom, $dateTo]);
                                         }, function($query) use ($useMonthFilter, $month, $year) {
                                             if ($useMonthFilter) {
                                                 return $query->whereMonth('consultation_date', $month)->whereYear('consultation_date', $year);
                                             } else {
                                                 return $query->whereYear('consultation_date', $year);
                                             }
                                         })
                                         ->distinct('clinical_records.id')
                                         ->count('clinical_records.id'),
            'tercera_edad' => MedicalConsultation::join('clinical_records', 'medical_consultations.clinical_record_id', '=', 'clinical_records.id')
                                               ->where('attention_type', 'emergencia')
                                               ->whereRaw('TIMESTAMPDIFF(YEAR, clinical_records.birth_date, CURDATE()) >= 60')
                                               ->when($useDateRangeFilter, function($query) use ($dateFrom, $dateTo) {
                                                   return $query->whereBetween('consultation_date', [$dateFrom, $dateTo]);
                                               }, function($query) use ($useMonthFilter, $month, $year) {
                                                   if ($useMonthFilter) {
                                                       return $query->whereMonth('consultation_date', $month)->whereYear('consultation_date', $year);
                                                   } else {
                                                       return $query->whereYear('consultation_date', $year);
                                                   }
                                               })
                                               ->distinct('clinical_records.id')
                                               ->count('clinical_records.id')
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
        $month = $request ? $request->get('month') : date('n');
        $dateFrom = $request ? $request->get('date_from') : null;
        $dateTo = $request ? $request->get('date_to') : null;
        
        // Si month está vacío, significa "todos los meses" del año
        $useMonthFilter = !empty($month);
        
        // Si hay filtros de rango de fechas, usar esos en lugar de mes/año
        $useDateRangeFilter = !empty($dateFrom) && !empty($dateTo);
        
        // KPIs que SIEMPRE se mantienen (no se filtran por fecha)
        $stats = [
            'expedientes_activos' => ClinicalRecord::count(),
            'expedientes_temporales' => TemporaryPatient::count(),
        ];
        
        // KPIs que SÍ se filtran por fecha
        $stats['consultas_mes'] = MedicalConsultation::when($useDateRangeFilter, function($query) use ($dateFrom, $dateTo) {
            return $query->whereBetween('consultation_date', [$dateFrom, $dateTo]);
        }, function($query) use ($useMonthFilter, $month, $year) {
            if ($useMonthFilter) {
            return $query->whereMonth('consultation_date', $month)->whereYear('consultation_date', $year);
            } else {
            return $query->whereYear('consultation_date', $year);
            }
        })->count();
        
        // Pacientes por género - Usar filtro de fecha apropiado
        if ($useDateRangeFilter) {
            // Si hay filtro de rango de fechas, contar solo pacientes con consultas en ese rango
            $stats['pacientes_hombres'] = MedicalConsultation::join('clinical_records', 'medical_consultations.clinical_record_id', '=', 'clinical_records.id')
                ->join('sexes', 'clinical_records.sex_id', '=', 'sexes.id')
                ->where(function($query) {
                    $query->where('sexes.name', 'LIKE', '%masculino%')
                          ->orWhere('sexes.name', 'LIKE', '%hombre%');
                })
                ->whereBetween('medical_consultations.consultation_date', [$dateFrom, $dateTo])
                ->distinct('clinical_records.id')
                ->count('clinical_records.id');
                
            $stats['pacientes_mujeres'] = MedicalConsultation::join('clinical_records', 'medical_consultations.clinical_record_id', '=', 'clinical_records.id')
                ->join('sexes', 'clinical_records.sex_id', '=', 'sexes.id')
                ->where(function($query) {
                    $query->where('sexes.name', 'LIKE', '%femenino%')
                          ->orWhere('sexes.name', 'LIKE', '%mujer%');
                })
                ->whereBetween('medical_consultations.consultation_date', [$dateFrom, $dateTo])
                ->distinct('clinical_records.id')
                ->count('clinical_records.id');
        } elseif ($useMonthFilter) {
            // Si hay filtro de mes, contar solo pacientes con consultas en ese mes
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
            // Si no hay filtro de mes, contar todos los expedientes del año
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
        
        $stats['ninos'] = MedicalConsultation::join('clinical_records', 'medical_consultations.clinical_record_id', '=', 'clinical_records.id')
                                            ->whereRaw('TIMESTAMPDIFF(YEAR, clinical_records.birth_date, CURDATE()) BETWEEN 0 AND 13')
                                            ->when($useDateRangeFilter, function($query) use ($dateFrom, $dateTo) {
                                                return $query->whereBetween('consultation_date', [$dateFrom, $dateTo]);
                                            }, function($query) use ($useMonthFilter, $month, $year) {
                                                if ($useMonthFilter) {
                                                         return $query->whereMonth('consultation_date', $month)->whereYear('consultation_date', $year);
                                                } else {
                                                         return $query->whereYear('consultation_date', $year);
                                                }
                                                     })
                                                     ->distinct('clinical_records.id')
                                                     ->count('clinical_records.id');
                                                     
        $stats['adolescentes'] = MedicalConsultation::join('clinical_records', 'medical_consultations.clinical_record_id', '=', 'clinical_records.id')
                                                  ->whereRaw('TIMESTAMPDIFF(YEAR, clinical_records.birth_date, CURDATE()) BETWEEN 14 AND 17')
                                                  ->when($useDateRangeFilter, function($query) use ($dateFrom, $dateTo) {
                                                      return $query->whereBetween('consultation_date', [$dateFrom, $dateTo]);
                                                  }, function($query) use ($useMonthFilter, $month, $year) {
                                                      if ($useMonthFilter) {
                                                         return $query->whereMonth('consultation_date', $month)->whereYear('consultation_date', $year);
                                                      } else {
                                                         return $query->whereYear('consultation_date', $year);
                                                      }
                                                     })
                                                     ->distinct('clinical_records.id')
                                                     ->count('clinical_records.id');
                                                  
        $stats['adultos'] = MedicalConsultation::join('clinical_records', 'medical_consultations.clinical_record_id', '=', 'clinical_records.id')
                                            ->whereRaw('TIMESTAMPDIFF(YEAR, clinical_records.birth_date, CURDATE()) BETWEEN 18 AND 59')
                                            ->when($useDateRangeFilter, function($query) use ($dateFrom, $dateTo) {
                                                return $query->whereBetween('consultation_date', [$dateFrom, $dateTo]);
                                            }, function($query) use ($useMonthFilter, $month, $year) {
                                                if ($useMonthFilter) {
                                                    return $query->whereMonth('consultation_date', $month)->whereYear('consultation_date', $year);
                                                } else {
                                                    return $query->whereYear('consultation_date', $year);
                                                }
                                            })
                                            ->distinct('clinical_records.id')
                                            ->count('clinical_records.id');
                                            
        $stats['tercera_edad'] = MedicalConsultation::join('clinical_records', 'medical_consultations.clinical_record_id', '=', 'clinical_records.id')
                                                   ->whereRaw('TIMESTAMPDIFF(YEAR, clinical_records.birth_date, CURDATE()) >= 60')
                                                   ->when($useDateRangeFilter, function($query) use ($dateFrom, $dateTo) {
                                                       return $query->whereBetween('consultation_date', [$dateFrom, $dateTo]);
                                                   }, function($query) use ($useMonthFilter, $month, $year) {
                                                       if ($useMonthFilter) {
                                                           return $query->whereMonth('consultation_date', $month)->whereYear('consultation_date', $year);
                                                       } else {
                                                           return $query->whereYear('consultation_date', $year);
                                                       }
                                                   })
                                                   ->distinct('clinical_records.id')
                                                   ->count('clinical_records.id');

        // Agregar métricas de estados finales para estadística
        $stats['pacientes_estables'] = MedicalConsultation::join('patient_statuses', 'medical_consultations.patient_status_id', '=', 'patient_statuses.id')
                                                         ->where('patient_statuses.name', 'Estable')
                                                         ->when($useDateRangeFilter, function($query) use ($dateFrom, $dateTo) {
                                                             return $query->whereBetween('consultation_date', [$dateFrom, $dateTo]);
                                                         }, function($query) use ($useMonthFilter, $month, $year) {
                                                             if ($useMonthFilter) {
                                                                 return $query->whereMonth('consultation_date', $month)->whereYear('consultation_date', $year);
                                                             } else {
                                                                 return $query->whereYear('consultation_date', $year);
                                                             }
                                                         })
                                                         ->count();
        
        $stats['pacientes_delicados'] = MedicalConsultation::join('patient_statuses', 'medical_consultations.patient_status_id', '=', 'patient_statuses.id')
                                                          ->where('patient_statuses.name', 'Delicado')
                                                          ->when($useDateRangeFilter, function($query) use ($dateFrom, $dateTo) {
                                                              return $query->whereBetween('consultation_date', [$dateFrom, $dateTo]);
                                                          }, function($query) use ($useMonthFilter, $month, $year) {
                                                              if ($useMonthFilter) {
                                                                  return $query->whereMonth('consultation_date', $month)->whereYear('consultation_date', $year);
                                                              } else {
                                                                  return $query->whereYear('consultation_date', $year);
                                                              }
                                                          })
                                                          ->count();
        
        $stats['pacientes_fallecidos'] = MedicalConsultation::join('patient_statuses', 'medical_consultations.patient_status_id', '=', 'patient_statuses.id')
                                                           ->where('patient_statuses.name', 'Fallecido')
                                                           ->when($useDateRangeFilter, function($query) use ($dateFrom, $dateTo) {
                                                               return $query->whereBetween('consultation_date', [$dateFrom, $dateTo]);
                                                           }, function($query) use ($useMonthFilter, $month, $year) {
                                                               if ($useMonthFilter) {
                                                                   return $query->whereMonth('consultation_date', $month)->whereYear('consultation_date', $year);
                                                               } else {
                                                                   return $query->whereYear('consultation_date', $year);
                                                               }
                                                           })
                                                           ->count();
        
        $stats['pacientes_hospitalizados'] = MedicalConsultation::where('final_status', 'hospitalizado')
                                                               ->when($useDateRangeFilter, function($query) use ($dateFrom, $dateTo) {
                                                                   return $query->whereBetween('consultation_date', [$dateFrom, $dateTo]);
                                                               }, function($query) use ($useMonthFilter, $month, $year) {
                                                                   if ($useMonthFilter) {
                                                                       return $query->whereMonth('consultation_date', $month)->whereYear('consultation_date', $year);
                                                                   } else {
                                                                       return $query->whereYear('consultation_date', $year);
                                                                   }
                                                               })
                                                               ->count();
        
        $stats['pacientes_referidos'] = MedicalConsultation::where('final_status', 'referido')
                                                          ->when($useDateRangeFilter, function($query) use ($dateFrom, $dateTo) {
                                                              return $query->whereBetween('consultation_date', [$dateFrom, $dateTo]);
                                                          }, function($query) use ($useMonthFilter, $month, $year) {
                                                              if ($useMonthFilter) {
                                                                  return $query->whereMonth('consultation_date', $month)->whereYear('consultation_date', $year);
                                                              } else {
                                                                  return $query->whereYear('consultation_date', $year);
                                                              }
                                                          })
                                                          ->count();

        // Distribución por sexo (con filtros)
        $distribucionSexo = MedicalConsultation::join('clinical_records', 'medical_consultations.clinical_record_id', '=', 'clinical_records.id')
            ->join('sexes', 'clinical_records.sex_id', '=', 'sexes.id')
            ->when($useDateRangeFilter, function($query) use ($dateFrom, $dateTo) {
                return $query->whereBetween('consultation_date', [$dateFrom, $dateTo]);
            }, function($query) use ($useMonthFilter, $month, $year) {
                if ($useMonthFilter) {
                return $query->whereMonth('consultation_date', $month)->whereYear('consultation_date', $year);
                } else {
                return $query->whereYear('consultation_date', $year);
                }
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


        return [
            'role' => 'Estadística',
            'stats' => $stats,
            'distribucionSexo' => $distribucionSexo,
            'expedientesPorMes' => $expedientesPorMes,
            'gruposEdad' => $gruposEdad
        ];
    }

    /**
     * Dashboard básico para otros roles
     */
    private function getBasicDashboard($user, $request = null)
    {
        // Obtener filtros de fecha
        $year = $request ? $request->get('year', date('Y')) : date('Y');
        $month = $request ? $request->get('month') : date('n');
        $dateFrom = $request ? $request->get('date_from') : null;
        $dateTo = $request ? $request->get('date_to') : null;
        
        // Si month está vacío, significa "todos los meses" del año
        $useMonthFilter = !empty($month);
        
        // Si hay filtros de rango de fechas, usar esos en lugar de mes/año
        $useDateRangeFilter = !empty($dateFrom) && !empty($dateTo);
        
        // Obtener meses con datos reales para el año seleccionado
        $monthsWithData = $this->getMonthsWithData($year);
        
        // Obtener años con datos reales
        $yearsWithData = $this->getYearsWithData();
        
        $stats = [
            'expedientes_activos' => ClinicalRecord::count(),
            'citas_hoy' => Appointment::when($useDateRangeFilter, function($query) use ($dateFrom, $dateTo) {
                return $query->whereBetween('appointment_date', [$dateFrom, $dateTo]);
            }, function($query) use ($useMonthFilter, $month, $year) {
                if ($useMonthFilter) {
                    return $query->whereMonth('appointment_date', $month)->whereYear('appointment_date', $year);
                } else {
                    return $query->whereYear('appointment_date', $year);
                }
            })
                                    ->count(),
            'consultas_mes' => MedicalConsultation::when($useDateRangeFilter, function($query) use ($dateFrom, $dateTo) {
                return $query->whereBetween('consultation_date', [$dateFrom, $dateTo]);
            }, function($query) use ($useMonthFilter, $month, $year) {
                if ($useMonthFilter) {
                    return $query->whereMonth('consultation_date', $month)->whereYear('consultation_date', $year);
                } else {
                    return $query->whereYear('consultation_date', $year);
                }
            })->count()
        ];

        return [
            'role' => $user->getRoleName(),
            'stats' => $stats,
            'monthsWithData' => $monthsWithData,
            'yearsWithData' => $yearsWithData
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
