<div id="globalSearchModal" class="search-modal" style="display: none;">
    <div class="search-modal-overlay"></div>
    <div class="search-modal-content">
        <div class="search-header">
            <div class="search-icon">
                <i class="bi bi-search"></i>
            </div>
            <input type="text" id="globalSearchInput" placeholder="Buscar en todo el sistema..." autocomplete="off">
            <button id="closeGlobalSearch" class="search-close">
                <i class="bi bi-x"></i>
            </button>
        </div>
        <div class="search-results" id="globalSearchResults">
            <div class="search-suggestions" id="searchSuggestions">
                <div class="suggestion-group">
                    <h6>Acciones Rápidas</h6>
                    @if(auth()->user()->isAdmin() || Gate::any(['emergencia.expedientes.crear', 'consulta_externa.expedientes.crear']))
                    <a href="{{ route('clinical-records.create') }}" class="suggestion-item"
                        data-search="nuevo expediente crear paciente registrar">
                        <i class="bi bi-plus"></i>
                        <span>Nuevo Expediente</span>
                    </a>
                    @endif
                    @if(auth()->user()->isAdmin() || Gate::any(['emergencia.citas.crear', 'consulta_externa.citas.crear']))
                    <a href="{{ route('appointments.create') }}" class="suggestion-item"
                        data-search="nueva cita agendar programar">
                        <i class="bi bi-calendar-plus"></i>
                        <span>Nueva Cita</span>
                    </a>
                    @endif
                    @if(auth()->user()->isAdmin() || Gate::any(['emergencia.consultas.crear', 'consulta_externa.consultas.crear']))
                    <a href="{{ route('clinical-records.index') }}" class="suggestion-item"
                        data-search="nueva consulta expedientes pacientes">
                        <i class="bi bi-person-plus"></i>
                        <span>Nueva Consulta</span>
                    </a>
                    @endif
                    @if(auth()->user()->isAdmin() || Gate::any(['reportes.ver', 'reportes.generar']))
                    <a href="{{ route('reports.index') }}" class="suggestion-item"
                        data-search="reportes sigsa generar estadisticas">
                        <i class="bi bi-bar-chart"></i>
                        <span>Generar Reportes SIGSA</span>
                    </a>
                    @endif
                </div>

                <div class="suggestion-group">
                    <h6>Módulos Principales</h6>
                    @if(auth()->user()->isAdmin() || Gate::any(['emergencia.expedientes.ver', 'consulta_externa.expedientes.ver']))
                    <a href="{{ route('clinical-records.index') }}" class="suggestion-item"
                        data-search="expedientes clinicos pacientes registros">
                        <i class="bi bi-folder2-open"></i>
                        <span>Expedientes Clínicos</span>
                    </a>
                    @endif
                    @if(auth()->user()->isAdmin() || Gate::any(['emergencia.citas.ver', 'consulta_externa.citas.ver']))
                    <a href="{{ route('appointments.index') }}" class="suggestion-item"
                        data-search="citas gestion agendar programar">
                        <i class="bi bi-calendar-date"></i>
                        <span>Gestión de Citas</span>
                    </a>
                    @endif
                    @if(auth()->user()->isAdmin() || Gate::any(['emergencia.consultas.ver', 'consulta_externa.consultas.ver']))
                    <a href="{{ route('medical-consultations.index') }}" class="suggestion-item"
                        data-search="consultas medicas emergencia externa">
                        <i class="bi bi-heart-pulse"></i>
                        <span>Consultas Médicas</span>
                    </a>
                    @endif
                </div>

                <div class="suggestion-group">
                    <h6>Gestión Médica</h6>
                    @if(auth()->user()->isAdmin() || Gate::check('especialidades.ver'))
                    <a href="{{ url('/especialidades') }}" class="suggestion-item"
                        data-search="especialidades medicas doctores">
                        <i class="bi bi-briefcase"></i>
                        <span>Especialidades</span>
                    </a>
                    @endif
                    @if(auth()->user()->isAdmin() || Gate::check('doctores.ver'))
                    <a href="{{ route('doctors.index') }}" class="suggestion-item"
                        data-search="doctores medicos profesionales">
                        <i class="bi bi-person-badge"></i>
                        <span>Médicos</span>
                    </a>
                    @endif
                    @if(auth()->user()->isAdmin() || Gate::check('tipos_horario.ver'))
                    <a href="{{ route('schedule-types.index') }}" class="suggestion-item"
                        data-search="horarios tipos schedule turnos">
                        <i class="bi bi-clock"></i>
                        <span>Tipos de Horario</span>
                    </a>
                    @endif
                    @if(auth()->user()->isAdmin() || Gate::check('doctores.sustituciones.ver'))
                    <a href="{{ route('doctor-substitutions.index') }}" class="suggestion-item"
                        data-search="sustituciones doctores reemplazos">
                        <i class="bi bi-arrow-repeat"></i>
                        <span>Sustituciones</span>
                    </a>
                    @endif
                    @if(auth()->user()->isAdmin() || Gate::check('dias_festivos.ver'))
                    <a href="{{ route('holidays.index') }}" class="suggestion-item"
                        data-search="dias festivos feriados calendario">
                        <i class="bi bi-calendar-event"></i>
                        <span>Días Festivos</span>
                    </a>
                    @endif
                </div>

                <div class="suggestion-group">
                    <h6>Catálogos Médicos</h6>
                    @if(auth()->user()->isAdmin() || Gate::check('sexos.ver'))
                    <a href="{{ route('sexes.index') }}" class="suggestion-item"
                        data-search="sexos genero masculino femenino">
                        <i class="bi bi-gender-ambiguous"></i>
                        <span>Sexos</span>
                    </a>
                    @endif
                    @if(auth()->user()->isAdmin() || Gate::check('estados_civiles.ver'))
                    <a href="{{ route('civil-statuses.index') }}" class="suggestion-item"
                        data-search="estado civil soltero casado">
                        <i class="bi bi-heart"></i>
                        <span>Estados Civiles</span>
                    </a>
                    @endif
                    @if(auth()->user()->isAdmin() || Gate::check('comunidades_linguisticas.ver'))
                    <a href="{{ route('linguistic-communities.index') }}" class="suggestion-item"
                        data-search="comunidades linguisticas idiomas">
                        <i class="bi bi-translate"></i>
                        <span>Comunidades Lingüísticas</span>
                    </a>
                    @endif
                    @if(auth()->user()->isAdmin() || Gate::check('etnias.ver'))
                    <a href="{{ route('ethnicities.index') }}" class="suggestion-item"
                        data-search="etnias raza grupo etnico">
                        <i class="bi bi-people"></i>
                        <span>Etnias</span>
                    </a>
                    @endif
                    @if(auth()->user()->isAdmin() || Gate::check('discapacidades.ver'))
                    <a href="{{ route('disabilities.index') }}" class="suggestion-item"
                        data-search="discapacidades limitaciones fisica mental">
                        <i class="bi bi-person-wheelchair"></i>
                        <span>Discapacidades</span>
                    </a>
                    @endif
                    @if(auth()->user()->isAdmin() || Gate::check('alergias.ver'))
                    <a href="{{ route('allergies.index') }}" class="suggestion-item"
                        data-search="alergias reacciones medicamentos">
                        <i class="bi bi-exclamation-triangle"></i>
                        <span>Alergias</span>
                    </a>
                    @endif
                    @if(auth()->user()->isAdmin() || Gate::check('estados_paciente.ver'))
                    <a href="{{ route('patient-statuses.index') }}" class="suggestion-item"
                        data-search="estado paciente gestion status">
                        <i class="bi bi-person-check"></i>
                        <span>Gestión de Estado de Paciente</span>
                    </a>
                    @endif
                    @if(auth()->user()->isAdmin() || Gate::check('relaciones_acompanantes.ver'))
                    <a href="{{ route('companion-relationships.index') }}" class="suggestion-item"
                        data-search="acompañante relaciones familiar">
                        <i class="bi bi-people-fill"></i>
                        <span>Acompañantes</span>
                    </a>
                    @endif
                    @if(auth()->user()->isAdmin() || Gate::check('metodos_anticonceptivos.ver'))
                    <a href="{{ route('contraceptive-methods.index') }}" class="suggestion-item"
                        data-search="metodos anticonceptivos planificacion familiar">
                        <i class="bi bi-shield-check"></i>
                        <span>Métodos Anticonceptivos</span>
                    </a>
                    @endif
                </div>

                <div class="suggestion-group">
                    <h6>Estudios y Medicamentos</h6>
                    @if(auth()->user()->isAdmin() || Gate::check('pruebas_laboratorio.ver'))
                    <a href="{{ route('laboratory-tests.index') }}" class="suggestion-item"
                        data-search="laboratorio pruebas examenes sangre orina">
                        <i class="bi bi-droplet"></i>
                        <span>Pruebas de Laboratorio</span>
                    </a>
                    @endif
                    @if(auth()->user()->isAdmin() || Gate::check('examenes.ver'))
                    <a href="{{ route('exams.index') }}" class="suggestion-item"
                        data-search="examenes estudios radiografia ecografia">
                        <i class="bi bi-lightning"></i>
                        <span>Exámenes</span>
                    </a>
                    @endif
                    @if(auth()->user()->isAdmin() || Gate::check('medicamentos.ver'))
                    <a href="{{ route('medications.index') }}" class="suggestion-item"
                        data-search="medicamentos farmacos medicina pastillas">
                        <i class="bi bi-circle-square"></i>
                        <span>Medicamentos</span>
                    </a>
                    @endif
                </div>

                <div class="suggestion-group">
                    <h6>Ubicaciones</h6>
                    @if(auth()->user()->isAdmin() || Gate::check('paises.ver'))
                    <a href="{{ url('/paises') }}" class="suggestion-item"
                        data-search="paises naciones territorios">
                        <i class="bi bi-globe"></i>
                        <span>Países</span>
                    </a>
                    @endif
                    @if(auth()->user()->isAdmin() || Gate::check('departamentos.ver'))
                    <a href="{{ url('/departamentos') }}" class="suggestion-item"
                        data-search="departamentos regiones provincias">
                        <i class="bi bi-geo-alt"></i>
                        <span>Departamentos</span>
                    </a>
                    @endif
                    @if(auth()->user()->isAdmin() || Gate::check('municipios.ver'))
                    <a href="{{ url('/municipios') }}" class="suggestion-item"
                        data-search="municipios ciudades localidades">
                        <i class="bi bi-building"></i>
                        <span>Municipios</span>
                    </a>
                    @endif
                </div>

                @if(auth()->check() && (auth()->user()->isAdmin() || Gate::any(['administracion.acceso', 'usuarios.ver', 'roles.ver'])))
                    <div class="suggestion-group">
                        <h6>Administración del Sistema</h6>
                        @if(auth()->user()->isAdmin() || Gate::check('usuarios.ver'))
                        <a href="{{ route('usuarios.index') }}" class="suggestion-item"
                            data-search="usuarios gestion personal empleados">
                            <i class="bi bi-people-fill"></i>
                            <span>Gestión de Usuarios</span>
                        </a>
                        @endif
                        @if(auth()->user()->isAdmin() || Gate::check('roles.ver'))
                        <a href="{{ route('roles.index') }}" class="suggestion-item"
                            data-search="roles permisos acceso seguridad">
                            <i class="bi bi-shield-lock"></i>
                            <span>Roles de Usuario</span>
                        </a>
                        @endif
                        @if(auth()->user()->isAdmin() || Gate::check('import.acceso'))
                        <a href="{{ route('import.index') }}" class="suggestion-item"
                            data-search="base datos importar exportar">
                            <i class="bi bi-database"></i>
                            <span>Base de Datos</span>
                        </a>
                        @endif
                    </div>
                @endif

                @if(auth()->check() && (auth()->user()->isAdmin() || Gate::any(['reportes.ver', 'reportes.generar'])))
                <div class="suggestion-group">
                    <h6>Reportes SIGSA 3H</h6>
                    <a href="{{ route('reports.index') }}" class="suggestion-item"
                        data-search="reportes sigsa generar estadisticas">
                        <i class="bi bi-graph-up-arrow"></i>
                        <span>Generar Reportes</span>
                    </a>
                </div>
                @endif

                <div class="suggestion-group">
                    <h6>Mi Perfil</h6>
                    <a href="{{ route('profile.index') }}" class="suggestion-item"
                        data-search="perfil cuenta usuario configuracion">
                        <i class="bi bi-person-circle"></i>
                        <span>Mi Perfil</span>
                    </a>
                    <a href="{{ route('panel') }}" class="suggestion-item"
                        data-search="inicio dashboard panel principal">
                        <i class="bi bi-house"></i>
                        <span>Inicio / Dashboard</span>
                    </a>
                </div>
            </div>

            <div class="no-results" id="noResults" style="display: none;">
                <div class="no-results-content">
                    <i class="bi bi-search"></i>
                    <h6>No se encontraron resultados</h6>
                    <p>Intenta con otro término de búsqueda</p>
                </div>
            </div>
        </div>
    </div>
</div>