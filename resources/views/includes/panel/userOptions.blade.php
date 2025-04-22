<div class="collapse navbar-collapse mt-sm-0 mt-2 me-md-0 me-sm-4" id="navbar">
    <div class="ms-md-auto pe-md-3 d-flex align-items-center">
        <div class="input-group input-group-outline">
        </div>
    </div>
    <ul class="navbar-nav">

        <!-- Notificaciones fuera del menú de usuario -->
        <li class="nav-item dropdown">
            <a class="nav-link text-dark position-relative" href="javascript:;" id="notificationDropdown" role="button"
                data-bs-toggle="dropdown" aria-expanded="false">
                <i class="material-symbols-rounded">notifications</i>
                <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                    3 <!-- Aquí puedes poner el conteo dinámico -->
                </span>
            </a>
            <ul class="dropdown-menu dropdown-menu-end shadow p-2" aria-labelledby="notificationDropdown"
                style="min-width: 300px;">
                <li class="dropdown-header fw-bold">Notificaciones</li>
                <li>
                    <a class="dropdown-item small text-wrap">
                        <div class="fw-bold">12/04/2025</div>
                        Se registró un nuevo paciente en emergencia.
                    </a>
                </li>
                <li>
                    <a class="dropdown-item small text-wrap">
                        <div class="fw-bold">11/04/2025</div>
                        El doctor López actualizó un expediente clínico.
                    </a>
                </li>
                <li>
                    <a class="dropdown-item small text-wrap">
                        <div class="fw-bold">10/04/2025</div>
                        Se programó una cirugía para el paciente #2231.
                    </a>
                </li>
            </ul>
        </li>

        <!-- Menú de perfil -->
        <li class="nav-item dropdown d-flex align-items-center">
            <!-- Nombre no clickeable -->
            <span class="nav-link text-dark d-none d-md-inline me-2">{{ Auth::user()->name }}</span>

            <!-- Imagen clickeable -->
            <a class="nav-link text-dark d-flex align-items-center" href="javascript:;" id="ProfileDropdown"
                role="button" data-bs-toggle="dropdown" aria-expanded="false">
                <img src="{{ asset('img/foto-perfil.jpg') }}" alt="profile" class="rounded-circle" width="30"
                    height="30">
            </a>

            <!-- Dropdown del perfil -->
            <ul class="dropdown-menu dropdown-menu-end shadow" aria-labelledby="ProfileDropdown">
                <li>
                    <a class="dropdown-item d-flex align-items-center" href="">
                        <i class="material-symbols-rounded me-2">account_circle</i> Mi Perfil
                    </a>
                </li>
                <li>
                    <a class="dropdown-item d-flex align-items-center" href="{{ route('logout') }}" onclick="event.preventDefault(); document.getElementById('formlogout').submit();">
                        <i class="material-symbols-rounded me-2">logout</i> Cerrar Sesión
                    </a>
                    <form method="POST" action="{{ route('logout') }}" style="display: none;" id="formlogout">
                        @csrf
                    </form>
                </li>
            </ul>
        </li>
    </ul>
</div>
