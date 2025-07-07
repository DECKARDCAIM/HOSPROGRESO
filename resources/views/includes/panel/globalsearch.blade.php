<div id="globalSearchModal" class="search-modal" style="display: none;">
    <div class="search-modal-overlay"></div>
    <div class="search-modal-content">
        <div class="search-header">
            <div class="search-icon">
                <i class="fas fa-search"></i>
            </div>
            <input type="text" id="globalSearchInput" placeholder="Buscar en todo el sistema..." autocomplete="off">
            <button id="closeGlobalSearch" class="search-close">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <div class="search-results" id="globalSearchResults">
            <div class="search-suggestions" id="searchSuggestions">
                <div class="suggestion-group">
                    <h6>Acciones Rápidas</h6>
                    <a href="{{ route('clinical-records.create') }}" class="suggestion-item"
                        data-search="nuevo expediente crear paciente registrar">
                        <i class="fas fa-plus"></i>
                        <span>Nuevo Expediente</span>
                    </a>
                    <a href="{{ route('appointments.create') }}" class="suggestion-item"
                        data-search="nueva cita agendar programar">
                        <i class="fas fa-calendar-plus"></i>
                        <span>Nueva Cita</span>
                    </a>
                    <a href="{{ route('clinical-records.index') }}" class="suggestion-item"
                        data-search="nueva consulta expedientes pacientes">
                        <i class="fas fa-user-plus"></i>
                        <span>Nueva Consulta</span>
                    </a>
                    <a href="{{ route('reports.index') }}" class="suggestion-item"
                        data-search="reportes sigsa generar estadisticas">
                        <i class="fas fa-chart-bar"></i>
                        <span>Generar Reportes SIGSA</span>
                    </a>
                </div>

                <div class="suggestion-group">
                    <h6>Módulos Principales</h6>
                    <a href="{{ route('clinical-records.index') }}" class="suggestion-item"
                        data-search="expedientes clinicos pacientes registros">
                        <i class="fas fa-folder-open"></i>
                        <span>Expedientes Clínicos</span>
                    </a>
                    <a href="{{ route('appointments.index') }}" class="suggestion-item"
                        data-search="citas gestion agendar programar">
                        <i class="fas fa-calendar"></i>
                        <span>Gestión de Citas</span>
                    </a>
                    <a href="{{ route('medical-consultations.index') }}" class="suggestion-item"
                        data-search="consultas medicas emergencia externa">
                        <i class="fas fa-stethoscope"></i>
                        <span>Consultas Médicas</span>
                    </a>
                </div>

                <div class="suggestion-group">
                    <h6>Gestión Médica</h6>
                    <a href="{{ url('/especialidades') }}" class="suggestion-item"
                        data-search="especialidades medicas doctores">
                        <i class="fas fa-briefcase-medical"></i>
                        <span>Especialidades</span>
                    </a>
                    <a href="{{ route('doctors.index') }}" class="suggestion-item"
                        data-search="doctores medicos profesionales">
                        <i class="fas fa-user-md"></i>
                        <span>Médicos</span>
                    </a>
                    <a href="{{ route('schedule-types.index') }}" class="suggestion-item"
                        data-search="horarios tipos schedule turnos">
                        <i class="fas fa-clock"></i>
                        <span>Tipos de Horario</span>
                    </a>
                </div>

                <div class="suggestion-group">
                    <h6>Catálogos Médicos</h6>
                    <a href="{{ route('sexes.index') }}" class="suggestion-item"
                        data-search="sexos genero masculino femenino">
                        <i class="fas fa-venus-mars"></i>
                        <span>Sexos</span>
                    </a>
                    <a href="{{ route('civil-statuses.index') }}" class="suggestion-item"
                        data-search="estado civil soltero casado">
                        <i class="fas fa-heart"></i>
                        <span>Estados Civiles</span>
                    </a>
                    <a href="{{ route('linguistic-communities.index') }}" class="suggestion-item"
                        data-search="comunidades linguisticas idiomas">
                        <i class="fas fa-language"></i>
                        <span>Comunidades Lingüísticas</span>
                    </a>
                    <a href="{{ route('ethnicities.index') }}" class="suggestion-item"
                        data-search="etnias raza grupo etnico">
                        <i class="fas fa-users"></i>
                        <span>Etnias</span>
                    </a>
                    <a href="{{ route('disabilities.index') }}" class="suggestion-item"
                        data-search="discapacidades limitaciones fisica mental">
                        <i class="fas fa-wheelchair"></i>
                        <span>Discapacidades</span>
                    </a>
                    <a href="{{ route('allergies.index') }}" class="suggestion-item"
                        data-search="alergias reacciones medicamentos">
                        <i class="fas fa-exclamation-triangle"></i>
                        <span>Alergias</span>
                    </a>
                    <a href="{{ route('control-types.index') }}" class="suggestion-item"
                        data-search="tipos control sigsa clasificacion">
                        <i class="fas fa-list-check"></i>
                        <span>Tipos de Control</span>
                    </a>
                </div>

                <div class="suggestion-group">
                    <h6>Estudios y Medicamentos</h6>
                    <a href="{{ route('laboratory-tests.index') }}" class="suggestion-item"
                        data-search="laboratorio pruebas examenes sangre orina">
                        <i class="fas fa-vial"></i>
                        <span>Pruebas de Laboratorio</span>
                    </a>
                    <a href="{{ route('exams.index') }}" class="suggestion-item"
                        data-search="examenes estudios radiografia ecografia">
                        <i class="fas fa-x-ray"></i>
                        <span>Exámenes</span>
                    </a>
                    <a href="{{ route('medications.index') }}" class="suggestion-item"
                        data-search="medicamentos farmacos medicina pastillas">
                        <i class="fas fa-pills"></i>
                        <span>Medicamentos</span>
                    </a>
                </div>

                <div class="suggestion-group">
                    <h6>Ubicaciones</h6>
                    <a href="{{ url('/paises') }}" class="suggestion-item"
                        data-search="paises naciones territorios">
                        <i class="fas fa-globe"></i>
                        <span>Países</span>
                    </a>
                    <a href="{{ url('/departamentos') }}" class="suggestion-item"
                        data-search="departamentos regiones provincias">
                        <i class="fas fa-map"></i>
                        <span>Departamentos</span>
                    </a>
                    <a href="{{ url('/municipios') }}" class="suggestion-item"
                        data-search="municipios ciudades localidades">
                        <i class="fas fa-map-marker-alt"></i>
                        <span>Municipios</span>
                    </a>
                </div>

                @if (auth()->check() && auth()->user()->isAdmin())
                    <div class="suggestion-group">
                        <h6>Administración del Sistema</h6>
                        <a href="{{ route('usuarios.index') }}" class="suggestion-item"
                            data-search="usuarios gestion personal empleados">
                            <i class="fas fa-users-cog"></i>
                            <span>Gestión de Usuarios</span>
                        </a>
                        <a href="{{ route('roles.index') }}" class="suggestion-item"
                            data-search="roles permisos acceso seguridad">
                            <i class="fas fa-user-shield"></i>
                            <span>Roles de Usuario</span>
                        </a>
                    </div>
                @endif

                <div class="suggestion-group">
                    <h6>Reportes SIGSA 3H</h6>
                    <a href="{{ route('reports.index') }}" class="suggestion-item"
                        data-search="reportes sigsa generar estadisticas">
                        <i class="fas fa-chart-line"></i>
                        <span>Generar Reportes</span>
                    </a>
                </div>

                <div class="suggestion-group">
                    <h6>Mi Perfil</h6>
                    <a href="{{ route('profile.index') }}" class="suggestion-item"
                        data-search="perfil cuenta usuario configuracion">
                        <i class="fas fa-user-circle"></i>
                        <span>Mi Perfil</span>
                    </a>
                    <a href="{{ route('panel') }}" class="suggestion-item"
                        data-search="inicio dashboard panel principal">
                        <i class="fas fa-home"></i>
                        <span>Inicio / Dashboard</span>
                    </a>
                </div>
            </div>

            <div class="no-results" id="noResults" style="display: none;">
                <div class="no-results-content">
                    <i class="fas fa-search"></i>
                    <h6>No se encontraron resultados</h6>
                    <p>Intenta con otro término de búsqueda</p>
                </div>
            </div>
        </div>
    </div>
</div>