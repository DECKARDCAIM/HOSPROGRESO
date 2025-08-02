<hr class="horizontal dark" />
<h6 class="ps-3 ms-2 text-uppercase text-xs font-weight-bolder text-dark">Panel de Controles</h6>

<ul class="navbar-nav">
    <!-- Emergencia -->
    <li class="nav-item">
        <a data-bs-toggle="collapse" href="#menuEmergencia" class="nav-link text-dark mobile-menu-item"
            aria-controls="menuEmergencia" role="button" aria-expanded="false">
            <i class="bi bi-hospital opacity-5"></i>
            <span class="nav-link-text ms-1 ps-1">Emergencia</span>
        </a>
        <div class="collapse" id="menuEmergencia">
            <ul class="nav mobile-submenu">
                <li class="nav-item">
                    <a class="nav-link text-dark mobile-submenu-item" href="{{ route('clinical-records.index') }}">
                        <i class="bi bi-folder2-open opacity-5 me-2"></i>
                        <span class="sidenav-normal">Expedientes Clínicos</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link text-dark mobile-submenu-item" href="{{ route('medical-consultations.index') }}">
                        <i class="bi bi-heart-pulse opacity-5 me-2"></i>
                        <span class="sidenav-normal">Consultas Médicas</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link text-dark mobile-submenu-item" href="{{ route('appointments.index') }}">
                        <i class="bi bi-calendar-date opacity-5 me-2"></i>
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
            <i class="bi bi-prescription2 opacity-5"></i>
            <span class="nav-link-text ms-1 ps-1">Consulta Externa</span>
        </a>
        <div class="collapse" id="menuConsulta">
            <ul class="nav mobile-submenu">
                <li class="nav-item">
                    <a class="nav-link text-dark mobile-submenu-item" href="{{ route('clinical-records.index') }}">
                        <i class="bi bi-folder2-open opacity-5 me-2"></i>
                        <span class="sidenav-normal">Expedientes Clínicos</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link text-dark mobile-submenu-item" href="{{ route('medical-consultations.index') }}">
                        <i class="bi bi-heart-pulse opacity-5 me-2"></i>
                        <span class="sidenav-normal">Consultas Médicas</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link text-dark mobile-submenu-item" href="{{ route('appointments.index') }}">
                        <i class="bi bi-calendar-date opacity-5 me-2"></i>
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
            <i class="bi bi-gear opacity-5"></i>
            <span class="nav-link-text ms-1 ps-1">Mantenimiento</span>
        </a>
        <div class="collapse" id="menuMantenimiento">
            <ul class="nav mobile-submenu">
                <!-- Gestión Médica Submenu -->
                <li class="nav-item">
                    <a class="nav-link text-dark mobile-submenu-item" data-bs-toggle="collapse" href="#menuGestionMedica" role="button"
                        aria-expanded="false" aria-controls="menuGestionMedica">
                        <i class="bi bi-hospital opacity-5 me-2"></i>
                        <span class="sidenav-normal">Gestión Médica</span>
                    </a>
                    <div class="collapse" id="menuGestionMedica">
                        <ul class="nav nav-sm flex-column mobile-submenu">
                <li class="nav-item">
                    <a class="nav-link text-dark mobile-submenu-item" href="{{ url('/especialidades') }}">
                        <i class="bi bi-heart-pulse opacity-5 me-2"></i>
                        <span class="sidenav-normal">Especialidades</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link text-dark mobile-submenu-item" href="{{ route('doctors.index') }}">
                        <i class="bi bi-people opacity-5 me-2"></i>
                        <span class="sidenav-normal">Doctores</span>
                    </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link text-dark mobile-submenu-item" href="{{ route('schedule-types.index') }}">
                                    <i class="bi bi-clock opacity-5 me-2"></i>
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
                        <i class="bi bi-tags opacity-5 me-2"></i>
                        <span class="sidenav-normal">Catálogos Médicos</span>
                    </a>
                    <div class="collapse" id="menuCatalogosMedicos">
                        <ul class="nav nav-sm flex-column mobile-submenu">
                            <li class="nav-item">
                                <a class="nav-link text-dark mobile-submenu-item" href="{{ route('sexes.index') }}">
                                    <i class="bi bi-gender-ambiguous opacity-5 me-2"></i>
                                    <span class="sidenav-normal">Sexos</span>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link text-dark mobile-submenu-item" href="{{ route('civil-statuses.index') }}">
                                    <i class="bi bi-heart-pulse opacity-5 me-2"></i>
                                    <span class="sidenav-normal">Estados Civiles</span>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link text-dark mobile-submenu-item" href="{{ route('linguistic-communities.index') }}">
                                    <i class="bi bi-translate opacity-5 me-2"></i>
                                    <span class="sidenav-normal">Comunidades Lingüísticas</span>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link text-dark mobile-submenu-item" href="{{ route('ethnicities.index') }}">
                                    <i class="bi bi-people-fill opacity-5 me-2"></i>
                                    <span class="sidenav-normal">Etnias</span>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link text-dark mobile-submenu-item" href="{{ route('disabilities.index') }}">
                                    <i class="bi bi-hand-index-thumb opacity-5 me-2"></i>
                                    <span class="sidenav-normal">Discapacidades</span>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link text-dark mobile-submenu-item" href="{{ route('allergies.index') }}">
                                    <i class="bi bi-exclamation-triangle opacity-5 me-2"></i>
                                    <span class="sidenav-normal">Alergias</span>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link text-dark mobile-submenu-item" href="{{ route('control-types.index') }}">
                                    <i class="bi bi-journal-text opacity-5 me-2"></i>
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
                        <i class="bi bi-heart-pulse opacity-5 me-2"></i>
                        <span class="sidenav-normal">Estudios y Medicamentos</span>
                    </a>
                    <div class="collapse" id="menuEstudiosMedicamentos">
                        <ul class="nav nav-sm flex-column mobile-submenu">
                            <li class="nav-item">
                                <a class="nav-link text-dark mobile-submenu-item" href="{{ route('laboratory-tests.index') }}">
                                    <i class="bi bi-droplet opacity-5 me-2"></i>
                                    <span class="sidenav-normal">Pruebas de Laboratorio</span>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link text-dark mobile-submenu-item" href="{{ route('exams.index') }}">
                                    <i class="bi bi-lightning opacity-5 me-2"></i>
                                    <span class="sidenav-normal">Exámenes</span>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link text-dark mobile-submenu-item" href="{{ route('medications.index') }}">
                                    <i class="bi bi-circle-square opacity-5 me-2"></i>
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
                        <i class="bi bi-geo-alt opacity-5 me-2"></i>
                        <span class="sidenav-normal">Ubicaciones</span>
                    </a>
                    <div class="collapse" id="menuUbicaciones">
                        <ul class="nav nav-sm flex-column mobile-submenu">
                            <li class="nav-item">
                                <a class="nav-link text-dark mobile-submenu-item" href="{{ url('/paises') }}">
                                    <i class="bi bi-flag opacity-5 me-2"></i>
                                    <span class="sidenav-normal">Paises</span>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link text-dark mobile-submenu-item" href="{{ url('/departamentos') }}">
                                    <i class="bi bi-map opacity-5 me-2"></i>
                                    <span class="sidenav-normal">Departamentos</span>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link text-dark mobile-submenu-item" href="{{ url('/municipios') }}">
                                    <i class="bi bi-geo-alt opacity-5 me-2"></i>
                                    <span class="sidenav-normal">Municipios</span>
                                </a>
                            </li>
                        </ul>
                    </div>
                </li>
            </ul>
        </div>
    </li>

    <!-- Archivo Clínico -->
    <li class="nav-item">
        <a data-bs-toggle="collapse" href="#menuArchivo" class="nav-link text-dark mobile-menu-item"
            aria-controls="menuArchivo" role="button" aria-expanded="false">
            <i class="bi bi-archive opacity-5"></i>
            <span class="nav-link-text ms-1 ps-1">Archivo Clínico</span>
        </a>
        <div class="collapse" id="menuArchivo">
            <ul class="nav mobile-submenu">
                <li class="nav-item">
                    <a class="nav-link text-dark mobile-submenu-item" href="{{ route('clinical-file.index') }}">
                        <i class="bi bi-file-medical opacity-5 me-2"></i>
                        <span class="sidenav-normal">Expedientes Recientes</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link text-dark mobile-submenu-item" href="{{ route('clinical-file.archived') }}">
                        <i class="bi bi-archive-fill opacity-5 me-2"></i>
                        <span class="sidenav-normal">Expedientes Archivados</span>
                    </a>
                </li>
            </ul>
        </div>
    </li>

    <!-- Administración del Sistema (Solo para administradores) -->
    @if(auth()->check() && auth()->user()->isAdmin())
    <li class="nav-item">
        <a data-bs-toggle="collapse" href="#menuAdministracion" class="nav-link text-dark mobile-menu-item"
            aria-controls="menuAdministracion" role="button" aria-expanded="false">
            <i class="bi bi-gear opacity-5"></i>
            <span class="nav-link-text ms-1 ps-1">Administración</span>
        </a>
        <div class="collapse" id="menuAdministracion">
            <ul class="nav mobile-submenu">
                <li class="nav-item">
                    <a class="nav-link text-dark mobile-submenu-item" href="{{ route('usuarios.index') }}">
                        <i class="bi bi-people opacity-5 me-2"></i>
                        <span class="sidenav-normal">Gestión de Usuarios</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link text-dark mobile-submenu-item" href="{{ route('roles.index') }}">
                        <i class="bi bi-shield-lock opacity-5 me-2"></i>
                        <span class="sidenav-normal">Roles de Usuario</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link text-dark mobile-submenu-item" href="{{ route('import.index') }}">
                        <i class="bi bi-database-fill opacity-5 me-2"></i>
                        <span class="sidenav-normal">Base de Datos</span>
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
            <i class="bi bi-graph-up-arrow opacity-5"></i>
            <span class="nav-link-text ms-1 ps-1">Reportes SIGSA 3H</span>
        </a>
        <div class="collapse" id="menuReportes">
            <ul class="nav mobile-submenu">
                <li class="nav-item">
                    <a class="nav-link text-dark mobile-submenu-item" href="{{ route('reports.index') }}">
                        <i class="bi bi-bar-chart opacity-5 me-2"></i>
                        <span class="sidenav-normal">Generar Reportes</span>
                    </a>
                </li>
            </ul>
        </div>
    </li>
</ul>
