@if (!request()->routeIs('profile.index') && !request()->routeIs('profile.edit'))
<nav aria-label="breadcrumb">
    <ol class="breadcrumb bg-transparent mb-0 pb-0 pt-1 px-0 me-sm-6 me-5">
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

<div class="collapse navbar-collapse mt-sm-0 mt-2 me-md-0 me-sm-4" id="navbar">
    <ul class="navbar-nav align-items-center ms-auto">

        <!-- Notificaciones -->
        <li class="nav-item dropdown">
            <a class="nav-link text-dark position-relative notification-trigger" href="javascript:;" id="notificationDropdown" role="button"
                data-bs-toggle="dropdown" data-bs-strategy="fixed" aria-expanded="false">
                <i class="material-symbols-rounded notification-bell">notifications</i>
                <span class="notification-badge" id="notification-count" style="display: none;"></span>
            </a>
            <ul class="dropdown-menu dropdown-menu-end professional-dropdown" aria-labelledby="notificationDropdown">
                <li class="dropdown-header-professional">
                    <div class="header-content">
                        <div class="header-icon">
                            <i class="material-symbols-rounded">notifications</i>
                        </div>
                        <div class="header-text">
                            <h6>Notificaciones</h6>
                            <small>Manténgase actualizado</small>
                        </div>
                    </div>
                    <button class="btn-mini-professional" id="mark-all-read" title="Marcar todas como leídas">
                        <i class="material-symbols-rounded">done_all</i>
                    </button>
                </li>
                <li class="dropdown-divider-professional"></li>
                <div id="notifications-list" class="notifications-preview">
                    <!-- Las notificaciones se cargarán dinámicamente aquí -->
                </div>
                <li class="dropdown-divider-professional"></li>
                <li class="dropdown-footer-professional">
                    <button class="btn-view-all-professional" id="view-all-notifications">
                        <i class="material-symbols-rounded me-2">open_in_full</i>
                        <span>Ver Centro de Notificaciones</span>
                    </button>
                </li>
            </ul>
        </li>

        <!-- Menú de perfil -->
        <li class="nav-item dropdown d-flex align-items-center ms-2">
            <span class="nav-link text-dark d-none d-md-inline me-2">{{ Auth::user()->name }}</span>
            <a class="nav-link text-dark d-flex align-items-center" href="javascript:;" id="ProfileDropdown"
                role="button" data-bs-toggle="dropdown" data-bs-strategy="fixed" aria-expanded="false">
                <img src="{{ Auth::user()->profile_photo_url }}" alt="profile" class="rounded-circle" width="30" height="30">
            </a>
            <ul class="dropdown-menu dropdown-menu-end shadow" aria-labelledby="ProfileDropdown">
                <li>
                    <a class="dropdown-item d-flex align-items-center" href="{{ route('profile.index') }}">
                        <i class="material-symbols-rounded me-2">account_circle</i> Mi Perfil
                    </a>
                </li>
                <li>
                    <a class="dropdown-item d-flex align-items-center" href="{{ route('logout') }}"
                        onclick="event.preventDefault(); document.getElementById('formlogout').submit();">
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
@endif

<!-- Modal para todas las notificaciones -->
<div class="modal fade" id="allNotificationsModal" tabindex="-1" aria-labelledby="allNotificationsModalLabel" aria-hidden="true" data-bs-backdrop="static">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content professional-modal">
            <div class="modal-header professional-header">
                <div class="d-flex align-items-center">
                    <div class="notification-icon-header">
                        <i class="material-symbols-rounded">notifications</i>
                    </div>
                    <div class="ms-3">
                        <h5 class="modal-title mb-0" id="allNotificationsModalLabel">Centro de Notificaciones</h5>
                        <small class="text-muted">Gestiona todas tus notificaciones</small>
                    </div>
                </div>
                <button type="button" class="btn-close-professional" data-bs-dismiss="modal" aria-label="Close">
                    <i class="material-symbols-rounded">close</i>
                </button>
            </div>
            <div class="modal-body professional-body">
                <div class="notification-controls">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <div class="notification-stats">
                            <div class="stat-item">
                                <i class="material-symbols-rounded">mark_email_read</i>
                                <span>Mostrando las últimas 30 notificaciones</span>
                            </div>
                        </div>
                        <button class="btn btn-professional-primary" id="modal-mark-all-read">
                            <i class="material-symbols-rounded me-2">done_all</i>
                            Marcar todas como leídas
                        </button>
                    </div>
                </div>
                <div id="all-notifications-list" class="notifications-container">
                    <!-- Todas las notificaciones se cargarán aquí -->
                </div>
            </div>
            <div class="modal-footer professional-footer">
                <button type="button" class="btn btn-professional-secondary" data-bs-dismiss="modal">
                    <i class="material-symbols-rounded me-2">close</i>
                    Cerrar
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Toast Container para alertas -->
<div class="toast-container position-fixed top-0 end-0 p-3" style="z-index: 1055;">
    <!-- Los toasts se agregarán dinámicamente aquí -->
</div>

<!-- Estilos profesionales para notificaciones -->
<style>
:root {
    --primary-blue: #1976d2;
    --primary-blue-dark: #1565c0;
    --primary-blue-light: #e3f2fd;
    --secondary-blue: #42a5f5;
    --success-color: #4caf50;
    --warning-color: #ff9800;
    --error-color: #f44336;
    --info-color: #2196f3;
    --text-primary: #212529;
    --text-secondary: #6c757d;
    --background-white: #ffffff;
    --background-light: #f8f9fa;
    --border-color: #e0e0e0;
    --shadow-light: 0 2px 8px rgba(0, 0, 0, 0.1);
    --shadow-medium: 0 4px 16px rgba(0, 0, 0, 0.15);
    --shadow-heavy: 0 8px 32px rgba(0, 0, 0, 0.25);
}

/* FONDO CON DESENFOQUE SUTIL Y ELEGANTE */
.modal-backdrop {
    background: linear-gradient(135deg, rgba(25, 118, 210, 0.1) 0%, rgba(0, 0, 0, 0.2) 100%) !important;
    backdrop-filter: blur(20px) saturate(150%) brightness(80%) !important;
    -webkit-backdrop-filter: blur(20px) saturate(150%) brightness(80%) !important;
    transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1) !important;
}

.modal-backdrop.show {
    opacity: 1 !important;
}

/* MODAL PROFESIONAL */
.professional-modal {
    border: none !important;
    border-radius: 20px !important;
    box-shadow: var(--shadow-heavy) !important;
    overflow: hidden !important;
    background: var(--background-white) !important;
    transform: scale(0.9);
    transition: all 0.3s ease !important;
}

.modal.show .professional-modal {
    transform: scale(1) !important;
}

/* HEADER AZUL PROFESIONAL */
.professional-header {
    background: linear-gradient(135deg, var(--primary-blue) 0%, var(--primary-blue-dark) 100%) !important;
    border: none !important;
    padding: 30px 100px 30px 40px !important;
    color: white !important;
    position: relative !important;
}

.professional-header::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><defs><pattern id="grid" width="10" height="10" patternUnits="userSpaceOnUse"><path d="M 10 0 L 0 0 0 10" fill="none" stroke="rgba(255,255,255,0.1)" stroke-width="0.5"/></pattern></defs><rect width="100" height="100" fill="url(%23grid)"/></svg>');
    opacity: 0.3;
}

.notification-icon-header {
    background: rgba(255, 255, 255, 0.2) !important;
    width: 70px !important;
    height: 70px !important;
    border-radius: 50% !important;
    display: flex !important;
    align-items: center !important;
    justify-content: center !important;
    position: relative !important;
    z-index: 1 !important;
}

.notification-icon-header i {
    font-size: 32px !important;
    color: white !important;
}

.professional-header .modal-title {
    font-size: 1.8rem !important;
    font-weight: 700 !important;
    color: white !important;
    margin: 0 !important;
    position: relative !important;
    z-index: 1 !important;
}

.professional-header small {
    color: rgba(255, 255, 255, 0.9) !important;
    font-size: 1rem !important;
    position: relative !important;
    z-index: 1 !important;
}

.btn-close-professional {
    background: rgba(255, 255, 255, 0.15) !important;
    border: 2px solid rgba(255, 255, 255, 0.3) !important;
    border-radius: 50% !important;
    width: 50px !important;
    height: 50px !important;
    display: flex !important;
    align-items: center !important;
    justify-content: center !important;
    transition: all 0.3s ease !important;
    position: absolute !important;
    top: 25px !important;
    right: 30px !important;
    z-index: 2 !important;
}

.btn-close-professional:hover {
    background: rgba(255, 255, 255, 0.25) !important;
    transform: scale(1.1) !important;
}

.btn-close-professional i {
    color: white !important;
    font-size: 24px !important;
}

/* CUERPO DEL MODAL */
.professional-body {
    padding: 40px !important;
    background: var(--background-white) !important;
    max-height: 65vh !important;
    overflow-y: auto !important;
}

.notification-controls {
    margin-bottom: 30px !important;
}

.stat-item {
    display: flex !important;
    align-items: center !important;
    background: var(--primary-blue-light) !important;
    padding: 15px 20px !important;
    border-radius: 12px !important;
    color: var(--primary-blue-dark) !important;
    font-weight: 600 !important;
    font-size: 1rem !important;
}

.stat-item i {
    margin-right: 12px !important;
    font-size: 24px !important;
}

/* BOTONES PROFESIONALES */
.btn-professional-primary {
    background: linear-gradient(135deg, var(--primary-blue) 0%, var(--primary-blue-dark) 100%) !important;
    border: none !important;
    color: white !important;
    padding: 15px 30px !important;
    border-radius: 10px !important;
    font-weight: 700 !important;
    display: flex !important;
    align-items: center !important;
    transition: all 0.3s ease !important;
    box-shadow: var(--shadow-light) !important;
    font-size: 1rem !important;
}

.btn-professional-primary:hover {
    transform: translateY(-3px) !important;
    box-shadow: var(--shadow-medium) !important;
    background: linear-gradient(135deg, var(--primary-blue-dark) 0%, var(--primary-blue) 100%) !important;
    color: white !important;
}

.btn-professional-secondary {
    background: var(--background-light) !important;
    border: 2px solid var(--border-color) !important;
    color: var(--text-secondary) !important;
    padding: 15px 30px !important;
    border-radius: 10px !important;
    font-weight: 700 !important;
    display: flex !important;
    align-items: center !important;
    transition: all 0.3s ease !important;
    font-size: 1rem !important;
}

.btn-professional-secondary:hover {
    background: var(--border-color) !important;
    color: var(--text-primary) !important;
    transform: translateY(-2px) !important;
}

/* CONTENEDOR DE NOTIFICACIONES */
.notifications-container {
    display: flex !important;
    flex-direction: column !important;
    gap: 20px !important;
}

.professional-notification-card {
    background: var(--background-white) !important;
    border: 2px solid var(--border-color) !important;
    border-radius: 16px !important;
    padding: 25px !important;
    transition: all 0.3s ease !important;
    position: relative !important;
    overflow: hidden !important;
}

.professional-notification-card::before {
    content: '';
    position: absolute;
    left: 0;
    top: 0;
    bottom: 0;
    width: 6px;
    background: var(--border-color);
    transition: all 0.3s ease;
}

.professional-notification-card.unread {
    background: linear-gradient(135deg, rgba(25, 118, 210, 0.05) 0%, rgba(25, 118, 210, 0.1) 100%) !important;
    border-color: var(--primary-blue-light) !important;
    box-shadow: var(--shadow-light) !important;
}

.professional-notification-card.unread::before {
    background: linear-gradient(135deg, var(--primary-blue) 0%, var(--secondary-blue) 100%) !important;
}

.professional-notification-card:hover {
    transform: translateY(-5px) !important;
    box-shadow: var(--shadow-medium) !important;
}

.notification-card-content {
    display: flex !important;
    align-items: flex-start !important;
    gap: 20px !important;
}

.notification-icon-type {
    width: 60px !important;
    height: 60px !important;
    border-radius: 50% !important;
    display: flex !important;
    align-items: center !important;
    justify-content: center !important;
    flex-shrink: 0 !important;
}

.professional-notification-card.success .notification-icon-type {
    background: rgba(76, 175, 80, 0.15) !important;
    color: var(--success-color) !important;
}

.professional-notification-card.warning .notification-icon-type {
    background: rgba(255, 152, 0, 0.15) !important;
    color: var(--warning-color) !important;
}

.professional-notification-card.error .notification-icon-type {
    background: rgba(244, 67, 54, 0.15) !important;
    color: var(--error-color) !important;
}

.professional-notification-card.info .notification-icon-type,
.professional-notification-card:not(.success):not(.warning):not(.error) .notification-icon-type {
    background: var(--primary-blue-light) !important;
    color: var(--primary-blue) !important;
}

.notification-icon-type i {
    font-size: 28px !important;
}

.notification-details {
    flex: 1 !important;
    min-width: 0 !important;
}

.notification-header {
    display: flex !important;
    justify-content: space-between !important;
    align-items: flex-start !important;
    margin-bottom: 12px !important;
    gap: 15px !important;
}

.notification-title {
    font-size: 1.3rem !important;
    font-weight: 700 !important;
    color: var(--text-primary) !important;
    margin: 0 !important;
    line-height: 1.3 !important;
}

.notification-time {
    color: var(--text-secondary) !important;
    font-size: 0.95rem !important;
    white-space: nowrap !important;
    flex-shrink: 0 !important;
    font-weight: 500 !important;
}

.notification-message {
    color: var(--text-secondary) !important;
    font-size: 1.05rem !important;
    line-height: 1.6 !important;
    margin: 0 0 12px 0 !important;
}

.unread-indicator {
    display: inline-block !important;
    background: linear-gradient(135deg, var(--primary-blue) 0%, var(--secondary-blue) 100%) !important;
    color: white !important;
    font-size: 0.8rem !important;
    font-weight: 800 !important;
    padding: 6px 12px !important;
    border-radius: 15px !important;
    letter-spacing: 0.8px !important;
    text-transform: uppercase !important;
}

.notification-actions {
    display: flex !important;
    align-items: center !important;
    flex-shrink: 0 !important;
}

.btn-professional-action {
    background: var(--primary-blue-light) !important;
    border: none !important;
    color: var(--primary-blue) !important;
    width: 45px !important;
    height: 45px !important;
    border-radius: 50% !important;
    display: flex !important;
    align-items: center !important;
    justify-content: center !important;
    transition: all 0.3s ease !important;
    cursor: pointer !important;
}

.btn-professional-action:hover {
    background: var(--primary-blue) !important;
    color: white !important;
    transform: scale(1.15) !important;
}

.read-status {
    width: 45px !important;
    height: 45px !important;
    border-radius: 50% !important;
    display: flex !important;
    align-items: center !important;
    justify-content: center !important;
    background: rgba(76, 175, 80, 0.15) !important;
    color: var(--success-color) !important;
}

.read-status i {
    font-size: 24px !important;
}

/* PIE DEL MODAL */
.professional-footer {
    background: var(--background-light) !important;
    border: none !important;
    padding: 25px 40px !important;
    display: flex !important;
    justify-content: flex-end !important;
    gap: 15px !important;
}

/* ESTADO SIN NOTIFICACIONES */
.no-notifications-state {
    display: flex !important;
    flex-direction: column !important;
    align-items: center !important;
    justify-content: center !important;
    padding: 80px 20px !important;
    text-align: center !important;
}

.no-notifications-icon {
    width: 100px !important;
    height: 100px !important;
    border-radius: 50% !important;
    background: var(--primary-blue-light) !important;
    display: flex !important;
    align-items: center !important;
    justify-content: center !important;
    margin-bottom: 30px !important;
}

.no-notifications-icon i {
    font-size: 50px !important;
    color: var(--primary-blue) !important;
}

.no-notifications-state h6 {
    font-size: 1.5rem !important;
    font-weight: 700 !important;
    color: var(--text-primary) !important;
    margin-bottom: 12px !important;
}

.no-notifications-state p {
    font-size: 1.1rem !important;
    color: var(--text-secondary) !important;
    margin: 0 !important;
}

/* ANIMACIONES */
@keyframes slideInUp {
    from {
        opacity: 0;
        transform: translateY(30px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.professional-notification-card {
    animation: slideInUp 0.4s ease forwards !important;
}

/* ===== DROPDOWN PROFESIONAL DE NOTIFICACIONES ===== */

/* TRIGGER DE NOTIFICACIONES */
.notification-trigger {
    transition: all 0.3s ease !important;
    padding: 8px 12px !important;
    border-radius: 8px !important;
    position: relative !important;
}

.notification-trigger:hover {
    background: var(--primary-blue-light) !important;
    color: var(--primary-blue) !important;
    transform: scale(1.05) !important;
}

.notification-bell {
    font-size: 24px !important;
    transition: all 0.3s ease !important;
}

.notification-trigger:hover .notification-bell {
    color: var(--primary-blue) !important;
}

.notification-badge {
    position: absolute !important;
    top: 2px !important;
    right: 2px !important;
    background: linear-gradient(135deg, #ff4444 0%, #cc0000 100%) !important;
    color: white !important;
    font-size: 0.7rem !important;
    font-weight: 700 !important;
    padding: 2px 6px !important;
    border-radius: 12px !important;
    min-width: 18px !important;
    height: 18px !important;
    display: none !important;
    align-items: center !important;
    justify-content: center !important;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.2) !important;
    animation: pulse 2s infinite !important;
}

.notification-badge.show {
    display: flex !important;
}

.notification-badge:empty {
    display: none !important;
}

@keyframes pulse {
    0% { transform: scale(1); }
    50% { transform: scale(1.1); }
    100% { transform: scale(1); }
}

/* DROPDOWN PROFESIONAL */
.professional-dropdown {
    border: none !important;
    border-radius: 16px !important;
    box-shadow: 0 8px 32px rgba(0, 0, 0, 0.2) !important;
    padding: 0 !important;
    min-width: 380px !important;
    max-width: 400px !important;
    background: var(--background-white) !important;
    overflow: hidden !important;
    margin-top: 8px !important;
}

.dropdown-header-professional {
    background: linear-gradient(135deg, var(--primary-blue) 0%, var(--primary-blue-dark) 100%) !important;
    color: white !important;
    padding: 20px !important;
    margin: 0 !important;
    border: none !important;
    display: flex !important;
    justify-content: space-between !important;
    align-items: center !important;
    position: relative !important;
}

.dropdown-header-professional::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><defs><pattern id="grid-small" width="8" height="8" patternUnits="userSpaceOnUse"><path d="M 8 0 L 0 0 0 8" fill="none" stroke="rgba(255,255,255,0.1)" stroke-width="0.5"/></pattern></defs><rect width="100" height="100" fill="url(%23grid-small)"/></svg>');
    opacity: 0.3;
}

.header-content {
    display: flex !important;
    align-items: center !important;
    gap: 12px !important;
    position: relative !important;
    z-index: 1 !important;
}

.header-icon {
    width: 40px !important;
    height: 40px !important;
    background: rgba(255, 255, 255, 0.2) !important;
    border-radius: 50% !important;
    display: flex !important;
    align-items: center !important;
    justify-content: center !important;
}

.header-icon i {
    font-size: 20px !important;
    color: white !important;
}

.header-text h6 {
    margin: 0 !important;
    font-size: 1.1rem !important;
    font-weight: 700 !important;
    color: white !important;
}

.header-text small {
    color: rgba(255, 255, 255, 0.8) !important;
    font-size: 0.85rem !important;
}

.btn-mini-professional {
    background: rgba(255, 255, 255, 0.15) !important;
    border: 1px solid rgba(255, 255, 255, 0.3) !important;
    color: white !important;
    width: 36px !important;
    height: 36px !important;
    border-radius: 50% !important;
    display: flex !important;
    align-items: center !important;
    justify-content: center !important;
    transition: all 0.3s ease !important;
    position: relative !important;
    z-index: 1 !important;
}

.btn-mini-professional:hover {
    background: rgba(255, 255, 255, 0.25) !important;
    transform: scale(1.1) !important;
    color: white !important;
}

.btn-mini-professional i {
    font-size: 18px !important;
}

.dropdown-divider-professional {
    height: 1px !important;
    background: var(--border-color) !important;
    margin: 0 !important;
    border: none !important;
}

/* PREVIEW DE NOTIFICACIONES */
.notifications-preview {
    max-height: 320px !important;
    overflow-y: auto !important;
    padding: 8px !important;
}

.notification-preview-item {
    padding: 12px !important;
    margin-bottom: 8px !important;
    border-radius: 12px !important;
    border: 1px solid transparent !important;
    transition: all 0.3s ease !important;
    position: relative !important;
    cursor: pointer !important;
}

.notification-preview-item::before {
    content: '';
    position: absolute;
    left: 0;
    top: 0;
    bottom: 0;
    width: 3px;
    background: var(--border-color);
    border-radius: 0 8px 8px 0;
    transition: all 0.3s ease;
}

.notification-preview-item.unread {
    background: linear-gradient(135deg, rgba(25, 118, 210, 0.03) 0%, rgba(25, 118, 210, 0.08) 100%) !important;
    border-color: var(--primary-blue-light) !important;
}

.notification-preview-item.unread::before {
    background: linear-gradient(135deg, var(--primary-blue) 0%, var(--secondary-blue) 100%) !important;
}

.notification-preview-item:hover {
    transform: translateX(4px) !important;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1) !important;
}

.notification-preview-content {
    display: flex !important;
    align-items: flex-start !important;
    gap: 12px !important;
}

.notification-preview-icon {
    width: 36px !important;
    height: 36px !important;
    border-radius: 50% !important;
    display: flex !important;
    align-items: center !important;
    justify-content: center !important;
    flex-shrink: 0 !important;
}

.notification-preview-item.success .notification-preview-icon {
    background: rgba(76, 175, 80, 0.1) !important;
    color: var(--success-color) !important;
}

.notification-preview-item.warning .notification-preview-icon {
    background: rgba(255, 152, 0, 0.1) !important;
    color: var(--warning-color) !important;
}

.notification-preview-item.error .notification-preview-icon {
    background: rgba(244, 67, 54, 0.1) !important;
    color: var(--error-color) !important;
}

.notification-preview-item.info .notification-preview-icon,
.notification-preview-item:not(.success):not(.warning):not(.error) .notification-preview-icon {
    background: var(--primary-blue-light) !important;
    color: var(--primary-blue) !important;
}

.notification-preview-icon i {
    font-size: 18px !important;
}

.notification-preview-details {
    flex: 1 !important;
    min-width: 0 !important;
}

.notification-preview-header {
    display: flex !important;
    justify-content: space-between !important;
    align-items: flex-start !important;
    margin-bottom: 4px !important;
    gap: 8px !important;
}

.notification-preview-title {
    font-size: 0.95rem !important;
    font-weight: 600 !important;
    color: var(--text-primary) !important;
    margin: 0 !important;
    line-height: 1.2 !important;
}

.notification-preview-time {
    color: var(--text-secondary) !important;
    font-size: 0.75rem !important;
    white-space: nowrap !important;
    flex-shrink: 0 !important;
}

.notification-preview-message {
    color: var(--text-secondary) !important;
    font-size: 0.85rem !important;
    line-height: 1.3 !important;
    margin: 0 0 6px 0 !important;
    display: -webkit-box !important;
    -webkit-line-clamp: 2 !important;
    -webkit-box-orient: vertical !important;
    overflow: hidden !important;
}

.mini-unread-indicator {
    display: inline-block !important;
    background: linear-gradient(135deg, var(--primary-blue) 0%, var(--secondary-blue) 100%) !important;
    color: white !important;
    font-size: 0.65rem !important;
    font-weight: 700 !important;
    padding: 2px 6px !important;
    border-radius: 8px !important;
    letter-spacing: 0.5px !important;
    text-transform: uppercase !important;
}

.notification-preview-actions {
    display: flex !important;
    align-items: center !important;
    flex-shrink: 0 !important;
}

.btn-mini-action {
    background: var(--primary-blue-light) !important;
    border: none !important;
    color: var(--primary-blue) !important;
    width: 28px !important;
    height: 28px !important;
    border-radius: 50% !important;
    display: flex !important;
    align-items: center !important;
    justify-content: center !important;
    transition: all 0.3s ease !important;
    cursor: pointer !important;
}

.btn-mini-action:hover {
    background: var(--primary-blue) !important;
    color: white !important;
    transform: scale(1.1) !important;
}

.btn-mini-action i {
    font-size: 14px !important;
}

.mini-read-status {
    width: 28px !important;
    height: 28px !important;
    border-radius: 50% !important;
    display: flex !important;
    align-items: center !important;
    justify-content: center !important;
    background: rgba(76, 175, 80, 0.1) !important;
    color: var(--success-color) !important;
}

.mini-read-status i {
    font-size: 14px !important;
}

/* ESTADO SIN NOTIFICACIONES EN PREVIEW */
.no-notifications-preview {
    padding: 40px 20px !important;
    text-align: center !important;
    color: var(--text-secondary) !important;
}

.no-notifications-icon-small {
    width: 60px !important;
    height: 60px !important;
    border-radius: 50% !important;
    background: var(--primary-blue-light) !important;
    display: flex !important;
    align-items: center !important;
    justify-content: center !important;
    margin: 0 auto 16px !important;
}

.no-notifications-icon-small i {
    font-size: 30px !important;
    color: var(--primary-blue) !important;
}

.no-notifications-preview p {
    margin: 0 !important;
    font-size: 0.95rem !important;
    color: var(--text-secondary) !important;
}

/* PIE DEL DROPDOWN */
.dropdown-footer-professional {
    padding: 16px !important;
    background: var(--background-light) !important;
    border-top: 1px solid var(--border-color) !important;
}

.btn-view-all-professional {
    width: 100% !important;
    background: linear-gradient(135deg, var(--primary-blue) 0%, var(--primary-blue-dark) 100%) !important;
    border: none !important;
    color: white !important;
    padding: 12px 20px !important;
    border-radius: 10px !important;
    font-weight: 600 !important;
    display: flex !important;
    align-items: center !important;
    justify-content: center !important;
    transition: all 0.3s ease !important;
    font-size: 0.95rem !important;
}

.btn-view-all-professional:hover {
    transform: translateY(-2px) !important;
    box-shadow: 0 6px 20px rgba(25, 118, 210, 0.3) !important;
    color: white !important;
}

/* SCROLLBAR DEL PREVIEW */
.notifications-preview::-webkit-scrollbar {
    width: 6px !important;
}

.notifications-preview::-webkit-scrollbar-track {
    background: var(--background-light) !important;
    border-radius: 3px !important;
}

.notifications-preview::-webkit-scrollbar-thumb {
    background: var(--border-color) !important;
    border-radius: 3px !important;
}

.notifications-preview::-webkit-scrollbar-thumb:hover {
    background: var(--primary-blue) !important;
}

/* RESPONSIVE */
@media (max-width: 768px) {
    .professional-modal {
        margin: 15px !important;
        border-radius: 20px !important;
    }
    
    .professional-header {
        padding: 25px 80px 25px 30px !important;
    }
    
    .btn-close-professional {
        top: 20px !important;
        right: 25px !important;
        width: 45px !important;
        height: 45px !important;
    }
    
    .professional-body {
        padding: 30px 25px !important;
    }
    
    .professional-footer {
        padding: 20px 25px !important;
    }
    
    .notification-controls > div {
        flex-direction: column !important;
        gap: 20px !important;
    }
    
    .notification-header {
        flex-direction: column !important;
        align-items: flex-start !important;
        gap: 10px !important;
    }
    
    .professional-dropdown {
        min-width: 320px !important;
        max-width: calc(100vw - 40px) !important;
    }
    
    .notification-preview-header {
        flex-direction: column !important;
        align-items: flex-start !important;
        gap: 4px !important;
    }
}

@media (max-width: 576px) {
    .professional-modal {
        margin: 0 !important;
        border-radius: 0 !important;
        height: 100vh !important;
    }
    
    .professional-body {
        max-height: calc(100vh - 200px) !important;
    }
    
    .professional-header {
        padding: 20px 70px 20px 25px !important;
    }
    
    .btn-close-professional {
        top: 15px !important;
        right: 20px !important;
        width: 40px !important;
        height: 40px !important;
    }
    
    .btn-close-professional i {
        font-size: 20px !important;
    }
}

/* ===== MEJORAS ESPECÍFICAS ===== */

/* Mejorar SOLO el dropdown del perfil (no tocar notificaciones) */
.dropdown-menu:not(.professional-dropdown) {
    border: none !important;
    border-radius: 12px !important;
    box-shadow: 0 8px 32px rgba(0, 0, 0, 0.15) !important;
    padding: 8px !important;
    background: var(--background-white) !important;
    overflow: hidden !important;
}

.dropdown-item:not(.notification-preview-item) {
    border-radius: 8px !important;
    padding: 12px 16px !important;
    transition: all 0.3s ease !important;
    display: flex !important;
    align-items: center !important;
    margin: 2px 0 !important;
}

.dropdown-item:not(.notification-preview-item):hover {
    background: var(--primary-blue-light) !important;
    color: var(--primary-blue) !important;
    transform: translateX(4px) !important;
}

.dropdown-item:not(.notification-preview-item) i {
    transition: all 0.3s ease !important;
    margin-right: 8px !important;
}

.dropdown-item:not(.notification-preview-item):hover i {
    color: var(--primary-blue) !important;
}

/* Tamaño uniforme de texto en toda la aplicación */
.nav-link-text, .sidenav-normal, .dropdown-item, 
.notification-preview-title, .notification-title {
    font-size: 0.875rem !important;
    line-height: 1.4 !important;
}

/* ===== EFECTOS HOVER PARA EL MENÚ ===== */

/* Efectos hover para items principales del menú */
.navbar-nav .nav-item > .nav-link {
    transition: all 0.3s ease !important;
    border-radius: 8px !important;
    margin: 2px 8px !important;
    padding: 12px 16px !important;
    position: relative !important;
}

.navbar-nav .nav-item > .nav-link:hover {
    background: linear-gradient(135deg, var(--primary-blue-light) 0%, rgba(25, 118, 210, 0.1) 100%) !important;
    color: var(--primary-blue) !important;
    transform: translateX(4px) !important;
    box-shadow: 0 4px 12px rgba(25, 118, 210, 0.15) !important;
}

.navbar-nav .nav-item > .nav-link:hover i {
    color: var(--primary-blue) !important;
    transform: scale(1.05) !important;
}

.navbar-nav .nav-item > .nav-link i {
    transition: all 0.3s ease !important;
}

/* Efectos hover para submenús */
.navbar-nav .nav .nav-item .nav-link {
    transition: all 0.3s ease !important;
    border-radius: 6px !important;
    margin: 1px 8px !important;
    padding: 8px 12px !important;
    border-left: 3px solid transparent !important;
}

.navbar-nav .nav .nav-item .nav-link:hover {
    background: rgba(25, 118, 210, 0.08) !important;
    border-left-color: var(--primary-blue) !important;
    color: var(--primary-blue) !important;
    transform: translateX(3px) !important;
}

.navbar-nav .nav .nav-item .nav-link:hover i {
    color: var(--primary-blue) !important;
}

/* Efectos hover para submenús de tercer nivel */
.navbar-nav .nav .nav .nav-item .nav-link {
    padding: 6px 10px !important;
    margin: 1px 8px !important;
}

.navbar-nav .nav .nav .nav-item .nav-link:hover {
    background: rgba(25, 118, 210, 0.05) !important;
    border-left-color: var(--secondary-blue) !important;
    transform: translateX(2px) !important;
}

/* Efecto para menús expandidos */
.navbar-nav .nav-item > .nav-link[aria-expanded="true"] {
    background: linear-gradient(135deg, var(--primary-blue) 0%, var(--primary-blue-dark) 100%) !important;
    color: white !important;
    box-shadow: 0 4px 16px rgba(25, 118, 210, 0.3) !important;
}

.navbar-nav .nav-item > .nav-link[aria-expanded="true"] i {
    color: white !important;
}

/* ===== DIFERENCIACIÓN ENTRE MENÚS Y VISTAS ===== */

/* Estilos para enlaces que abren menús (tienen data-bs-toggle) */
.navbar-nav .nav-item > .nav-link[data-bs-toggle="collapse"] {
    font-weight: 600 !important;
    position: relative !important;
}

.navbar-nav .nav-item > .nav-link[data-bs-toggle="collapse"]::after {
    content: '▼' !important;
    position: absolute !important;
    right: 16px !important;
    top: 50% !important;
    transform: translateY(-50%) !important;
    font-size: 0.7rem !important;
    transition: transform 0.3s ease !important;
    opacity: 0.6 !important;
}

.navbar-nav .nav-item > .nav-link[data-bs-toggle="collapse"][aria-expanded="true"]::after {
    transform: translateY(-50%) rotate(180deg) !important;
    color: white !important;
    opacity: 1 !important;
}

/* Estilos para enlaces de submenús que abren más submenús */
.navbar-nav .nav .nav-item .nav-link[data-bs-toggle="collapse"] {
    font-weight: 500 !important;
    position: relative !important;
}

.navbar-nav .nav .nav-item .nav-link[data-bs-toggle="collapse"]::after {
    content: '▶' !important;
    position: absolute !important;
    right: 12px !important;
    top: 50% !important;
    transform: translateY(-50%) !important;
    font-size: 0.6rem !important;
    transition: transform 0.3s ease !important;
    opacity: 0.5 !important;
}

.navbar-nav .nav .nav-item .nav-link[data-bs-toggle="collapse"][aria-expanded="true"]::after {
    transform: translateY(-50%) rotate(90deg) !important;
    opacity: 0.8 !important;
}

/* Estilos para enlaces directos a vistas (sin data-bs-toggle) */
.navbar-nav .nav-item .nav-link:not([data-bs-toggle]) {
    font-weight: 400 !important;
    padding-left: 20px !important;
}

.navbar-nav .nav .nav-item .nav-link:not([data-bs-toggle]) {
    padding-left: 16px !important;
    font-size: 0.85rem !important;
}

.navbar-nav .nav .nav .nav-item .nav-link:not([data-bs-toggle]) {
    padding-left: 24px !important;
    font-size: 0.8rem !important;
}

/* Enlace activo (vista actual) */
.active-menu-item {
    background: linear-gradient(135deg, rgba(25, 118, 210, 0.15) 0%, rgba(25, 118, 210, 0.1) 100%) !important;
    color: var(--primary-blue) !important;
    font-weight: 600 !important;
    border-left: 4px solid var(--primary-blue) !important;
    box-shadow: 0 2px 8px rgba(25, 118, 210, 0.2) !important;
    border-radius: 0 8px 8px 0 !important;
    margin-left: 4px !important;
    margin-right: 8px !important;
    position: relative !important;
}

.active-menu-item::after {
    content: '•' !important;
    position: absolute !important;
    right: 8px !important;
    top: 50% !important;
    transform: translateY(-50%) !important;
    color: var(--primary-blue) !important;
    font-size: 1.2rem !important;
    font-weight: bold !important;
    animation: pulse-dot 2s infinite !important;
}

.active-menu-item i {
    color: var(--primary-blue) !important;
    font-weight: 600 !important;
    transform: scale(1.1) !important;
}

.active-menu-item .sidenav-normal {
    color: var(--primary-blue) !important;
    font-weight: 600 !important;
}

/* Animación para el indicador activo */
@keyframes pulse-dot {
    0%, 100% { 
        opacity: 1; 
        transform: translateY(-50%) scale(1); 
    }
    50% { 
        opacity: 0.6; 
        transform: translateY(-50%) scale(1.2); 
    }
}

/* Efecto hover menos intenso para elementos activos */
.active-menu-item:hover {
    transform: translateX(2px) !important;
    background: linear-gradient(135deg, rgba(25, 118, 210, 0.2) 0%, rgba(25, 118, 210, 0.15) 100%) !important;
}

/* Indicador visual para diferenciar tipo de elemento */
.navbar-nav .nav-item .nav-link:not([data-bs-toggle])::before {
    content: '•' !important;
    position: absolute !important;
    left: 8px !important;
    top: 50% !important;
    transform: translateY(-50%) !important;
    color: var(--primary-blue) !important;
    opacity: 0.4 !important;
    font-size: 0.8rem !important;
}

.navbar-nav .nav .nav-item .nav-link:not([data-bs-toggle])::before {
    content: '→' !important;
    left: 4px !important;
    font-size: 0.7rem !important;
}

/* Ajustes responsive para el nuevo ancho del menú */
@media (max-width: 1199px) {
    .main-content {
        margin-left: 0 !important;
    }
    
    .sidenav {
        transform: translateX(-100%);
    }
    
    .g-sidenav-show .sidenav {
        transform: translateX(0);
    }
}

@media (min-width: 1200px) {
    .g-sidenav-show .main-content {
        margin-left: 296px !important;
    }
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    let notificationCount = 0;
    let allNotifications = [];
    
    // Función para cargar el contador de notificaciones
    function loadNotificationCount() {
        fetch('{{ route("notifications.unread-count") }}')
            .then(response => response.json())
            .then(data => {
                notificationCount = parseInt(data.count) || 0;
                const countElement = document.getElementById('notification-count');
                if (countElement) {
                    if (notificationCount > 0) {
                        countElement.textContent = notificationCount;
                        countElement.classList.add('show');
                        countElement.style.display = 'flex';
                        countElement.style.visibility = 'visible';
                    } else {
                        countElement.classList.remove('show');
                        countElement.style.display = 'none';
                        countElement.style.visibility = 'hidden';
                        countElement.textContent = '';
                        countElement.innerHTML = '';
                    }
                }
    
            })
                          .catch(error => {});
    }
    
    // Función para cargar notificaciones no leídas
    function loadUnreadNotifications() {
        fetch('{{ route("notifications.unread") }}')
            .then(response => response.json())
            .then(data => {
                const notificationsList = document.getElementById('notifications-list');
                if (notificationsList) {
                    if (data.notifications.length === 0) {
                        notificationsList.innerHTML = `
                            <div class="no-notifications-preview">
                                <div class="no-notifications-icon-small">
                                    <i class="material-symbols-rounded">notifications_off</i>
                                </div>
                                <p>No hay notificaciones nuevas</p>
                            </div>
                        `;
                    } else {
                        notificationsList.innerHTML = data.notifications.map(notification => `
                            <div class="notification-preview-item ${notification.read_at ? 'read' : 'unread'} ${notification.type}">
                                <div class="notification-preview-content">
                                    <div class="notification-preview-icon">
                                        <i class="material-symbols-rounded">${getNotificationIcon(notification.type)}</i>
                                    </div>
                                    <div class="notification-preview-details">
                                        <div class="notification-preview-header">
                                            <h6 class="notification-preview-title">${notification.title}</h6>
                                            <span class="notification-preview-time">${formatDate(notification.created_at)}</span>
                                        </div>
                                        <p class="notification-preview-message">${notification.message}</p>
                                        ${!notification.read_at ? '<span class="mini-unread-indicator">NUEVO</span>' : ''}
                                    </div>
                                    <div class="notification-preview-actions">
                                        ${!notification.read_at ? `
                                            <button class="btn-mini-action mark-read-btn" 
                                                    data-notification-id="${notification.id}"
                                                    title="Marcar como leída">
                                                <i class="material-symbols-rounded">check</i>
                                            </button>
                                        ` : `
                                            <div class="mini-read-status">
                                                <i class="material-symbols-rounded">done</i>
                                            </div>
                                        `}
                                    </div>
                                </div>
                            </div>
                        `).join('');
                    }
                }
            })
            .catch(error => {});
    }
    
    // Función para cargar todas las notificaciones
    function loadAllNotifications() {
        fetch('{{ route("notifications.all") }}')
            .then(response => response.json())
            .then(data => {
                allNotifications = data.notifications;
                const allNotificationsList = document.getElementById('all-notifications-list');
                if (allNotificationsList) {
                    if (allNotifications.length === 0) {
                        allNotificationsList.innerHTML = `
                            <div class="no-notifications-state">
                                <div class="no-notifications-icon">
                                    <i class="material-symbols-rounded">notifications_off</i>
                                </div>
                                <h6>No tienes notificaciones</h6>
                                <p class="text-muted">Cuando recibas notificaciones, aparecerán aquí</p>
                            </div>
                        `;
                    } else {
                        allNotificationsList.innerHTML = allNotifications.map(notification => `
                            <div class="professional-notification-card ${notification.read_at ? 'read' : 'unread'} ${notification.type}">
                                <div class="notification-card-content">
                                    <div class="notification-icon-type">
                                        <i class="material-symbols-rounded">${getNotificationIcon(notification.type)}</i>
                                    </div>
                                    <div class="notification-details">
                                        <div class="notification-header">
                                            <h6 class="notification-title">${notification.title}</h6>
                                            <span class="notification-time">${formatDate(notification.created_at)}</span>
                                        </div>
                                        <p class="notification-message">${notification.message}</p>
                                        ${!notification.read_at ? '<span class="unread-indicator">NUEVO</span>' : ''}
                                    </div>
                                    <div class="notification-actions">
                                        ${!notification.read_at ? `
                                            <button class="btn-professional-action mark-read-btn" 
                                                    data-notification-id="${notification.id}"
                                                    title="Marcar como leída">
                                                <i class="material-symbols-rounded">check</i>
                                            </button>
                                        ` : `
                                            <div class="read-status">
                                                <i class="material-symbols-rounded">done</i>
                                            </div>
                                        `}
                                    </div>
                                </div>
                            </div>
                        `).join('');
                    }
                }
            })
            .catch(error => {});
    }
    
    // Función para marcar notificación como leída
    function markAsRead(notificationId) {
        fetch('{{ route("notifications.mark-as-read") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({ notification_id: notificationId })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Actualizar contador inmediatamente
                notificationCount = Math.max(0, notificationCount - 1);
                const countElement = document.getElementById('notification-count');
                if (countElement) {
                    if (notificationCount > 0) {
                        countElement.textContent = notificationCount;
                        countElement.classList.add('show');
                        countElement.style.display = 'flex';
                        countElement.style.visibility = 'visible';
                    } else {
                        countElement.classList.remove('show');
                        countElement.style.display = 'none';
                        countElement.style.visibility = 'hidden';
                        countElement.textContent = '';
                        countElement.innerHTML = '';
                    }
                }
                
                // Recargar datos
                loadNotificationCount();
                loadUnreadNotifications();
                if (document.getElementById('allNotificationsModal').classList.contains('show')) {
                    loadAllNotifications();
                }
            }
        })
        .catch(error => {});
    }
    
    // Función para marcar todas como leídas
    function markAllAsRead() {
        fetch('{{ route("notifications.mark-all-as-read") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Actualizar contador inmediatamente a 0
                notificationCount = 0;
                const countElement = document.getElementById('notification-count');
                if (countElement) {
                    countElement.classList.remove('show');
                    countElement.style.display = 'none';
                    countElement.style.visibility = 'hidden';
                    countElement.textContent = '';
                    countElement.innerHTML = '';
                }
                
                // Recargar datos
                loadNotificationCount();
                loadUnreadNotifications();
                if (document.getElementById('allNotificationsModal').classList.contains('show')) {
                    loadAllNotifications();
                }
            }
        })
        .catch(error => {});
    }
    
    // Función para obtener color según tipo de notificación
    function getNotificationColor(type) {
        switch (type) {
            case 'success': return 'success';
            case 'error': return 'danger';
            case 'warning': return 'warning';
            default: return 'info';
        }
    }
    
    // Función para formatear fecha
    function formatDate(dateString) {
        const date = new Date(dateString);
        const now = new Date();
        const diffInSeconds = Math.floor((now - date) / 1000);
        const diffInMinutes = Math.floor(diffInSeconds / 60);
        const diffInHours = Math.floor(diffInMinutes / 60);
        const diffInDays = Math.floor(diffInHours / 24);
        
        if (diffInSeconds < 60) {
            return 'Hace unos segundos';
        } else if (diffInMinutes < 60) {
            return `Hace ${diffInMinutes} minuto${diffInMinutes > 1 ? 's' : ''}`;
        } else if (diffInHours < 24) {
            return `Hace ${diffInHours} hora${diffInHours > 1 ? 's' : ''}`;
        } else if (diffInDays < 7) {
            return `Hace ${diffInDays} día${diffInDays > 1 ? 's' : ''}`;
        } else {
            return date.toLocaleDateString('es-ES', {
                day: '2-digit',
                month: '2-digit',
                year: 'numeric',
                hour: '2-digit',
                minute: '2-digit'
            });
        }
    }
    
    
    // Función para obtener icono según tipo de notificación
    function getNotificationIcon(type) {
        switch (type) {
            case 'success': return 'check_circle';
            case 'error': return 'error';
            case 'warning': return 'warning';
            default: return 'info';
        }
    }
    
    // Event listeners
    document.addEventListener('click', function(e) {
        const markReadButton = e.target.closest('.mark-read-btn');
        if (markReadButton) {
            e.preventDefault();
            e.stopPropagation();
            const notificationId = markReadButton.getAttribute('data-notification-id');
            if (notificationId) {
                markAsRead(notificationId);
            }
        }
    });
    
    // Marcar todas como leídas en dropdown
    document.getElementById('mark-all-read')?.addEventListener('click', function(e) {
        e.preventDefault();
        e.stopPropagation();
        markAllAsRead();
    });
    
    // Marcar todas como leídas en modal
    document.getElementById('modal-mark-all-read')?.addEventListener('click', function(e) {
        e.preventDefault();
        e.stopPropagation();
        markAllAsRead();
    });
    
    // Event listener adicional para botones de marcar como leída
    document.addEventListener('click', function(e) {
        if (e.target.classList.contains('mark-read-btn') || e.target.closest('.mark-read-btn')) {
            e.preventDefault();
            e.stopPropagation();
            const button = e.target.classList.contains('mark-read-btn') ? e.target : e.target.closest('.mark-read-btn');
            const notificationId = button.getAttribute('data-notification-id');
            if (notificationId) {
                markAsRead(notificationId);
            }
        }
    });
    
    // Ver todas las notificaciones
    document.getElementById('view-all-notifications')?.addEventListener('click', function(e) {
        e.preventDefault();
        loadAllNotifications();
        const modal = new bootstrap.Modal(document.getElementById('allNotificationsModal'));
        modal.show();
    });
    
    // Inicializar badge oculto
    const countElement = document.getElementById('notification-count');
    if (countElement) {
        countElement.classList.remove('show');
        countElement.style.display = 'none';
        countElement.style.visibility = 'hidden';
        countElement.textContent = '';
        countElement.innerHTML = '';
    }
    
    // Cargar datos iniciales
    loadNotificationCount();
    loadUnreadNotifications();
    
    // Recargar cada 30 segundos
    setInterval(function() {
        loadNotificationCount();
        loadUnreadNotifications();
    }, 30000);
    
    // Exponer función para mostrar toasts desde otros scripts
    window.showNotificationToast = showNotificationToast;
    

});
</script>
