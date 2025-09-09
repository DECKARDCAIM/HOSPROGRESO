<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Permission;
use App\Models\Role;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeder.
     */
    public function run(): void
    {
        // Estructura JERÁRQUICA de permisos del sistema
        $permissionsData = [
            
            // ========== EMERGENCIA (MÓDULO PRINCIPAL) ==========
            'Emergencia' => [
                // Acceso base al módulo (sin submódulos)
                ['name' => 'Acceso módulo Emergencia', 'slug' => 'emergencia.acceso'],
                
                // Submódulo: Expedientes de Emergencia
                ['name' => 'Ver expedientes de Emergencia', 'slug' => 'emergencia.expedientes.ver'],
                ['name' => 'Crear expedientes de Emergencia', 'slug' => 'emergencia.expedientes.crear'],
                ['name' => 'Editar expedientes de Emergencia', 'slug' => 'emergencia.expedientes.editar'],
                ['name' => 'Eliminar expedientes de Emergencia', 'slug' => 'emergencia.expedientes.eliminar'],
                ['name' => 'Imprimir expedientes de Emergencia', 'slug' => 'emergencia.expedientes.imprimir'],
                
                // Submódulo: Consultas de Emergencia
                ['name' => 'Ver consultas de Emergencia', 'slug' => 'emergencia.consultas.ver'],
                ['name' => 'Crear consultas de Emergencia', 'slug' => 'emergencia.consultas.crear'],
                ['name' => 'Editar consultas de Emergencia', 'slug' => 'emergencia.consultas.editar'],
                ['name' => 'Eliminar consultas de Emergencia', 'slug' => 'emergencia.consultas.eliminar'],
                ['name' => 'Imprimir consultas de Emergencia', 'slug' => 'emergencia.consultas.imprimir'],
                
                // Submódulo: Citas de Emergencia
                ['name' => 'Ver citas de Emergencia', 'slug' => 'emergencia.citas.ver'],
                ['name' => 'Crear citas de Emergencia', 'slug' => 'emergencia.citas.crear'],
                ['name' => 'Editar citas de Emergencia', 'slug' => 'emergencia.citas.editar'],
                ['name' => 'Eliminar citas de Emergencia', 'slug' => 'emergencia.citas.eliminar'],
                ['name' => 'Gestionar citas de Emergencia', 'slug' => 'emergencia.citas.gestionar'],
            ],

            // ========== CONSULTA EXTERNA (MÓDULO PRINCIPAL) ==========
            'Consulta Externa' => [
                // Acceso base al módulo (sin submódulos)
                ['name' => 'Acceso módulo Consulta Externa', 'slug' => 'consulta_externa.acceso'],
                
                // Submódulo: Expedientes de Consulta Externa
                ['name' => 'Ver expedientes de Consulta Externa', 'slug' => 'consulta_externa.expedientes.ver'],
                ['name' => 'Crear expedientes de Consulta Externa', 'slug' => 'consulta_externa.expedientes.crear'],
                ['name' => 'Editar expedientes de Consulta Externa', 'slug' => 'consulta_externa.expedientes.editar'],
                ['name' => 'Eliminar expedientes de Consulta Externa', 'slug' => 'consulta_externa.expedientes.eliminar'],
                ['name' => 'Imprimir expedientes de Consulta Externa', 'slug' => 'consulta_externa.expedientes.imprimir'],
                
                // Submódulo: Consultas de Consulta Externa
                ['name' => 'Ver consultas de Consulta Externa', 'slug' => 'consulta_externa.consultas.ver'],
                ['name' => 'Crear consultas de Consulta Externa', 'slug' => 'consulta_externa.consultas.crear'],
                ['name' => 'Editar consultas de Consulta Externa', 'slug' => 'consulta_externa.consultas.editar'],
                ['name' => 'Eliminar consultas de Consulta Externa', 'slug' => 'consulta_externa.consultas.eliminar'],
                ['name' => 'Imprimir consultas de Consulta Externa', 'slug' => 'consulta_externa.consultas.imprimir'],
                
                // Submódulo: Citas de Consulta Externa
                ['name' => 'Ver citas de Consulta Externa', 'slug' => 'consulta_externa.citas.ver'],
                ['name' => 'Crear citas de Consulta Externa', 'slug' => 'consulta_externa.citas.crear'],
                ['name' => 'Editar citas de Consulta Externa', 'slug' => 'consulta_externa.citas.editar'],
                ['name' => 'Eliminar citas de Consulta Externa', 'slug' => 'consulta_externa.citas.eliminar'],
                ['name' => 'Gestionar citas de Consulta Externa', 'slug' => 'consulta_externa.citas.gestionar'],
            ],

            // ========== MANTENIMIENTO (MÓDULO PRINCIPAL) ==========
            'Mantenimiento' => [
                // Acceso base al módulo
                ['name' => 'Acceso módulo Mantenimiento', 'slug' => 'mantenimiento.acceso'],
            ],

            // ========== GESTIÓN MÉDICA ==========
            'Doctores' => [
                ['name' => 'Ver doctores', 'slug' => 'doctores.ver'],
                ['name' => 'Crear doctores', 'slug' => 'doctores.crear'],
                ['name' => 'Editar doctores', 'slug' => 'doctores.editar'],
                ['name' => 'Eliminar doctores', 'slug' => 'doctores.eliminar'],
                ['name' => 'Reactivar doctores', 'slug' => 'doctores.reactivar'],
            ],

            'Especialidades' => [
                ['name' => 'Ver especialidades', 'slug' => 'especialidades.ver'],
                ['name' => 'Crear especialidades', 'slug' => 'especialidades.crear'],
                ['name' => 'Editar especialidades', 'slug' => 'especialidades.editar'],
                ['name' => 'Eliminar especialidades', 'slug' => 'especialidades.eliminar'],
                ['name' => 'Reactivar especialidades', 'slug' => 'especialidades.reactivar'],
            ],

            'Tipos de Horario' => [
                ['name' => 'Ver tipos de horario', 'slug' => 'tipos_horario.ver'],
                ['name' => 'Crear tipos de horario', 'slug' => 'tipos_horario.crear'],
                ['name' => 'Editar tipos de horario', 'slug' => 'tipos_horario.editar'],
                ['name' => 'Eliminar tipos de horario', 'slug' => 'tipos_horario.eliminar'],
                ['name' => 'Reactivar tipos de horario', 'slug' => 'tipos_horario.reactivar'],
            ],

            'Sustituciones de Doctores' => [
                ['name' => 'Ver sustituciones de doctores', 'slug' => 'doctores.sustituciones.ver'],
                ['name' => 'Crear sustituciones de doctores', 'slug' => 'doctores.sustituciones.crear'],
                ['name' => 'Editar sustituciones de doctores', 'slug' => 'doctores.sustituciones.editar'],
                ['name' => 'Eliminar sustituciones de doctores', 'slug' => 'doctores.sustituciones.eliminar'],
                ['name' => 'Completar sustituciones de doctores', 'slug' => 'doctores.sustituciones.completar'],
                ['name' => 'Cancelar sustituciones de doctores', 'slug' => 'doctores.sustituciones.cancelar'],
            ],

            'Días Festivos' => [
                ['name' => 'Ver días festivos', 'slug' => 'dias_festivos.ver'],
                ['name' => 'Crear días festivos', 'slug' => 'dias_festivos.crear'],
                ['name' => 'Editar días festivos', 'slug' => 'dias_festivos.editar'],
                ['name' => 'Eliminar días festivos', 'slug' => 'dias_festivos.eliminar'],
                ['name' => 'Reactivar días festivos', 'slug' => 'dias_festivos.reactivar'],
            ],

            // ========== CATÁLOGOS MÉDICOS ==========
            'Sexos' => [
                ['name' => 'Ver sexos', 'slug' => 'sexos.ver'],
                ['name' => 'Crear sexos', 'slug' => 'sexos.crear'],
                ['name' => 'Editar sexos', 'slug' => 'sexos.editar'],
                ['name' => 'Eliminar sexos', 'slug' => 'sexos.eliminar'],
            ],

            'Estados Civiles' => [
                ['name' => 'Ver estados civiles', 'slug' => 'estados_civiles.ver'],
                ['name' => 'Crear estados civiles', 'slug' => 'estados_civiles.crear'],
                ['name' => 'Editar estados civiles', 'slug' => 'estados_civiles.editar'],
                ['name' => 'Eliminar estados civiles', 'slug' => 'estados_civiles.eliminar'],
            ],

            'Comunidades Lingüísticas' => [
                ['name' => 'Ver comunidades lingüísticas', 'slug' => 'comunidades_linguisticas.ver'],
                ['name' => 'Crear comunidades lingüísticas', 'slug' => 'comunidades_linguisticas.crear'],
                ['name' => 'Editar comunidades lingüísticas', 'slug' => 'comunidades_linguisticas.editar'],
                ['name' => 'Eliminar comunidades lingüísticas', 'slug' => 'comunidades_linguisticas.eliminar'],
            ],

            'Etnias' => [
                ['name' => 'Ver etnias', 'slug' => 'etnias.ver'],
                ['name' => 'Crear etnias', 'slug' => 'etnias.crear'],
                ['name' => 'Editar etnias', 'slug' => 'etnias.editar'],
                ['name' => 'Eliminar etnias', 'slug' => 'etnias.eliminar'],
            ],

            'Discapacidades' => [
                ['name' => 'Ver discapacidades', 'slug' => 'discapacidades.ver'],
                ['name' => 'Crear discapacidades', 'slug' => 'discapacidades.crear'],
                ['name' => 'Editar discapacidades', 'slug' => 'discapacidades.editar'],
                ['name' => 'Eliminar discapacidades', 'slug' => 'discapacidades.eliminar'],
            ],

            'Alergias' => [
                ['name' => 'Ver alergias', 'slug' => 'alergias.ver'],
                ['name' => 'Crear alergias', 'slug' => 'alergias.crear'],
                ['name' => 'Editar alergias', 'slug' => 'alergias.editar'],
                ['name' => 'Eliminar alergias', 'slug' => 'alergias.eliminar'],
            ],

            'Tipos de Control' => [
                ['name' => 'Ver tipos de control', 'slug' => 'tipos_control.ver'],
                ['name' => 'Crear tipos de control', 'slug' => 'tipos_control.crear'],
                ['name' => 'Editar tipos de control', 'slug' => 'tipos_control.editar'],
                ['name' => 'Eliminar tipos de control', 'slug' => 'tipos_control.eliminar'],
            ],

            'Relaciones de Acompañantes' => [
                ['name' => 'Ver relaciones de acompañantes', 'slug' => 'relaciones_acompanantes.ver'],
                ['name' => 'Crear relaciones de acompañantes', 'slug' => 'relaciones_acompanantes.crear'],
                ['name' => 'Editar relaciones de acompañantes', 'slug' => 'relaciones_acompanantes.editar'],
                ['name' => 'Eliminar relaciones de acompañantes', 'slug' => 'relaciones_acompanantes.eliminar'],
            ],

            'Métodos Anticonceptivos' => [
                ['name' => 'Ver métodos anticonceptivos', 'slug' => 'metodos_anticonceptivos.ver'],
                ['name' => 'Crear métodos anticonceptivos', 'slug' => 'metodos_anticonceptivos.crear'],
                ['name' => 'Editar métodos anticonceptivos', 'slug' => 'metodos_anticonceptivos.editar'],
                ['name' => 'Eliminar métodos anticonceptivos', 'slug' => 'metodos_anticonceptivos.eliminar'],
            ],

            'Estados del Paciente' => [
                ['name' => 'Ver estados del paciente', 'slug' => 'estados_paciente.ver'],
                ['name' => 'Crear estados del paciente', 'slug' => 'estados_paciente.crear'],
                ['name' => 'Editar estados del paciente', 'slug' => 'estados_paciente.editar'],
                ['name' => 'Eliminar estados del paciente', 'slug' => 'estados_paciente.eliminar'],
            ],

            // ========== ESTUDIOS Y MEDICAMENTOS ==========
            'Pruebas de Laboratorio' => [
                ['name' => 'Ver pruebas laboratorio', 'slug' => 'pruebas_laboratorio.ver'],
                ['name' => 'Crear pruebas laboratorio', 'slug' => 'pruebas_laboratorio.crear'],
                ['name' => 'Editar pruebas laboratorio', 'slug' => 'pruebas_laboratorio.editar'],
                ['name' => 'Eliminar pruebas laboratorio', 'slug' => 'pruebas_laboratorio.eliminar'],
            ],

            'Exámenes' => [
                ['name' => 'Ver exámenes', 'slug' => 'examenes.ver'],
                ['name' => 'Crear exámenes', 'slug' => 'examenes.crear'],
                ['name' => 'Editar exámenes', 'slug' => 'examenes.editar'],
                ['name' => 'Eliminar exámenes', 'slug' => 'examenes.eliminar'],
            ],

            'Medicamentos' => [
                ['name' => 'Ver medicamentos', 'slug' => 'medicamentos.ver'],
                ['name' => 'Crear medicamentos', 'slug' => 'medicamentos.crear'],
                ['name' => 'Editar medicamentos', 'slug' => 'medicamentos.editar'],
                ['name' => 'Eliminar medicamentos', 'slug' => 'medicamentos.eliminar'],
            ],

            // ========== UBICACIÓN ==========
            'Países' => [
                ['name' => 'Ver países', 'slug' => 'paises.ver'],
                ['name' => 'Crear países', 'slug' => 'paises.crear'],
                ['name' => 'Editar países', 'slug' => 'paises.editar'],
                ['name' => 'Eliminar países', 'slug' => 'paises.eliminar'],
                ['name' => 'Reactivar países', 'slug' => 'paises.reactivar'],
            ],

            'Departamentos' => [
                ['name' => 'Ver departamentos', 'slug' => 'departamentos.ver'],
                ['name' => 'Crear departamentos', 'slug' => 'departamentos.crear'],
                ['name' => 'Editar departamentos', 'slug' => 'departamentos.editar'],
                ['name' => 'Eliminar departamentos', 'slug' => 'departamentos.eliminar'],
                ['name' => 'Reactivar departamentos', 'slug' => 'departamentos.reactivar'],
            ],

            'Municipios' => [
                ['name' => 'Ver municipios', 'slug' => 'municipios.ver'],
                ['name' => 'Crear municipios', 'slug' => 'municipios.crear'],
                ['name' => 'Editar municipios', 'slug' => 'municipios.editar'],
                ['name' => 'Eliminar municipios', 'slug' => 'municipios.eliminar'],
                ['name' => 'Reactivar municipios', 'slug' => 'municipios.reactivar'],
            ],


            // ========== ADMINISTRACIÓN ==========
            'Administración' => [
                ['name' => 'Acceso módulo Administración', 'slug' => 'administracion.acceso'],
            ],

            'Gestión de Usuarios' => [
                ['name' => 'Ver usuarios', 'slug' => 'usuarios.ver'],
                ['name' => 'Crear usuarios', 'slug' => 'usuarios.crear'],
                ['name' => 'Editar usuarios', 'slug' => 'usuarios.editar'],
                ['name' => 'Eliminar usuarios', 'slug' => 'usuarios.eliminar'],
                ['name' => 'Reactivar usuarios', 'slug' => 'usuarios.reactivar'],
            ],

            'Roles de Usuario' => [
                ['name' => 'Ver roles', 'slug' => 'roles.ver'],
                ['name' => 'Crear roles', 'slug' => 'roles.crear'],
                ['name' => 'Editar roles', 'slug' => 'roles.editar'],
                ['name' => 'Eliminar roles', 'slug' => 'roles.eliminar'],
                ['name' => 'Gestionar permisos', 'slug' => 'roles.permisos'],
            ],

            'Exportar Datos' => [
                ['name' => 'Acceso exportar datos', 'slug' => 'import.acceso'],
                ['name' => 'Importar datos', 'slug' => 'import.importar'],
                ['name' => 'Gestionar datos temporales', 'slug' => 'import.temporal'],
            ],

            // ========== REPORTES ==========
            'Reportes SIGSA 3H' => [
                ['name' => 'Ver reportes', 'slug' => 'reportes.ver'],
                ['name' => 'Generar reportes', 'slug' => 'reportes.generar'],
                ['name' => 'Exportar reportes', 'slug' => 'reportes.exportar'],
            ],

            // ========== MI PERFIL ==========
            'Mi Perfil' => [
                ['name' => 'Ver mi perfil', 'slug' => 'perfil.ver'],
                ['name' => 'Editar mi perfil', 'slug' => 'perfil.editar'],
                ['name' => 'Cambiar contraseña', 'slug' => 'perfil.password'],
                ['name' => 'Gestionar foto', 'slug' => 'perfil.foto'],
                ['name' => 'Gestionar banner', 'slug' => 'perfil.banner'],
                ['name' => 'Gestionar sesiones', 'slug' => 'perfil.sesiones'],
            ],
        ];

        // Crear permisos
        foreach ($permissionsData as $module => $perms) {
            foreach ($perms as $permData) {
                Permission::firstOrCreate(
                    ['slug' => $permData['slug']],
                    [
                        'name' => $permData['name'],
                        'module' => $module
                    ]
                );
            }
        }

        // Asignar TODOS los permisos al rol Administrador
        $adminRole = Role::where('name', 'Administrador')->first();
        if ($adminRole) {
            $allPermissions = Permission::all();
            $adminRole->permissions()->sync($allPermissions->pluck('id'));
            
            $this->command->info('✅ Todos los permisos asignados al rol Administrador');
        }


        $totalPermissions = collect($permissionsData)->flatten(1)->count();
        $this->command->info("✅ $totalPermissions permisos creados exitosamente");
    }
}