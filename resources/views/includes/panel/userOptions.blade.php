<div class="d-flex align-items-center justify-content-between w-100">
    <div class="d-flex align-items-center">
        <button class="navbar-toggler d-lg-none me-3" type="button" id="mobileMenuToggle" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon">
                <span class="navbar-toggler-bar bar1"></span>
                <span class="navbar-toggler-bar bar2"></span>
                <span class="navbar-toggler-bar bar3"></span>
            </span>
        </button>
        @if (!request()->routeIs('profile.index') && !request()->routeIs('profile.edit'))
            <nav aria-label="breadcrumb" class="d-none d-md-block">
                <ol class="breadcrumb bg-transparent mb-0 pb-0 pt-1 px-0">
                    @if (View::hasSection('breadcrumb'))
                        <li class="breadcrumb-item text-sm">
                            <a class="opacity-5 text-dark" href="{{ url('/panel') }}">Inicio</a>
                        </li>
                        <li class="breadcrumb-item text-sm text-dark active font-weight-bold" aria-current="page">
                            @yield('breadcrumb')
                        </li>
                    @else
                        <li class="breadcrumb-item text-sm text-dark active font-weight-bold" aria-current="page">
                            @yield('title')
                        </li>
                    @endif
                </ol>
            </nav>
        @endif
    </div>
    <div class="d-flex align-items-center">
        <ul class="navbar-nav align-items-center d-flex flex-row">
            <li class="nav-item d-xl-none">
                <a class="nav-link text-dark" href="javascript:;" id="mobileGlobalSearch" role="button" title="Buscar"
                    onclick="document.getElementById('globalSearchModal').style.display='flex'; setTimeout(() => { document.getElementById('globalSearchModal').classList.add('show'); document.getElementById('globalSearchInput').focus(); }, 10);">
                    <i class="bi bi-search"></i>
                </a>
            </li>
            <li class="nav-item dropdown d-flex align-items-center ms-2">
                <span class="nav-link text-dark d-none d-md-inline me-2">{{ Auth::user()->name }}</span>
                <a class="nav-link text-dark d-flex align-items-center" href="javascript:;" id="ProfileDropdown"
                    role="button" data-bs-toggle="dropdown" data-bs-strategy="fixed" aria-expanded="false">
                    <img src="{{ Auth::user()->profile_photo_url }}" alt="profile" class="rounded-circle"
                        width="30" height="30">
                </a>
                <ul class="dropdown-menu dropdown-menu-end shadow" aria-labelledby="ProfileDropdown">
                    <li>
                        <a class="dropdown-item d-flex align-items-center" href="{{ route('profile.index') }}">
                            <i class="bi bi-person-circle me-2"></i> Mi Perfil
                        </a>
                    </li>
                    <li>
                        <a class="dropdown-item d-flex align-items-center" href="{{ route('logout') }}"
                            onclick="event.preventDefault(); document.getElementById('formlogout').submit();">
                            <i class="bi bi-box-arrow-right me-2"></i> Cerrar Sesión
                        </a>
                        <form method="POST" action="{{ route('logout') }}" style="display: none;" id="formlogout">
                            @csrf
                        </form>
                    </li>
                </ul>
            </li>
        </ul>
    </div>
</div>
<div class="toast-container position-fixed top-0 end-0 p-3" style="z-index: 1055;"></div>
