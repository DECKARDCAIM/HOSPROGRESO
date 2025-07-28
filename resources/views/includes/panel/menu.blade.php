<hr class="horizontal dark" />
<h6 class="ps-3 ms-2 text-uppercase text-xs font-weight-bolder text-dark">Panel de Controles</h6>

<ul class="navbar-nav">
    <!-- Emergencia -->
    <li class="nav-item">
        <a data-bs-toggle="collapse" href="#menuEmergencia" class="nav-link text-dark mobile-menu-item"
            aria-controls="menuEmergencia" role="button" aria-expanded="false">
            <i class="material-symbols-rounded opacity-5">emergency</i>
            <span class="nav-link-text ms-1 ps-1">Emergencia</span>
            <i class="material-symbols-rounded ms-auto collapse-arrow">expand_more</i>
        </a>
        <div class="collapse" id="menuEmergencia">
            <ul class="nav mobile-submenu">
                <li class="nav-item">
                    <a class="nav-link text-dark mobile-submenu-item" href="{{ route('clinical-records.index') }}">
                        <i class="material-symbols-rounded opacity-5 me-2">folder_shared</i>
                        <span class="sidenav-normal">Expedientes Clínicos</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link text-dark mobile-submenu-item" href="{{ route('medical-consultations.index') }}">
                        <i class="material-symbols-rounded opacity-5 me-2">medical_services</i>
                        <span class="sidenav-normal">Consultas Médicas</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link text-dark mobile-submenu-item" href="{{ route('appointments.index') }}">
                        <i class="material-symbols-rounded opacity-5 me-2">calendar_month</i>
                        <span class="sidenav-normal">Gestión de Citas</span>
                    </a>
                </li>
            </ul>
        </div>
    </li>

    <!-- Consulta Externa -->
    <li class="nav-item">
        <a data-bs-toggle="collapse" href="#menuConsulta" class="nav-link text-dark mobile-menu-item"
            aria-controls="menuConsulta" role="button" aria-expanded="false">
            <i class="material-symbols-rounded opacity-5">medication</i>
            <span class="nav-link-text ms-1 ps-1">Consulta Externa</span>
            <i class="material-symbols-rounded ms-auto collapse-arrow">expand_more</i>
        </a>
        <div class="collapse" id="menuConsulta">
            <ul class="nav mobile-submenu">
                <li class="nav-item">
                    <a class="nav-link text-dark mobile-submenu-item" href="{{ route('clinical-records.index') }}">
                        <i class="material-symbols-rounded opacity-5 me-2">folder_shared</i>
                        <span class="sidenav-normal">Expedientes Clínicos</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link text-dark mobile-submenu-item" href="{{ route('medical-consultations.index') }}">
                        <i class="material-symbols-rounded opacity-5 me-2">medical_services</i>
                        <span class="sidenav-normal">Consultas Médicas</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link text-dark mobile-submenu-item" href="{{ route('appointments.index') }}">
                        <i class="material-symbols-rounded opacity-5 me-2">calendar_month</i>
                        <span class="sidenav-normal">Gestión de Citas</span>
                    </a>
                </li>
            </ul>
        </div>
    </li>

    <!-- Mantenimiento -->
    <li class="nav-item">
        <a data-bs-toggle="collapse" href="#menuMantenimiento" class="nav-link text-dark mobile-menu-item"
            aria-controls="menuMantenimiento" role="button" aria-expanded="false">
            <i class="material-symbols-rounded opacity-5">engineering</i>
            <span class="nav-link-text ms-1 ps-1">Mantenimiento</span>
            <i class="material-symbols-rounded ms-auto collapse-arrow">expand_more</i>
        </a>
        <div class="collapse" id="menuMantenimiento">
            <ul class="nav mobile-submenu">
                <!-- Gestión Médica Submenu -->
                <li class="nav-item">
                    <a class="nav-link text-dark mobile-submenu-item" data-bs-toggle="collapse" href="#menuGestionMedica" role="button"
                        aria-expanded="false" aria-controls="menuGestionMedica">
                        <i class="material-symbols-rounded opacity-5 me-2">local_hospital</i>
                        <span class="sidenav-normal">Gestión Médica</span>
                        <i class="material-symbols-rounded ms-auto collapse-arrow">expand_more</i>
                    </a>
                    <div class="collapse" id="menuGestionMedica">
                        <ul class="nav nav-sm flex-column mobile-submenu">
                <li class="nav-item">
                    <a class="nav-link text-dark mobile-submenu-item" href="{{ url('/especialidades') }}">
                        <i class="material-symbols-rounded opacity-5 me-2">medical_services</i>
                        <span class="sidenav-normal">Especialidades</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link text-dark mobile-submenu-item" href="{{ route('doctors.index') }}">
                        <i class="material-symbols-rounded opacity-5 me-2">group</i>
                        <span class="sidenav-normal">Doctores</span>
                    </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link text-dark mobile-submenu-item" href="{{ route('schedule-types.index') }}">
                                    <i class="material-symbols-rounded opacity-5 me-2">schedule</i>
                                    <span class="sidenav-normal">Tipos de Horario</span>
                                </a>
                            </li>
                        </ul>
                    </div>
                </li>

                <!-- Catálogos Médicos Submenu -->
                <li class="nav-item">
                    <a class="nav-link text-dark mobile-submenu-item" data-bs-toggle="collapse" href="#menuCatalogosMedicos" role="button"
                        aria-expanded="false" aria-controls="menuCatalogosMedicos">
                        <i class="material-symbols-rounded opacity-5 me-2">category</i>
                        <span class="sidenav-normal">Catálogos Médicos</span>
                        <i class="material-symbols-rounded ms-auto collapse-arrow">expand_more</i>
                    </a>
                    <div class="collapse" id="menuCatalogosMedicos">
                        <ul class="nav nav-sm flex-column mobile-submenu">
                            <li class="nav-item">
                                <a class="nav-link text-dark mobile-submenu-item" href="{{ route('sexes.index') }}">
                                    <i class="material-symbols-rounded opacity-5 me-2">wc</i>
                                    <span class="sidenav-normal">Sexos</span>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link text-dark mobile-submenu-item" href="{{ route('civil-statuses.index') }}">
                                    <i class="material-symbols-rounded opacity-5 me-2">favorite</i>
                                    <span class="sidenav-normal">Estados Civiles</span>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link text-dark mobile-submenu-item" href="{{ route('linguistic-communities.index') }}">
                                    <i class="material-symbols-rounded opacity-5 me-2">translate</i>
                                    <span class="sidenav-normal">Comunidades Lingüísticas</span>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link text-dark mobile-submenu-item" href="{{ route('ethnicities.index') }}">
                                    <i class="material-symbols-rounded opacity-5 me-2">diversity_3</i>
                                    <span class="sidenav-normal">Etnias</span>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link text-dark mobile-submenu-item" href="{{ route('disabilities.index') }}">
                                    <i class="material-symbols-rounded opacity-5 me-2">accessibility</i>
                                    <span class="sidenav-normal">Discapacidades</span>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link text-dark mobile-submenu-item" href="{{ route('allergies.index') }}">
                                    <i class="material-symbols-rounded opacity-5 me-2">warning</i>
                                    <span class="sidenav-normal">Alergias</span>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link text-dark mobile-submenu-item" href="{{ route('control-types.index') }}">
                                    <i class="material-symbols-rounded opacity-5 me-2">assignment</i>
                                    <span class="sidenav-normal">Tipos de Control</span>
                                </a>
                            </li>
                        </ul>
                    </div>
                </li>

                <!-- Estudios y Medicamentos Submenu -->
                <li class="nav-item">
                    <a class="nav-link text-dark mobile-submenu-item" data-bs-toggle="collapse" href="#menuEstudiosMedicamentos" role="button"
                        aria-expanded="false" aria-controls="menuEstudiosMedicamentos">
                        <i class="material-symbols-rounded opacity-5 me-2">science</i>
                        <span class="sidenav-normal">Estudios y Medicamentos</span>
                        <i class="material-symbols-rounded ms-auto collapse-arrow">expand_more</i>
                    </a>
                    <div class="collapse" id="menuEstudiosMedicamentos">
                        <ul class="nav nav-sm flex-column mobile-submenu">
                            <li class="nav-item">
                                <a class="nav-link text-dark mobile-submenu-item" href="{{ route('laboratory-tests.index') }}">
                                    <i class="material-symbols-rounded opacity-5 me-2">biotech</i>
                                    <span class="sidenav-normal">Pruebas de Laboratorio</span>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link text-dark mobile-submenu-item" href="{{ route('exams.index') }}">
                                    <i class="material-symbols-rounded opacity-5 me-2">monitor_heart</i>
                                    <span class="sidenav-normal">Exámenes</span>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link text-dark mobile-submenu-item" href="{{ route('medications.index') }}">
                                    <i class="material-symbols-rounded opacity-5 me-2">medication</i>
                                    <span class="sidenav-normal">Medicamentos</span>
                                </a>
                            </li>
                        </ul>
                    </div>
                </li>

                <!-- Ubicaciones Submenu -->
                <li class="nav-item">
                    <a class="nav-link text-dark mobile-submenu-item" data-bs-toggle="collapse" href="#menuUbicaciones" role="button"
                        aria-expanded="false" aria-controls="menuUbicaciones">
                        <i class="material-symbols-rounded opacity-5 me-2">public</i>
                        <span class="sidenav-normal">Ubicaciones</span>
                        <i class="material-symbols-rounded ms-auto collapse-arrow">expand_more</i>
                    </a>
                    <div class="collapse" id="menuUbicaciones">
                        <ul class="nav nav-sm flex-column mobile-submenu">
                            <li class="nav-item">
                                <a class="nav-link text-dark mobile-submenu-item" href="{{ url('/paises') }}">
                                    <i class="material-symbols-rounded opacity-5 me-2">flag</i>
                                    <span class="sidenav-normal">Paises</span>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link text-dark mobile-submenu-item" href="{{ url('/departamentos') }}">
                                    <i class="material-symbols-rounded opacity-5 me-2">map</i>
                                    <span class="sidenav-normal">Departamentos</span>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link text-dark mobile-submenu-item" href="{{ url('/municipios') }}">
                                    <i class="material-symbols-rounded opacity-5 me-2">location_city</i>
                                    <span class="sidenav-normal">Municipios</span>
                                </a>
                            </li>
                        </ul>
                    </div>
                </li>
            </ul>
        </div>
    </li>

    <!-- Administración del Sistema (Solo para administradores) -->
    @if(auth()->check() && auth()->user()->isAdmin())
    <li class="nav-item">
        <a data-bs-toggle="collapse" href="#menuAdministracion" class="nav-link text-dark mobile-menu-item"
            aria-controls="menuAdministracion" role="button" aria-expanded="false">
            <i class="material-symbols-rounded opacity-5">admin_panel_settings</i>
            <span class="nav-link-text ms-1 ps-1">Administración</span>
            <i class="material-symbols-rounded ms-auto collapse-arrow">expand_more</i>
        </a>
        <div class="collapse" id="menuAdministracion">
            <ul class="nav mobile-submenu">
                <li class="nav-item">
                    <a class="nav-link text-dark mobile-submenu-item" href="{{ route('usuarios.index') }}">
                        <i class="material-symbols-rounded opacity-5 me-2">group</i>
                        <span class="sidenav-normal">Gestión de Usuarios</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link text-dark mobile-submenu-item" href="{{ route('roles.index') }}">
                        <i class="material-symbols-rounded opacity-5 me-2">security</i>
                        <span class="sidenav-normal">Roles de Usuario</span>
                    </a>
                </li>
            </ul>
        </div>
    </li>
    @endif

    <!-- Reportes SIGSA 3H -->
    <li class="nav-item">
        <a data-bs-toggle="collapse" href="#menuReportes" class="nav-link text-dark mobile-menu-item"
            aria-controls="menuReportes" role="button" aria-expanded="false">
            <i class="material-symbols-rounded opacity-5">assessment</i>
            <span class="nav-link-text ms-1 ps-1">Reportes SIGSA 3H</span>
            <i class="material-symbols-rounded ms-auto collapse-arrow">expand_more</i>
        </a>
        <div class="collapse" id="menuReportes">
            <ul class="nav mobile-submenu">
                <li class="nav-item">
                    <a class="nav-link text-dark mobile-submenu-item" href="{{ route('reports.index') }}">
                        <i class="material-symbols-rounded opacity-5 me-2">bar_chart</i>
                        <span class="sidenav-normal">Generar Reportes</span>
                    </a>
                </li>
            </ul>
        </div>
    </li>
</ul>
