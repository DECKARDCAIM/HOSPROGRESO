<hr class="horizontal dark" />
<h6 class="ps-3 ms-2 text-uppercase text-xs font-weight-bolder text-dark">Panel de Controles</h6>

<ul class="navbar-nav">
    <!-- Emergencia -->
    <li class="nav-item">
        <a data-bs-toggle="collapse" href="#menuEmergencia" class="nav-link text-dark"
            aria-controls="menuEmergencia" role="button" aria-expanded="false">
            <i class="material-symbols-rounded opacity-5">space_dashboard</i>
            <span class="nav-link-text ms-1 ps-1">Emergencia</span>
        </a>
        <div class="collapse" id="menuEmergencia">
            <ul class="nav">
                <li class="nav-item">
                    <a class="nav-link text-dark" href="../../pages/dashboards/analytics.html">
                        <span class="sidenav-normal ms-1 ps-1">Paciente</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link text-dark" href="../../pages/dashboards/discover.html">
                        <span class="sidenav-normal ms-1 ps-1">Lista de Turnos</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link text-dark" href="../../pages/dashboards/discover.html">
                        <span class="sidenav-normal ms-1 ps-1">Referencias</span>
                    </a>
                </li>
            </ul>
        </div>
    </li>

    <!-- Consulta Externa -->
    <li class="nav-item">
        <a data-bs-toggle="collapse" href="#menuConsulta" class="nav-link text-dark"
            aria-controls="menuConsulta" role="button" aria-expanded="false">
            <i class="material-symbols-rounded opacity-5">space_dashboard</i>
            <span class="nav-link-text ms-1 ps-1">Consulta Externa</span>
        </a>
        <div class="collapse" id="menuConsulta">
            <ul class="nav">
                <li class="nav-item">
                    <a class="nav-link text-dark" href="../../pages/dashboards/analytics.html">
                        <span class="sidenav-normal ms-1 ps-1">Paciente</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link text-dark" href="../../pages/dashboards/discover.html">
                        <span class="sidenav-normal ms-1 ps-1">Lista de Turnos</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link text-dark" href="../../pages/dashboards/discover.html">
                        <span class="sidenav-normal ms-1 ps-1">Citas</span>
                    </a>
                </li>
            </ul>
        </div>
    </li>

    <!-- Mantenimiento -->
    <li class="nav-item">
        <a data-bs-toggle="collapse" href="#menuMantenimiento" class="nav-link text-dark"
            aria-controls="menuMantenimiento" role="button" aria-expanded="false">
            <i class="material-symbols-rounded opacity-5">space_dashboard</i>
            <span class="nav-link-text ms-1 ps-1">Mantenimiento</span>
        </a>
        <div class="collapse" id="menuMantenimiento">
            <ul class="nav">
                <li class="nav-item">
                    <a class="nav-link text-dark" href="{{ url('/especialidades') }}">
                        <span class="sidenav-normal ms-1 ps-1">Especialidades</span>
                    </a>
                </li>
                <!--
                    
                    <li class="nav-item">
                        <a class="nav-link text-dark" href="../../pages/dashboards/discover.html">
                            <span class="sidenav-normal ms-1 ps-1">Enfermeros</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link text-dark" href="../../pages/dashboards/automotive.html">
                            <span class="sidenav-normal ms-1 ps-1">Medicamentos</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link text-dark" href="../../pages/dashboards/smart-home.html">
                            <span class="sidenav-normal ms-1 ps-1">Exámenes</span>
                        </a>
                    </li>
                -->





                <li class="nav-item">
                    <a data-bs-toggle="collapse" href="#menuUbicaciones" class="nav-link text-dark"
                        aria-controls="menuMantenimiento" role="button" aria-expanded="false">
                        <i class="material-symbols-rounded opacity-5">space_dashboard</i>
                        <span class="nav-link-text ms-1 ps-1">Ubicaciones</span>
                    </a>
                    <div class="collapse" id="menuUbicaciones">
                        <ul class="nav">
                            <li class="nav-item">
                                <a class="nav-link text-dark" href="{{ url('/paises') }}">
                                    <span class="sidenav-normal ms-1 ps-1">Paises</span>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link text-dark" href="{{ url('/departamentos') }}">
                                    <span class="sidenav-normal ms-1 ps-1">Departamentos</span>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link text-dark" href="{{ url('/municipios') }}">
                                    <span class="sidenav-normal ms-1 ps-1">Municipios</span>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link text-dark" href="{{ url('/direcciones') }}">
                                    <span class="sidenav-normal ms-1 ps-1">Direcciones</span>
                                </a>
                            </li>
                        </ul>
                    </div>
                </li>

                
            </ul>
        </div>
    </li>
</ul>
