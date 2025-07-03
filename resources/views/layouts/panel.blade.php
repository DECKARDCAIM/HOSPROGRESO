<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="icon" type="image/png" href="{{ asset('img/logo.png') }}">
    <link rel="apple-touch-icon" href="{{ asset('img/logo.png') }}">
    <title>{{ config('app.name') }} - @yield('title')</title>
    <link href="{{ asset('css/nucleo-icons.css') }}" rel="stylesheet" />
    <link href="{{ asset('css/nucleo-svg.css') }}" rel="stylesheet" />
    <link id="pagestyle" href="{{ asset('css/material-dashboard.css') }}" rel="stylesheet" />
    <link rel="stylesheet" type="text/css"
        href="https://fonts.googleapis.com/css?family=Inter:300,400,500,600,700,900" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.1/css/all.min.css"
        integrity="sha512-5Hs3dF2AEPkpNAR7UiOHba+lRSJNeM2ECkwxUIxC1Q/FLycGTbNapWXB4tP889k5T5Ju8fs4b1P5z/iB4nMfSQ=="
        crossorigin="anonymous" referrerpolicy="no-referrer">
    <link rel="stylesheet"
        href="https://fonts.googleapis.com/css2?family=Material+Symbols+Rounded:opsz,wght,FILL,GRAD@24,400,0,0" />
    <link href="{{ asset('css/notifications.css') }}" rel="stylesheet" />
    
    <!-- Estilos Búsqueda Global -->
    <style>
        /* ===== BÚSQUEDA GLOBAL ESTILO AZUL INFO ===== */
        .search-modal {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: 9999;
            display: flex;
            align-items: flex-start;
            justify-content: center;
            padding-top: 10vh;
        }

        .search-modal-overlay {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: linear-gradient(135deg, rgba(25, 118, 210, 0.1) 0%, rgba(0, 0, 0, 0.3) 100%);
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
        }

        .search-modal-content {
            position: relative;
            width: 90%;
            max-width: 600px;
            background: white;
            border-radius: 20px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
            overflow: hidden;
            transform: scale(0.9) translateY(-20px);
            opacity: 0;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .search-modal.show .search-modal-content {
            transform: scale(1) translateY(0);
            opacity: 1;
        }

        .search-header {
            background: linear-gradient(135deg, #1976d2 0%, #1565c0 100%);
            padding: 25px;
            display: flex;
            align-items: center;
            gap: 15px;
            position: relative;
        }

        .search-header::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><defs><pattern id="search-grid" width="10" height="10" patternUnits="userSpaceOnUse"><path d="M 10 0 L 0 0 0 10" fill="none" stroke="rgba(255,255,255,0.1)" stroke-width="0.5"/></pattern></defs><rect width="100" height="100" fill="url(%23search-grid)"/></svg>');
            opacity: 0.3;
        }

        .search-icon {
            width: 50px;
            height: 50px;
            background: rgba(255, 255, 255, 0.15);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
            z-index: 1;
        }

        .search-icon i {
            font-size: 20px;
            color: white;
        }

        #globalSearchInput {
            flex: 1;
            background: rgba(255, 255, 255, 0.15);
            border: 2px solid rgba(255, 255, 255, 0.3);
            border-radius: 12px;
            padding: 15px 20px;
            font-size: 1.1rem;
            color: white;
            font-weight: 500;
            position: relative;
            z-index: 1;
            transition: all 0.3s ease;
        }

        #globalSearchInput::placeholder {
            color: rgba(255, 255, 255, 0.8);
        }

        #globalSearchInput:focus {
            outline: none;
            background: rgba(255, 255, 255, 0.25);
            border-color: rgba(255, 255, 255, 0.6);
            box-shadow: 0 0 0 4px rgba(255, 255, 255, 0.2);
        }

        .search-close {
            width: 45px;
            height: 45px;
            background: rgba(255, 255, 255, 0.15);
            border: 2px solid rgba(255, 255, 255, 0.3);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: all 0.3s ease;
            position: relative;
            z-index: 1;
        }

        .search-close:hover {
            background: rgba(255, 255, 255, 0.25);
            transform: scale(1.1);
        }

        .search-close i {
            color: white;
            font-size: 16px;
        }

        .search-results {
            max-height: 400px;
            overflow-y: auto;
            padding: 25px;
        }

        .search-suggestions {
            display: flex;
            flex-direction: column;
            gap: 25px;
        }

        .suggestion-group h6 {
            font-size: 1rem;
            font-weight: 700;
            color: #1976d2;
            margin-bottom: 15px;
            padding-bottom: 8px;
            border-bottom: 2px solid #e3f2fd;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .suggestion-item {
            display: flex;
            align-items: center;
            gap: 15px;
            padding: 15px 20px;
            border-radius: 12px;
            background: linear-gradient(135deg, rgba(25, 118, 210, 0.05) 0%, rgba(25, 118, 210, 0.02) 100%);
            border: 2px solid transparent;
            text-decoration: none;
            color: #344767;
            font-weight: 500;
            margin-bottom: 8px;
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }

        .suggestion-item::before {
            content: '';
            position: absolute;
            left: 0;
            top: 0;
            bottom: 0;
            width: 4px;
            background: #1976d2;
            transform: scaleY(0);
            transition: transform 0.3s ease;
        }

        .suggestion-item:hover {
            background: linear-gradient(135deg, rgba(25, 118, 210, 0.15) 0%, rgba(25, 118, 210, 0.1) 100%);
            border-color: #e3f2fd;
            color: #1976d2;
            transform: translateX(5px);
            box-shadow: 0 5px 15px rgba(25, 118, 210, 0.2);
        }

        .suggestion-item:hover::before {
            transform: scaleY(1);
        }

        .suggestion-item i {
            width: 40px;
            height: 40px;
            background: linear-gradient(135deg, #1976d2 0%, #1565c0 100%);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 16px;
            flex-shrink: 0;
            transition: all 0.3s ease;
        }

        .suggestion-item:hover i {
            transform: scale(1.1);
            box-shadow: 0 4px 12px rgba(25, 118, 210, 0.4);
        }

        .suggestion-item span {
            font-size: 1rem;
            font-weight: 600;
        }

        /* Scrollbar personalizada */
        .search-results::-webkit-scrollbar {
            width: 8px;
        }

        .search-results::-webkit-scrollbar-track {
            background: #f1f1f1;
            border-radius: 4px;
        }

        .search-results::-webkit-scrollbar-thumb {
            background: #1976d2;
            border-radius: 4px;
        }

        .search-results::-webkit-scrollbar-thumb:hover {
            background: #1565c0;
        }

        /* Indicador de tecla TAB */
        .search-key-indicator {
            position: fixed;
            top: 20px;
            right: 20px;
            background: linear-gradient(135deg, #1976d2 0%, #1565c0 100%);
            color: white;
            padding: 8px 15px;
            border-radius: 20px;
            font-size: 0.85rem;
            font-weight: 600;
            z-index: 10000;
            display: none;
            align-items: center;
            gap: 8px;
            box-shadow: 0 4px 15px rgba(25, 118, 210, 0.3);
        }

        .search-key-indicator.show {
            display: flex;
            animation: slideInRight 0.3s ease;
        }

        @keyframes slideInRight {
            from { transform: translateX(100%); opacity: 0; }
            to { transform: translateX(0); opacity: 1; }
        }

                 /* Estado sin resultados */
         .no-results {
             display: flex;
             flex-direction: column;
             align-items: center;
             justify-content: center;
             padding: 60px 20px;
             text-align: center;
         }

         .no-results-content {
             background: linear-gradient(135deg, rgba(25, 118, 210, 0.05) 0%, rgba(25, 118, 210, 0.02) 100%);
             padding: 40px;
             border-radius: 20px;
             border: 2px dashed #e3f2fd;
         }

         .no-results-content i {
             font-size: 48px;
             color: #1976d2;
             margin-bottom: 20px;
             opacity: 0.6;
         }

         .no-results-content h6 {
             font-size: 1.3rem;
             font-weight: 700;
             color: #1976d2;
             margin-bottom: 10px;
         }

         .no-results-content p {
             color: #6c757d;
             font-size: 1rem;
             margin: 0;
         }

         /* Icono de búsqueda clickeable */
         .search-icon {
             cursor: pointer;
             transition: all 0.3s ease;
         }

         .search-icon:hover {
             background: rgba(255, 255, 255, 0.25);
             transform: scale(1.1);
         }

         /* Responsive */
         @media (max-width: 768px) {
             .search-modal-content {
                 width: 95%;
                 margin: 0 10px;
             }
             
             .search-header {
                 padding: 20px;
                 gap: 10px;
             }
             
             #globalSearchInput {
                 font-size: 1rem;
                 padding: 12px 15px;
             }
             
             .search-results {
                 padding: 20px;
                 max-height: 300px;
             }
             
             .suggestion-item {
                 padding: 12px 15px;
                 gap: 12px;
             }
             
             .suggestion-item i {
                 width: 35px;
                 height: 35px;
                 font-size: 14px;
             }

             .no-results-content {
                 padding: 30px 20px;
             }

             .no-results-content i {
                 font-size: 36px;
             }

             .no-results-content h6 {
                 font-size: 1.1rem;
             }
         }

         /* Estilo para elementos activos del menú */
         .active-menu-item {
             background: rgba(30, 136, 229, 0.1) !important;
             border-left: 4px solid #1e88e5 !important;
             border-radius: 0 8px 8px 0 !important;
             color: #1e88e5 !important;
             margin-left: 0 !important;
             padding-left: 12px !important;
         }

         .active-menu-item .nav-link-text,
         .active-menu-item i,
         .active-menu-item span {
             color: #1e88e5 !important;
             font-weight: 600 !important;
         }

         /* Efecto hover mejorado para elementos del menú */
         .sidenav .nav-link:hover:not(.active-menu-item) {
             background: rgba(30, 136, 229, 0.08) !important;
             border-left: 3px solid rgba(30, 136, 229, 0.5) !important;
             border-radius: 0 6px 6px 0 !important;
             margin-left: 0 !important;
             padding-left: 13px !important;
             transition: all 0.2s ease !important;
         }

         .sidenav .nav-link:hover:not(.active-menu-item) .nav-link-text,
         .sidenav .nav-link:hover:not(.active-menu-item) i,
         .sidenav .nav-link:hover:not(.active-menu-item) span {
             color: #1e88e5 !important;
         }

         /* Estilo para logo activo (HOSPROGRESO en inicio) */
         .active-logo {
             background: linear-gradient(135deg, #1e88e5 0%, #1976d2 100%) !important;
             border-radius: 8px !important;
             box-shadow: 0 4px 8px rgba(30, 136, 229, 0.3) !important;
             transform: scale(1.02) !important;
             transition: all 0.3s ease !important;
         }

         .active-logo span {
             color: white !important;
         }

         .active-logo img {
             /* Mantener el logo original, sin filtros */
             filter: none !important;
         }

         /* Hover para el logo cuando no está activo */
         .navbar-brand:hover:not(.active-logo) {
             background: rgba(30, 136, 229, 0.1) !important;
             border-radius: 8px !important;
             transform: scale(1.01) !important;
             transition: all 0.3s ease !important;
         }
    </style>
</head>

<body class="g-sidenav-show  bg-gray-100">

    <aside class="sidenav navbar navbar-vertical navbar-expand-xs border-radius-lg fixed-start ms-2 bg-white my-2"
        id="sidenav-main" style="width: 280px !important; min-width: 280px !important;">
        <div class="sidenav-header">
            <i class="fas fa-times p-3 cursor-pointer text-dark opacity-5 position-absolute end-0 top-0 d-none d-xl-none"
                aria-hidden="true" id="iconSidenav"></i>
            <a class="navbar-brand px-4 py-3 m-0" href="{{ url('/panel') }}">
                <img src="{{ asset('img/logo.png')}}" class="navbar-brand-img" width="26" height="26"
                    alt="main_logo">
                <span class="ms-1 text-sm text-dark">HOSPROGRESO</span>
            </a>
        </div>
        <hr class="horizontal dark mt-0 mb-2">
        <div class="collapse navbar-collapse  w-auto h-auto" id="sidenav-collapse-main">
            <ul class="navbar-nav">
                <li class="nav-item mb-2 mt-0">
                    <a data-bs-toggle="collapse" href="#ProfileNav" class="nav-link text-dark"
                        aria-controls="ProfileNav" role="button" aria-expanded="false">
                        <img src="{{ Auth::user()->profile_photo_url }}" class="avatar">
                        <span class="nav-link-text ms-2 ps-1">Mi Cuenta</span>
                    </a>
                    <div class="collapse" id="ProfileNav" style="">
                        <ul class="nav ">
                            <li class="nav-item">
                                <a class="nav-link text-dark" href="{{ route('profile.index') }}">
                                    <span class="sidenav-mini-icon"></span>
                                    <span class="sidenav-normal  ms-3  ps-1"> Mi Perfil </span>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link text-dark" href="{{ route('logout') }}" onclick="event.preventDefault(); document.getElementById('formlogout').submit();">
                                    <span class="sidenav-mini-icon"></span>
                                    <span class="sidenav-normal ms-3 ps-1"> Cerrar Sesión </span>
                                </a>
                                <form method="POST" action="{{ route('logout') }}" style="display: none;" id="formlogout">
                                    @csrf
                                </form>
                            </li>
                        </ul>
                    </div>
                </li>
            </ul>
        </div>

        <div class="collapse navbar-collapse  w-auto h-auto" id="sidenav-collapse-main">

            @include('includes.panel.menu')

        </div>
    </aside>

    <main class="main-content position-relative border-radius-lg d-flex flex-column" style="height: 100vh; margin-left: 296px !important;">
        <!-- Navbar -->
        <nav class="navbar navbar-main navbar-expand-lg px-0 mx-4 shadow-none border-radius-xl" id="navbarBlur" data-scroll="true">
            <div class="container-fluid py-1 px-3">
                @include('includes.panel.userOptions')
            </div>
        </nav>
        <!-- End Navbar -->

        <div class="container-fluid py-4 flex-grow-1" style="overflow-y: auto;">
            @yield('content')
        </div>
        
        @include('includes.panel.footer')
    </main>

    <!-- Búsqueda Global -->
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
                        <a href="{{ route('clinical-records.create') }}" class="suggestion-item" data-search="nuevo expediente crear paciente registrar">
                            <i class="fas fa-plus"></i>
                            <span>Nuevo Expediente</span>
                        </a>
                        <a href="{{ route('appointments.create') }}" class="suggestion-item" data-search="nueva cita agendar programar">
                            <i class="fas fa-calendar-plus"></i>
                            <span>Nueva Cita</span>
                        </a>
                        <a href="{{ route('clinical-records.index') }}" class="suggestion-item" data-search="nueva consulta expedientes pacientes">
                            <i class="fas fa-user-plus"></i>
                            <span>Nueva Consulta</span>
                        </a>
                        <a href="{{ route('reports.index') }}" class="suggestion-item" data-search="reportes sigsa generar estadisticas">
                            <i class="fas fa-chart-bar"></i>
                            <span>Generar Reportes SIGSA</span>
                        </a>
                    </div>
                    
                    <div class="suggestion-group">
                        <h6>Módulos Principales</h6>
                        <a href="{{ route('clinical-records.index') }}" class="suggestion-item" data-search="expedientes clinicos pacientes registros">
                            <i class="fas fa-folder-open"></i>
                            <span>Expedientes Clínicos</span>
                        </a>
                        <a href="{{ route('appointments.index') }}" class="suggestion-item" data-search="citas gestion agendar programar">
                            <i class="fas fa-calendar"></i>
                            <span>Gestión de Citas</span>
                        </a>
                        <a href="{{ route('medical-consultations.index') }}" class="suggestion-item" data-search="consultas medicas emergencia externa">
                            <i class="fas fa-stethoscope"></i>
                            <span>Consultas Médicas</span>
                        </a>
                    </div>

                    <div class="suggestion-group">
                        <h6>Gestión Médica</h6>
                        <a href="{{ url('/especialidades') }}" class="suggestion-item" data-search="especialidades medicas doctores">
                            <i class="fas fa-briefcase-medical"></i>
                            <span>Especialidades</span>
                        </a>
                        <a href="{{ route('doctors.index') }}" class="suggestion-item" data-search="doctores medicos profesionales">
                            <i class="fas fa-user-md"></i>
                            <span>Médicos</span>
                        </a>
                        <a href="{{ route('schedule-types.index') }}" class="suggestion-item" data-search="horarios tipos schedule turnos">
                            <i class="fas fa-clock"></i>
                            <span>Tipos de Horario</span>
                        </a>
                    </div>

                    <div class="suggestion-group">
                        <h6>Catálogos Médicos</h6>
                        <a href="{{ route('sexes.index') }}" class="suggestion-item" data-search="sexos genero masculino femenino">
                            <i class="fas fa-venus-mars"></i>
                            <span>Sexos</span>
                        </a>
                        <a href="{{ route('civil-statuses.index') }}" class="suggestion-item" data-search="estado civil soltero casado">
                            <i class="fas fa-heart"></i>
                            <span>Estados Civiles</span>
                        </a>
                        <a href="{{ route('linguistic-communities.index') }}" class="suggestion-item" data-search="comunidades linguisticas idiomas">
                            <i class="fas fa-language"></i>
                            <span>Comunidades Lingüísticas</span>
                        </a>
                        <a href="{{ route('ethnicities.index') }}" class="suggestion-item" data-search="etnias raza grupo etnico">
                            <i class="fas fa-users"></i>
                            <span>Etnias</span>
                        </a>
                        <a href="{{ route('disabilities.index') }}" class="suggestion-item" data-search="discapacidades limitaciones fisica mental">
                            <i class="fas fa-wheelchair"></i>
                            <span>Discapacidades</span>
                        </a>
                        <a href="{{ route('allergies.index') }}" class="suggestion-item" data-search="alergias reacciones medicamentos">
                            <i class="fas fa-exclamation-triangle"></i>
                            <span>Alergias</span>
                        </a>
                        <a href="{{ route('control-types.index') }}" class="suggestion-item" data-search="tipos control sigsa clasificacion">
                            <i class="fas fa-list-check"></i>
                            <span>Tipos de Control</span>
                        </a>
                    </div>

                    <div class="suggestion-group">
                        <h6>Estudios y Medicamentos</h6>
                        <a href="{{ route('laboratory-tests.index') }}" class="suggestion-item" data-search="laboratorio pruebas examenes sangre orina">
                            <i class="fas fa-vial"></i>
                            <span>Pruebas de Laboratorio</span>
                        </a>
                        <a href="{{ route('exams.index') }}" class="suggestion-item" data-search="examenes estudios radiografia ecografia">
                            <i class="fas fa-x-ray"></i>
                            <span>Exámenes</span>
                        </a>
                        <a href="{{ route('medications.index') }}" class="suggestion-item" data-search="medicamentos farmacos medicina pastillas">
                            <i class="fas fa-pills"></i>
                            <span>Medicamentos</span>
                        </a>
                    </div>

                    <div class="suggestion-group">
                        <h6>Ubicaciones</h6>
                        <a href="{{ url('/paises') }}" class="suggestion-item" data-search="paises naciones territorios">
                            <i class="fas fa-globe"></i>
                            <span>Países</span>
                        </a>
                        <a href="{{ url('/departamentos') }}" class="suggestion-item" data-search="departamentos regiones provincias">
                            <i class="fas fa-map"></i>
                            <span>Departamentos</span>
                        </a>
                        <a href="{{ url('/municipios') }}" class="suggestion-item" data-search="municipios ciudades localidades">
                            <i class="fas fa-map-marker-alt"></i>
                            <span>Municipios</span>
                        </a>
                    </div>

                    @if(auth()->check() && auth()->user()->isAdmin())
                    <div class="suggestion-group">
                        <h6>Administración del Sistema</h6>
                        <a href="{{ route('usuarios.index') }}" class="suggestion-item" data-search="usuarios gestion personal empleados">
                            <i class="fas fa-users-cog"></i>
                            <span>Gestión de Usuarios</span>
                        </a>
                        <a href="{{ route('roles.index') }}" class="suggestion-item" data-search="roles permisos acceso seguridad">
                            <i class="fas fa-user-shield"></i>
                            <span>Roles de Usuario</span>
                        </a>
                    </div>
                    @endif

                    <div class="suggestion-group">
                        <h6>Reportes SIGSA 3H</h6>
                        <a href="{{ route('reports.index') }}" class="suggestion-item" data-search="reportes sigsa generar estadisticas">
                            <i class="fas fa-chart-line"></i>
                            <span>Generar Reportes</span>
                        </a>
                    </div>
                    
                    <div class="suggestion-group">
                        <h6>Mi Perfil</h6>
                        <a href="{{ route('profile.index') }}" class="suggestion-item" data-search="perfil cuenta usuario configuracion">
                            <i class="fas fa-user-circle"></i>
                            <span>Mi Perfil</span>
                        </a>
                        <a href="{{ route('panel') }}" class="suggestion-item" data-search="inicio dashboard panel principal">
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

    <div class="toast-container position-fixed top-2 end-3 p-3" style="z-index: 1050">
        <!-- Toast elements will be injected here -->
    </div>

    {{-- Core JS Files --}}
    <script src="{{ asset('js/core/popper.min.js')}}"></script>
    <script src="{{ asset('js/core/bootstrap.min.js')}}"></script>
    <script src="{{ asset('js/plugins/perfect-scrollbar.min.js')}}"></script>
    <script src="{{ asset('js/plugins/smooth-scrollbar.min.js')}}"></script>
    
    {{-- Panel JS --}}
    <script src="{{ asset('js/panel.js') }}"></script>
    
    {{-- Material Dashboard JS --}}
    <script src="{{ asset('js/material-dashboard.min.js?v=3.1.0')}}"></script>
    
    @stack('scripts')

    <script>
        // Función global para mostrar toast notifications
        function showToast(type, title, message, duration = 4000) {
            const toastContainer = document.querySelector('.toast-container');
            if (!toastContainer) return;

            const icons = {
                success: 'check_circle',
                error: 'error',
                info: 'info',
                warning: 'warning'
            };

            const colors = {
                success: 'success',
                error: 'danger',
                info: 'info',
                warning: 'warning'
            };

            const toastId = 'toast-' + Date.now();
            const toastHTML = `
                <div id="${toastId}" class="toast" role="alert" aria-live="assertive" aria-atomic="true" data-bs-delay="${duration}">
                    <div class="toast-header bg-${colors[type] ?? 'info'} text-white">
                        <i class="material-symbols-rounded me-2">${icons[type] ?? 'info'}</i>
                        <strong class="me-auto">${title}</strong>
                        <small>Ahora</small>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="toast" aria-label="Close"></button>
                    </div>
                    <div class="toast-body bg-white text-dark">
                        ${message}
                    </div>
                </div>
            `;

            toastContainer.insertAdjacentHTML('beforeend', toastHTML);
            const toastElement = document.getElementById(toastId);
            const toast = new bootstrap.Toast(toastElement);
            toast.show();

            toastElement.addEventListener('hidden.bs.toast', function () {
                toastElement.remove();
            });

            return toast;
        }

        // Función específica para notificaciones de éxito
        function showSuccessToast(message, title = 'Éxito') {
            return showToast('success', title, message);
        }

        // Función específica para notificaciones de error
        function showErrorToast(message, title = 'Error') {
            return showToast('error', title, message);
        }

        // Función específica para notificaciones de información
        function showInfoToast(message, title = 'Información') {
            return showToast('info', title, message);
        }
    </script>

    @if (session('toast'))
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const toastData = @json(session('toast'));
            showToast(
                toastData.type ?? 'info',
                toastData.title ?? 'Notificación',
                toastData.message ?? '',
                5000
            );
        });
    </script>
    @endif

    <script>
        // Sistema de persistencia completa del menú - MEJORADO
        document.addEventListener('DOMContentLoaded', function() {
            console.log('🚀 Iniciando sistema de persistencia del menú');
            
            const currentUrl = window.location.pathname;
            console.log('📍 URL actual:', currentUrl);
            
            // Lista de todos los menús disponibles
            const allMenus = [
                'ProfileNav',
                'menuEmergencia',
                'menuConsulta', 
                'menuMantenimiento',
                'menuGestionMedica',
                'menuCatalogosMedicos',
                'menuEstudiosMedicamentos',
                'menuUbicaciones',
                'menuAdministracion',
                'menuReportes'
            ];
            
            // PASO 1: Función para restaurar estados guardados
            function restoreMenuStates() {
                const savedMenuStates = JSON.parse(localStorage.getItem('menuStates') || '{}');
                console.log('💾 Estados guardados:', savedMenuStates);
                
                // Control especial para ProfileNav
                const isProfilePage = currentUrl.includes('/perfil') || currentUrl.includes('/profile');
                
                allMenus.forEach(menuId => {
                    const menuElement = document.getElementById(menuId);
                    const triggerElement = document.querySelector(`[href="#${menuId}"]`);
                    
                    if (menuElement && triggerElement) {
                        let shouldBeOpen = savedMenuStates[menuId] === true;
                        
                        // ProfileNav solo debe estar abierto en páginas de perfil
                        if (menuId === 'ProfileNav') {
                            shouldBeOpen = isProfilePage;
                            console.log(`👤 ProfileNav en página ${isProfilePage ? 'de perfil' : 'normal'}: ${shouldBeOpen ? 'abierto' : 'cerrado'}`);
                        }
                        
                        if (shouldBeOpen) {
                            console.log(`✅ Abriendo menú: ${menuId}`);
                            menuElement.classList.add('show');
                            triggerElement.setAttribute('aria-expanded', 'true');
                            triggerElement.classList.remove('collapsed');
                        } else {
                            console.log(`❌ Cerrando menú: ${menuId}`);
                            menuElement.classList.remove('show');
                            triggerElement.setAttribute('aria-expanded', 'false');
                            triggerElement.classList.add('collapsed');
                        }
                    } else {
                        console.log(`⚠️ No se encontró menú: ${menuId}`);
                    }
                });
            }
            
            // PASO 2: Función para marcar página activa y gestionar menús especiales
            function markActivePage() {
                console.log('🎯 Marcando página activa');
                
                // Limpiar estados activos previos
                document.querySelectorAll('.active-menu-item').forEach(el => {
                    el.classList.remove('active-menu-item');
                });
                document.querySelectorAll('.active-logo').forEach(el => {
                    el.classList.remove('active-logo');
                });
                
                // Detectar si estamos en páginas de perfil
                if (currentUrl.includes('/perfil') || currentUrl.includes('/profile')) {
                    console.log('👤 Detectada página de perfil');
                    
                    // Mantener abierto el menú "Mi Cuenta"
                    const profileMenu = document.getElementById('ProfileNav');
                    const profileTrigger = document.querySelector('a[href="#ProfileNav"]');
                    
                    if (profileMenu && profileTrigger) {
                        profileMenu.classList.add('show');
                        profileTrigger.setAttribute('aria-expanded', 'true');
                        profileTrigger.classList.remove('collapsed');
                        console.log('📂 Mi Cuenta abierto automáticamente');
                    }
                    
                    // Marcar el enlace "Mi Perfil" como activo
                    const profileLink = document.querySelector('a[href*="/perfil"]') || document.querySelector('a[href*="/profile"]');
                    if (profileLink) {
                        profileLink.classList.add('active-menu-item');
                        console.log('✨ Mi Perfil marcado como activo');
                    }
                    return;
                }
                
                // Detectar si estamos en inicio/dashboard
                if (currentUrl === '/panel' || currentUrl === '/' || currentUrl.includes('/home')) {
                    console.log('🏠 Detectada página de inicio');
                    const logoLink = document.querySelector('.navbar-brand');
                    if (logoLink) {
                        logoLink.classList.add('active-logo');
                        console.log('✨ HOSPROGRESO marcado como activo');
                    }
                    return;
                }
                
                // Buscar el enlace exacto de la página actual
                let activeLink = null;
                
                // 1. Intentar encontrar enlace exacto
                activeLink = document.querySelector(`a[href="${currentUrl}"]`);
                console.log('🔍 Enlace exacto encontrado:', !!activeLink);
                
                // 2. Si no se encuentra exacto, buscar que contenga la URL
                if (!activeLink) {
                    const links = document.querySelectorAll('.sidenav a[href*="/"]');
                    let bestMatch = null;
                    let longestMatch = 0;
                    
                    for (let link of links) {
                        const href = link.getAttribute('href');
                        if (href && href !== '/' && currentUrl.includes(href)) {
                            if (href.length > longestMatch) {
                                bestMatch = link;
                                longestMatch = href.length;
                            }
                        }
                    }
                    activeLink = bestMatch;
                    console.log('🔍 Mejor coincidencia encontrada:', activeLink ? activeLink.getAttribute('href') : 'ninguna');
                }
                
                // 3. Marcar como activo
                if (activeLink) {
                    activeLink.classList.add('active-menu-item');
                    console.log('✨ Página marcada como activa:', activeLink.getAttribute('href'));
                } else {
                    console.log('❓ No se encontró enlace activo para:', currentUrl);
                }
            }
            
            // PASO 3: Función para guardar estado de menú
            function saveMenuState(menuId, isOpen) {
                // ProfileNav no debe guardar su estado porque se controla automáticamente
                if (menuId === 'ProfileNav') {
                    console.log(`👤 ProfileNav estado temporal (no guardado): ${isOpen ? 'abierto' : 'cerrado'}`);
                    return;
                }
                
                const savedStates = JSON.parse(localStorage.getItem('menuStates') || '{}');
                savedStates[menuId] = isOpen;
                localStorage.setItem('menuStates', JSON.stringify(savedStates));
                console.log(`💾 Estado guardado - ${menuId}: ${isOpen ? 'abierto' : 'cerrado'}`);
            }
            
            // PASO 4: Esperar a que Bootstrap esté listo
            setTimeout(() => {
                console.log('⏰ Ejecutando restauración después de Bootstrap');
                restoreMenuStates();
                markActivePage();
                
                // Verificar estados después de restaurar
                setTimeout(() => {
                    console.log('🔍 Verificando estados restaurados:');
                    allMenus.forEach(menuId => {
                        const menuElement = document.getElementById(menuId);
                        if (menuElement) {
                            const isOpen = menuElement.classList.contains('show');
                            console.log(`  ${menuId}: ${isOpen ? 'ABIERTO' : 'cerrado'}`);
                        }
                    });
                }, 500);
            }, 300);
            
            // PASO 5: Escuchar eventos de Bootstrap para guardar estados
            allMenus.forEach(menuId => {
                const menuElement = document.getElementById(menuId);
                if (menuElement) {
                    // Eventos de Bootstrap collapse
                    menuElement.addEventListener('shown.bs.collapse', function() {
                        console.log(`📂 Bootstrap evento: ${menuId} abierto`);
                        saveMenuState(menuId, true);
                    });
                    
                    menuElement.addEventListener('hidden.bs.collapse', function() {
                        console.log(`📁 Bootstrap evento: ${menuId} cerrado`);
                        saveMenuState(menuId, false);
                    });
                }
            });
            
            // PASO 6: Backup - escuchar clics directos también
            document.addEventListener('click', function(e) {
                const clickedElement = e.target.closest('[data-bs-toggle="collapse"]');
                if (clickedElement) {
                    const menuId = clickedElement.getAttribute('href').replace('#', '');
                    console.log(`🖱️ Click detectado en: ${menuId}`);
                    
                    // Esperar a que se procese el cambio
                    setTimeout(() => {
                        const menuElement = document.getElementById(menuId);
                        if (menuElement) {
                            const isOpen = menuElement.classList.contains('show');
                            saveMenuState(menuId, isOpen);
                        }
                    }, 400);
                }
            });
            
            // PASO 7: Funciones de debugging
            window.resetMenuStates = function() {
                console.log('🔄 Reseteando estados del menú');
                localStorage.removeItem('menuStates');
                location.reload();
            };
            
            window.showMenuStates = function() {
                const states = JSON.parse(localStorage.getItem('menuStates') || '{}');
                console.log('📊 Estados actuales del menú:', states);
                return states;
            };
            
            window.forceOpenMenu = function(menuId) {
                const menuElement = document.getElementById(menuId);
                const triggerElement = document.querySelector(`[href="#${menuId}"]`);
                if (menuElement && triggerElement) {
                    menuElement.classList.add('show');
                    triggerElement.setAttribute('aria-expanded', 'true');
                    triggerElement.classList.remove('collapsed');
                    saveMenuState(menuId, true);
                    console.log(`🔓 Menú ${menuId} forzado a abrir`);
                }
            };
            
            console.log('✅ Sistema de persistencia del menú iniciado');
        });
    </script>

    <!-- Script Búsqueda Global -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            console.log('🔍 Iniciando sistema de búsqueda global');
            
            const searchModal = document.getElementById('globalSearchModal');
            const searchInput = document.getElementById('globalSearchInput');
            const closeButton = document.getElementById('closeGlobalSearch');
            
            let isSearchOpen = false;
            
            // Función para abrir búsqueda
            function openSearch() {
                console.log('🔍 Abriendo búsqueda global');
                searchModal.style.display = 'flex';
                setTimeout(() => {
                    searchModal.classList.add('show');
                    searchInput.focus();
                    isSearchOpen = true;
                }, 10);
            }
            
            // Función para cerrar búsqueda
            function closeSearch() {
                console.log('❌ Cerrando búsqueda global');
                searchModal.classList.remove('show');
                setTimeout(() => {
                    searchModal.style.display = 'none';
                    isSearchOpen = false;
                    searchInput.value = '';
                }, 300);
            }
            
            // Evento para tecla TAB (abrir búsqueda)
            document.addEventListener('keydown', function(e) {
                // TAB para abrir búsqueda
                if (e.key === 'Tab' && !e.shiftKey && !e.ctrlKey && !e.altKey) {
                    // Solo si no estamos en un input o textarea
                    if (!['INPUT', 'TEXTAREA', 'SELECT'].includes(e.target.tagName) && 
                        !e.target.isContentEditable && 
                        !isSearchOpen) {
                        e.preventDefault();
                        openSearch();
                        return;
                    }
                }
                
                // ESC para cerrar búsqueda
                if (e.key === 'Escape' && isSearchOpen) {
                    e.preventDefault();
                    closeSearch();
                    return;
                }
                
                // Ctrl+K o Cmd+K para búsqueda (estándar web)
                if ((e.ctrlKey || e.metaKey) && e.key === 'k' && !isSearchOpen) {
                    e.preventDefault();
                    openSearch();
                    return;
                }
            });
            
            // Cerrar con botón X
            closeButton?.addEventListener('click', closeSearch);
            
            // Cerrar haciendo click en el overlay
            searchModal?.addEventListener('click', function(e) {
                if (e.target === searchModal || e.target.classList.contains('search-modal-overlay')) {
                    closeSearch();
                }
            });
            
                         // Navegación con teclado en resultados
             searchInput?.addEventListener('keydown', function(e) {
                 const suggestions = document.querySelectorAll('.suggestion-item[style*="flex"]:not([style*="none"])');
                 let currentIndex = -1;
                 
                 // Limpiar selecciones anteriores
                 document.querySelectorAll('.suggestion-item').forEach(item => {
                     if (item.classList.contains('keyboard-selected')) {
                         currentIndex = Array.from(suggestions).indexOf(item);
                         item.classList.remove('keyboard-selected');
                     }
                 });
                 
                 if (e.key === 'ArrowDown') {
                     e.preventDefault();
                     currentIndex = Math.min(currentIndex + 1, suggestions.length - 1);
                     if (suggestions[currentIndex]) {
                         suggestions[currentIndex].classList.add('keyboard-selected');
                         suggestions[currentIndex].scrollIntoView({ block: 'nearest' });
                     }
                 } else if (e.key === 'ArrowUp') {
                     e.preventDefault();
                     currentIndex = Math.max(currentIndex - 1, 0);
                     if (suggestions[currentIndex]) {
                         suggestions[currentIndex].classList.add('keyboard-selected');
                         suggestions[currentIndex].scrollIntoView({ block: 'nearest' });
                     }
                 } else if (e.key === 'Enter') {
                     e.preventDefault();
                     const selected = document.querySelector('.suggestion-item.keyboard-selected');
                     if (selected) {
                         // Navegar al enlace seleccionado
                         window.location.href = selected.href;
                     } else if (suggestions.length > 0) {
                         // Si no hay selección pero hay resultados, ir al primero
                         window.location.href = suggestions[0].href;
                     } else {
                         // Si no hay resultados, ejecutar búsqueda
                         executeSearch();
                     }
                 }
             });
            
                         // Función de búsqueda en tiempo real
             function performSearch(query = '') {
                 const searchQuery = query.toLowerCase().trim();
                 const allItems = document.querySelectorAll('.suggestion-item');
                 const allGroups = document.querySelectorAll('.suggestion-group');
                 const suggestions = document.getElementById('searchSuggestions');
                 const noResults = document.getElementById('noResults');
                 
                 let hasResults = false;
                 
                 if (searchQuery === '') {
                     // Mostrar todo si no hay búsqueda
                     allGroups.forEach(group => group.style.display = 'block');
                     allItems.forEach(item => item.style.display = 'flex');
                     suggestions.style.display = 'block';
                     noResults.style.display = 'none';
                     return;
                 }
                 
                 allGroups.forEach(group => {
                     const items = group.querySelectorAll('.suggestion-item');
                     let groupHasResults = false;
                     
                     items.forEach(item => {
                         const text = item.textContent.toLowerCase();
                         const searchData = item.getAttribute('data-search') || '';
                         const isMatch = text.includes(searchQuery) || searchData.toLowerCase().includes(searchQuery);
                         
                         if (isMatch) {
                             item.style.display = 'flex';
                             groupHasResults = true;
                             hasResults = true;
                         } else {
                             item.style.display = 'none';
                         }
                     });
                     
                     group.style.display = groupHasResults ? 'block' : 'none';
                 });
                 
                 // Mostrar/ocultar mensajes
                 if (hasResults) {
                     suggestions.style.display = 'block';
                     noResults.style.display = 'none';
                 } else {
                     suggestions.style.display = 'none';
                     noResults.style.display = 'block';
                 }
             }

             // Evento de búsqueda en tiempo real
             searchInput?.addEventListener('input', function(e) {
                 const query = e.target.value;
                 
                 // Limpiar selecciones anteriores
                 document.querySelectorAll('.suggestion-item.keyboard-selected').forEach(item => {
                     item.classList.remove('keyboard-selected');
                 });
                 
                 performSearch(query);
                 console.log('🔍 Buscando:', query);
             });

             // Función para ejecutar búsqueda con Enter o click
             function executeSearch() {
                 const query = searchInput?.value || '';
                 performSearch(query);
                 
                 if (query.trim() !== '') {
                     // Si hay texto y resultados visibles, seleccionar el primero
                     const firstVisible = document.querySelector('.suggestion-item[style*="flex"]:not([style*="none"])');
                     if (firstVisible) {
                         firstVisible.classList.add('keyboard-selected');
                         firstVisible.scrollIntoView({ block: 'nearest' });
                     }
                 }
             }

             // Click en icono de búsqueda
             document.querySelector('.search-icon')?.addEventListener('click', executeSearch);
            
                         // Mostrar indicador de tecla TAB al cargar
             setTimeout(() => {
                 const indicator = document.createElement('div');
                 indicator.className = 'search-key-indicator';
                 indicator.innerHTML = '<i class="fas fa-search"></i> TAB o Ctrl+K para buscar';
                 document.body.appendChild(indicator);
                 
                 setTimeout(() => {
                     indicator.classList.add('show');
                 }, 1000);
                 
                 setTimeout(() => {
                     indicator.classList.remove('show');
                     setTimeout(() => {
                         indicator.remove();
                     }, 300);
                 }, 6000);
             }, 2000);
            
            console.log('✅ Sistema de búsqueda global iniciado');
            console.log('💡 Presiona TAB para abrir la búsqueda global');
            console.log('💡 Presiona Ctrl+K para búsqueda rápida');
        });
        
        // Estilo adicional para selección por teclado
        const style = document.createElement('style');
        style.textContent = `
            .suggestion-item.keyboard-selected {
                background: linear-gradient(135deg, rgba(25, 118, 210, 0.25) 0%, rgba(25, 118, 210, 0.15) 100%) !important;
                border-color: #1976d2 !important;
                color: #1976d2 !important;
                transform: translateX(5px) !important;
                box-shadow: 0 5px 15px rgba(25, 118, 210, 0.3) !important;
            }
            
            .suggestion-item.keyboard-selected::before {
                transform: scaleY(1) !important;
            }
            
            .suggestion-item.keyboard-selected i {
                transform: scale(1.1) !important;
                box-shadow: 0 4px 12px rgba(25, 118, 210, 0.4) !important;
            }
        `;
        document.head.appendChild(style);
    </script>


</body>

</html>
