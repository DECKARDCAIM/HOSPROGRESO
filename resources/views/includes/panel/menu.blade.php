<hr class="horizontal dark" />
<h6 class="ps-3 ms-2 text-uppercase text-xs font-weight-bolder text-dark">Panel de Controles</h6>

<ul class="navbar-nav">
    <!-- EMERGENCIA - Aparece si tiene acceso base O algún submódulo -->
    @if(auth()->check() && (auth()->user()->isAdmin() || Gate::any([
        'emergencia.acceso', 
        'emergencia.expedientes.ver', 'emergencia.expedientes.crear', 'emergencia.expedientes.editar', 'emergencia.expedientes.eliminar',
        'emergencia.consultas.ver', 'emergencia.consultas.crear', 'emergencia.consultas.editar', 'emergencia.consultas.eliminar',
        'emergencia.citas.ver', 'emergencia.citas.crear', 'emergencia.citas.editar', 'emergencia.citas.eliminar'
    ])))
    <li class="nav-item">
        <a data-bs-toggle="collapse" href="#menuEmergencia" class="nav-link text-dark mobile-menu-item"
            aria-controls="menuEmergencia" role="button" aria-expanded="false">
            <i class="bi bi-hospital opacity-5"></i>
            <span class="nav-link-text ms-1 ps-1">Emergencia</span>
        </a>
        <div class="collapse" id="menuEmergencia">
            <ul class="nav mobile-submenu">
                <!-- Expedientes Clínicos de Emergencia -->
                @if(auth()->user()->isAdmin() || Gate::any(['emergencia.expedientes.ver', 'emergencia.expedientes.crear', 'emergencia.expedientes.editar', 'emergencia.expedientes.eliminar']))
                <li class="nav-item">
                    <a class="nav-link text-dark mobile-submenu-item" href="{{ route('clinical-records.index') }}">
                        <i class="bi bi-folder2-open opacity-5 me-2"></i>
                        <span class="sidenav-normal">Expedientes Clínicos</span>
                    </a>
                </li>
                @endif
                
                <!-- Consultas Médicas de Emergencia -->
                @if(auth()->user()->isAdmin() || Gate::any(['emergencia.consultas.ver', 'emergencia.consultas.crear', 'emergencia.consultas.editar', 'emergencia.consultas.eliminar']))
                <li class="nav-item">
                    <a class="nav-link text-dark mobile-submenu-item" href="{{ route('medical-consultations.index') }}">
                        <i class="bi bi-heart-pulse opacity-5 me-2"></i>
                        <span class="sidenav-normal">Consultas Médicas</span>
                    </a>
                </li>
                @endif
                
                <!-- Gestión de Citas de Emergencia -->
                @if(auth()->user()->isAdmin() || Gate::any(['emergencia.citas.ver', 'emergencia.citas.crear', 'emergencia.citas.editar', 'emergencia.citas.eliminar']))
                <li class="nav-item">
                    <a class="nav-link text-dark mobile-submenu-item" href="{{ route('appointments.index') }}">
                        <i class="bi bi-calendar-date opacity-5 me-2"></i>
                        <span class="sidenav-normal">Gestión de Citas</span>
                    </a>
                </li>
                @endif
            </ul>
        </div>
    </li>
    @endif

    <!-- CONSULTA EXTERNA - Aparece si tiene acceso base O algún submódulo -->
    @if(auth()->check() && (auth()->user()->isAdmin() || Gate::any([
        'consulta_externa.acceso',
        'consulta_externa.expedientes.ver', 'consulta_externa.expedientes.crear', 'consulta_externa.expedientes.editar', 'consulta_externa.expedientes.eliminar',
        'consulta_externa.consultas.ver', 'consulta_externa.consultas.crear', 'consulta_externa.consultas.editar', 'consulta_externa.consultas.eliminar',
        'consulta_externa.citas.ver', 'consulta_externa.citas.crear', 'consulta_externa.citas.editar', 'consulta_externa.citas.eliminar'
    ])))
    <li class="nav-item">
        <a data-bs-toggle="collapse" href="#menuConsulta" class="nav-link text-dark mobile-menu-item"
            aria-controls="menuConsulta" role="button" aria-expanded="false">
            <i class="bi bi-prescription2 opacity-5"></i>
            <span class="nav-link-text ms-1 ps-1">Consulta Externa</span>
        </a>
        <div class="collapse" id="menuConsulta">
            <ul class="nav mobile-submenu">
                <!-- Expedientes Clínicos de Consulta Externa -->
                @if(auth()->user()->isAdmin() || Gate::any(['consulta_externa.expedientes.ver', 'consulta_externa.expedientes.crear', 'consulta_externa.expedientes.editar', 'consulta_externa.expedientes.eliminar']))
                <li class="nav-item">
                    <a class="nav-link text-dark mobile-submenu-item" href="{{ route('clinical-records.index') }}">
                        <i class="bi bi-folder2-open opacity-5 me-2"></i>
                        <span class="sidenav-normal">Expedientes Clínicos</span>
                    </a>
                </li>
                @endif
                
                <!-- Consultas Médicas de Consulta Externa -->
                @if(auth()->user()->isAdmin() || Gate::any(['consulta_externa.consultas.ver', 'consulta_externa.consultas.crear', 'consulta_externa.consultas.editar', 'consulta_externa.consultas.eliminar']))
                <li class="nav-item">
                    <a class="nav-link text-dark mobile-submenu-item" href="{{ route('medical-consultations.index') }}">
                        <i class="bi bi-heart-pulse opacity-5 me-2"></i>
                        <span class="sidenav-normal">Consultas Médicas</span>
                    </a>
                </li>
                @endif
                
                <!-- Gestión de Citas de Consulta Externa -->
                @if(auth()->user()->isAdmin() || Gate::any(['consulta_externa.citas.ver', 'consulta_externa.citas.crear', 'consulta_externa.citas.editar', 'consulta_externa.citas.eliminar']))
                <li class="nav-item">
                    <a class="nav-link text-dark mobile-submenu-item" href="{{ route('appointments.index') }}">
                        <i class="bi bi-calendar-date opacity-5 me-2"></i>
                        <span class="sidenav-normal">Gestión de Citas</span>
                    </a>
                </li>
                @endif
            </ul>
        </div>
    </li>
    @endif

    <!-- MANTENIMIENTO - Aparece si tiene acceso base O algún submódulo -->
    @if(auth()->check() && (auth()->user()->isAdmin() || Gate::any([
        'mantenimiento.acceso', 
        'especialidades.ver', 'doctores.ver', 'tipos_horario.ver',
        'sexos.ver', 'estados_civiles.ver', 'comunidades_linguisticas.ver', 'etnias.ver', 'discapacidades.ver', 'alergias.ver', 'tipos_control.ver', 'relaciones_acompanantes.ver', 'metodos_anticonceptivos.ver', 'estados_paciente.ver',
        'pruebas_laboratorio.ver', 'examenes.ver', 'medicamentos.ver',
        'paises.ver', 'departamentos.ver', 'municipios.ver'
    ])))
    <li class="nav-item">
        <a data-bs-toggle="collapse" href="#menuMantenimiento" class="nav-link text-dark mobile-menu-item"
            aria-controls="menuMantenimiento" role="button" aria-expanded="false">
            <i class="bi bi-gear opacity-5"></i>
            <span class="nav-link-text ms-1 ps-1">Mantenimiento</span>
        </a>
        <div class="collapse" id="menuMantenimiento">
            <ul class="nav mobile-submenu">
                <!-- Gestión Médica Submenu -->
                @if(auth()->user()->isAdmin() || Gate::any(['especialidades.ver', 'doctores.ver', 'tipos_horario.ver']))
                <li class="nav-item">
                    <a class="nav-link text-dark mobile-submenu-item" data-bs-toggle="collapse" href="#menuGestionMedica" role="button"
                        aria-expanded="false" aria-controls="menuGestionMedica">
                        <i class="bi bi-hospital opacity-5 me-2"></i>
                        <span class="sidenav-normal">Gestión Médica</span>
                    </a>
                    <div class="collapse" id="menuGestionMedica">
                        <ul class="nav nav-sm flex-column mobile-submenu">
                            @if(auth()->user()->isAdmin() || Gate::check('especialidades.ver'))
                            <li class="nav-item">
                                <a class="nav-link text-dark mobile-submenu-item" href="{{ url('/especialidades') }}">
                                    <i class="bi bi-heart-pulse opacity-5 me-2"></i>
                                    <span class="sidenav-normal">Especialidades</span>
                                </a>
                            </li>
                            @endif
                            @if(auth()->user()->isAdmin() || Gate::check('doctores.ver'))
                            <li class="nav-item">
                                <a class="nav-link text-dark mobile-submenu-item" href="{{ route('doctors.index') }}">
                                    <i class="bi bi-people opacity-5 me-2"></i>
                                    <span class="sidenav-normal">Doctores</span>
                                </a>
                            </li>
                            @endif
                            @if(auth()->user()->isAdmin() || Gate::check('tipos_horario.ver'))
                            <li class="nav-item">
                                <a class="nav-link text-dark mobile-submenu-item" href="{{ route('schedule-types.index') }}">
                                    <i class="bi bi-clock opacity-5 me-2"></i>
                                    <span class="sidenav-normal">Tipos de Horario</span>
                                </a>
                            </li>
                            @endif
                        </ul>
                    </div>
                </li>
                @endif

                <!-- Catálogos Médicos Submenu -->
                @if(auth()->user()->isAdmin() || Gate::any(['sexos.ver', 'estados_civiles.ver', 'comunidades_linguisticas.ver', 'etnias.ver', 'discapacidades.ver', 'alergias.ver', 'tipos_control.ver', 'relaciones_acompanantes.ver', 'metodos_anticonceptivos.ver', 'estados_paciente.ver']))
                <li class="nav-item">
                    <a class="nav-link text-dark mobile-submenu-item" data-bs-toggle="collapse" href="#menuCatalogosMedicos" role="button"
                        aria-expanded="false" aria-controls="menuCatalogosMedicos">
                        <i class="bi bi-tags opacity-5 me-2"></i>
                        <span class="sidenav-normal">Catálogos Médicos</span>
                    </a>
                    <div class="collapse" id="menuCatalogosMedicos">
                        <ul class="nav nav-sm flex-column mobile-submenu">
                            @if(auth()->user()->isAdmin() || Gate::check('sexos.ver'))
                            <li class="nav-item">
                                <a class="nav-link text-dark mobile-submenu-item" href="{{ route('sexes.index') }}">
                                    <i class="bi bi-gender-ambiguous opacity-5 me-2"></i>
                                    <span class="sidenav-normal">Sexos</span>
                                </a>
                            </li>
                            @endif
                            @if(auth()->user()->isAdmin() || Gate::check('estados_civiles.ver'))
                            <li class="nav-item">
                                <a class="nav-link text-dark mobile-submenu-item" href="{{ route('civil-statuses.index') }}">
                                    <i class="bi bi-heart-pulse opacity-5 me-2"></i>
                                    <span class="sidenav-normal">Estados Civiles</span>
                                </a>
                            </li>
                            @endif
                            @if(auth()->user()->isAdmin() || Gate::check('comunidades_linguisticas.ver'))
                            <li class="nav-item">
                                <a class="nav-link text-dark mobile-submenu-item" href="{{ route('linguistic-communities.index') }}">
                                    <i class="bi bi-translate opacity-5 me-2"></i>
                                    <span class="sidenav-normal">Comunidades Lingüísticas</span>
                                </a>
                            </li>
                            @endif
                            @if(auth()->user()->isAdmin() || Gate::check('etnias.ver'))
                            <li class="nav-item">
                                <a class="nav-link text-dark mobile-submenu-item" href="{{ route('ethnicities.index') }}">
                                    <i class="bi bi-people-fill opacity-5 me-2"></i>
                                    <span class="sidenav-normal">Etnias</span>
                                </a>
                            </li>
                            @endif
                            @if(auth()->user()->isAdmin() || Gate::check('discapacidades.ver'))
                            <li class="nav-item">
                                <a class="nav-link text-dark mobile-submenu-item" href="{{ route('disabilities.index') }}">
                                    <i class="bi bi-hand-index-thumb opacity-5 me-2"></i>
                                    <span class="sidenav-normal">Discapacidades</span>
                                </a>
                            </li>
                            @endif
                            @if(auth()->user()->isAdmin() || Gate::check('alergias.ver'))
                            <li class="nav-item">
                                <a class="nav-link text-dark mobile-submenu-item" href="{{ route('allergies.index') }}">
                                    <i class="bi bi-exclamation-triangle opacity-5 me-2"></i>
                                    <span class="sidenav-normal">Alergias</span>
                                </a>
                            </li>
                            @endif
                            @if(auth()->user()->isAdmin() || Gate::check('tipos_control.ver'))
                            <li class="nav-item">
                                <a class="nav-link text-dark mobile-submenu-item" href="{{ route('control-types.index') }}">
                                    <i class="bi bi-journal-text opacity-5 me-2"></i>
                                    <span class="sidenav-normal">Tipos de Control</span>
                                </a>
                            </li>
                            @endif
                            @if(auth()->user()->isAdmin() || Gate::check('relaciones_acompanantes.ver'))
                            <li class="nav-item">
                                <a class="nav-link text-dark mobile-submenu-item" href="{{ route('companion-relationships.index') }}">
                                    <i class="bi bi-people opacity-5 me-2"></i>
                                    <span class="sidenav-normal">Relaciones de Acompañantes</span>
                                </a>
                            </li>
                            @endif
                            @if(auth()->user()->isAdmin() || Gate::check('metodos_anticonceptivos.ver'))
                            <li class="nav-item">
                                <a class="nav-link text-dark mobile-submenu-item" href="{{ route('contraceptive-methods.index') }}">
                                    <i class="bi bi-shield-check opacity-5 me-2"></i>
                                    <span class="sidenav-normal">Métodos Anticonceptivos</span>
                                </a>
                            </li>
                            @endif
                            @if(auth()->user()->isAdmin() || Gate::check('estados_paciente.ver'))
                            <li class="nav-item">
                                <a class="nav-link text-dark mobile-submenu-item" href="{{ route('patient-statuses.index') }}">
                                    <i class="bi bi-activity opacity-5 me-2"></i>
                                    <span class="sidenav-normal">Estados del Paciente</span>
                                </a>
                            </li>
                            @endif
                        </ul>
                    </div>
                </li>
                @endif

                <!-- Estudios y Medicamentos Submenu -->
                @if(auth()->user()->isAdmin() || Gate::any(['pruebas_laboratorio.ver', 'examenes.ver', 'medicamentos.ver']))
                <li class="nav-item">
                    <a class="nav-link text-dark mobile-submenu-item" data-bs-toggle="collapse" href="#menuEstudiosMedicamentos" role="button"
                        aria-expanded="false" aria-controls="menuEstudiosMedicamentos">
                        <i class="bi bi-heart-pulse opacity-5 me-2"></i>
                        <span class="sidenav-normal">Estudios y Medicamentos</span>
                    </a>
                    <div class="collapse" id="menuEstudiosMedicamentos">
                        <ul class="nav nav-sm flex-column mobile-submenu">
                            @if(auth()->user()->isAdmin() || Gate::check('pruebas_laboratorio.ver'))
                            <li class="nav-item">
                                <a class="nav-link text-dark mobile-submenu-item" href="{{ route('laboratory-tests.index') }}">
                                    <i class="bi bi-droplet opacity-5 me-2"></i>
                                    <span class="sidenav-normal">Pruebas de Laboratorio</span>
                                </a>
                            </li>
                            @endif
                            @if(auth()->user()->isAdmin() || Gate::check('examenes.ver'))
                            <li class="nav-item">
                                <a class="nav-link text-dark mobile-submenu-item" href="{{ route('exams.index') }}">
                                    <i class="bi bi-lightning opacity-5 me-2"></i>
                                    <span class="sidenav-normal">Exámenes</span>
                                </a>
                            </li>
                            @endif
                            @if(auth()->user()->isAdmin() || Gate::check('medicamentos.ver'))
                            <li class="nav-item">
                                <a class="nav-link text-dark mobile-submenu-item" href="{{ route('medications.index') }}">
                                    <i class="bi bi-circle-square opacity-5 me-2"></i>
                                    <span class="sidenav-normal">Medicamentos</span>
                                </a>
                            </li>
                            @endif
                        </ul>
                    </div>
                </li>
                @endif

                <!-- Ubicaciones Submenu -->
                @if(auth()->user()->isAdmin() || Gate::any(['paises.ver', 'departamentos.ver', 'municipios.ver']))
                <li class="nav-item">
                    <a class="nav-link text-dark mobile-submenu-item" data-bs-toggle="collapse" href="#menuUbicaciones" role="button"
                        aria-expanded="false" aria-controls="menuUbicaciones">
                        <i class="bi bi-geo-alt opacity-5 me-2"></i>
                        <span class="sidenav-normal">Ubicaciones</span>
                    </a>
                    <div class="collapse" id="menuUbicaciones">
                        <ul class="nav nav-sm flex-column mobile-submenu">
                            @if(auth()->user()->isAdmin() || Gate::check('paises.ver'))
                            <li class="nav-item">
                                <a class="nav-link text-dark mobile-submenu-item" href="{{ url('/paises') }}">
                                    <i class="bi bi-flag opacity-5 me-2"></i>
                                    <span class="sidenav-normal">Países</span>
                                </a>
                            </li>
                            @endif
                            @if(auth()->user()->isAdmin() || Gate::check('departamentos.ver'))
                            <li class="nav-item">
                                <a class="nav-link text-dark mobile-submenu-item" href="{{ url('/departamentos') }}">
                                    <i class="bi bi-map opacity-5 me-2"></i>
                                    <span class="sidenav-normal">Departamentos</span>
                                </a>
                            </li>
                            @endif
                            @if(auth()->user()->isAdmin() || Gate::check('municipios.ver'))
                            <li class="nav-item">
                                <a class="nav-link text-dark mobile-submenu-item" href="{{ url('/municipios') }}">
                                    <i class="bi bi-geo-alt opacity-5 me-2"></i>
                                    <span class="sidenav-normal">Municipios</span>
                                </a>
                            </li>
                            @endif
                        </ul>
                    </div>
                </li>
                @endif
            </ul>
        </div>
    </li>
    @endif

    <!-- Archivo Clínico -->
    @if(auth()->check() && (auth()->user()->isAdmin() || Gate::any(['archivo_clinico.acceso', 'archivo_clinico.expedientes_recientes', 'archivo_clinico.expedientes_archivados'])))
    <li class="nav-item">
        <a data-bs-toggle="collapse" href="#menuArchivo" class="nav-link text-dark mobile-menu-item"
            aria-controls="menuArchivo" role="button" aria-expanded="false">
            <i class="bi bi-archive opacity-5"></i>
            <span class="nav-link-text ms-1 ps-1">Archivo Clínico</span>
        </a>
        <div class="collapse" id="menuArchivo">
            <ul class="nav mobile-submenu">
                @if(auth()->user()->isAdmin() || Gate::check('archivo_clinico.expedientes_recientes'))
                <li class="nav-item">
                    <a class="nav-link text-dark mobile-submenu-item" href="{{ route('clinical-file.index') }}">
                        <i class="bi bi-file-medical opacity-5 me-2"></i>
                        <span class="sidenav-normal">Expedientes Recientes</span>
                    </a>
                </li>
                @endif
                @if(auth()->user()->isAdmin() || Gate::check('archivo_clinico.expedientes_archivados'))
                <li class="nav-item">
                    <a class="nav-link text-dark mobile-submenu-item" href="{{ route('clinical-file.archived') }}">
                        <i class="bi bi-archive-fill opacity-5 me-2"></i>
                        <span class="sidenav-normal">Expedientes Archivados</span>
                    </a>
                </li>
                @endif
            </ul>
        </div>
    </li>
    @endif

    <!-- Administración del Sistema -->
    @if(auth()->check() && (auth()->user()->isAdmin() || Gate::any(['administracion.acceso', 'usuarios.ver', 'roles.ver', 'import.acceso'])))
    <li class="nav-item">
        <a data-bs-toggle="collapse" href="#menuAdministracion" class="nav-link text-dark mobile-menu-item"
            aria-controls="menuAdministracion" role="button" aria-expanded="false">
            <i class="bi bi-gear opacity-5"></i>
            <span class="nav-link-text ms-1 ps-1">Administración</span>
        </a>
        <div class="collapse" id="menuAdministracion">
            <ul class="nav mobile-submenu">
                @if(auth()->user()->isAdmin() || Gate::check('usuarios.ver'))
                <li class="nav-item">
                    <a class="nav-link text-dark mobile-submenu-item" href="{{ route('usuarios.index') }}">
                        <i class="bi bi-people opacity-5 me-2"></i>
                        <span class="sidenav-normal">Gestión de Usuarios</span>
                    </a>
                </li>
                @endif
                @if(auth()->user()->isAdmin() || Gate::check('roles.ver'))
                <li class="nav-item">
                    <a class="nav-link text-dark mobile-submenu-item" href="{{ route('roles.index') }}">
                        <i class="bi bi-shield-lock opacity-5 me-2"></i>
                        <span class="sidenav-normal">Roles de Usuario</span>
                    </a>
                </li>
                @endif
                @if(auth()->user()->isAdmin() || Gate::check('import.acceso'))
                <li class="nav-item">
                    <a class="nav-link text-dark mobile-submenu-item" href="{{ route('import.index') }}">
                        <i class="bi bi-database opacity-5 me-2"></i>
                        <span class="sidenav-normal">Base de Datos</span>
                    </a>
                </li>
                @endif
            </ul>
        </div>
    </li>
    @endif

    <!-- Reportes SIGSA 3H -->
    @if(auth()->check() && (auth()->user()->isAdmin() || Gate::any(['reportes.ver', 'reportes.generar'])))
    <li class="nav-item">
        <a data-bs-toggle="collapse" href="#menuReportes" class="nav-link text-dark mobile-menu-item"
            aria-controls="menuReportes" role="button" aria-expanded="false">
            <i class="bi bi-graph-up-arrow opacity-5"></i>
            <span class="nav-link-text ms-1 ps-1">Reportes SIGSA 3H</span>
        </a>
        <div class="collapse" id="menuReportes">
            <ul class="nav mobile-submenu">
                @if(auth()->user()->isAdmin() || Gate::check('reportes.ver'))
                <li class="nav-item">
                    <a class="nav-link text-dark mobile-submenu-item" href="{{ route('reports.index') }}">
                        <i class="bi bi-bar-chart opacity-5 me-2"></i>
                        <span class="sidenav-normal">Generar Reportes</span>
                    </a>
                </li>
                @endif
            </ul>
        </div>
    </li>
    @endif
</ul>