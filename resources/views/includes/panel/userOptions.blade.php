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
            <a class="nav-link text-dark position-relative" href="javascript:;" id="notificationDropdown" role="button"
                data-bs-toggle="dropdown" data-bs-strategy="fixed" aria-expanded="false">
                <i class="material-symbols-rounded">notifications</i>
                <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger" id="notification-count" style="display: none;">
                    0
                </span>
            </a>
            <ul class="dropdown-menu dropdown-menu-end shadow p-2" aria-labelledby="notificationDropdown"
                style="min-width: 350px; max-height: 400px; overflow-y: auto;">
                <li class="dropdown-header d-flex justify-content-between align-items-center">
                    <span class="fw-bold">Notificaciones</span>
                    <button class="btn btn-sm btn-outline-secondary" id="mark-all-read">Marcar todas</button>
                </li>
                <li><hr class="dropdown-divider"></li>
                <div id="notifications-list">
                    <!-- Las notificaciones se cargarán dinámicamente aquí -->
                </div>
                <li><hr class="dropdown-divider"></li>
                <li>
                    <a class="dropdown-item text-center text-info" href="javascript:;" id="view-all-notifications">
                        <strong>Ver todas las notificaciones</strong>
                    </a>
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
<div class="modal fade" id="allNotificationsModal" tabindex="-1" aria-labelledby="allNotificationsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="allNotificationsModalLabel">Todas las Notificaciones</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <span class="text-muted">Mostrando las últimas 30 notificaciones</span>
                    <button class="btn btn-sm btn-outline-primary" id="modal-mark-all-read">Marcar todas como leídas</button>
                </div>
                <div id="all-notifications-list">
                    <!-- Todas las notificaciones se cargarán aquí -->
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>

<!-- Toast Container para alertas -->
<div class="toast-container position-fixed top-0 end-0 p-3" style="z-index: 1055;">
    <!-- Los toasts se agregarán dinámicamente aquí -->
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    let notificationCount = 0;
    let allNotifications = [];
    
    // Función para cargar el contador de notificaciones
    function loadNotificationCount() {
        fetch('{{ route("notifications.unread-count") }}')
            .then(response => response.json())
            .then(data => {
                notificationCount = data.count;
                const countElement = document.getElementById('notification-count');
                if (countElement) {
                    countElement.textContent = notificationCount;
                    countElement.style.display = notificationCount > 0 ? 'block' : 'none';
                }
            })
            .catch(error => console.error('Error cargando contador:', error));
    }
    
    // Función para cargar notificaciones no leídas
    function loadUnreadNotifications() {
        fetch('{{ route("notifications.unread") }}')
            .then(response => response.json())
            .then(data => {
                const notificationsList = document.getElementById('notifications-list');
                if (notificationsList) {
                    if (data.notifications.length === 0) {
                        notificationsList.innerHTML = '<li><span class="dropdown-item text-muted">No hay notificaciones nuevas</span></li>';
                    } else {
                        notificationsList.innerHTML = data.notifications.map(notification => `
                            <li>
                                <a class="dropdown-item small text-wrap notification-item" href="javascript:;" 
                                   data-notification-id="${notification.id}">
                                    <div class="d-flex justify-content-between align-items-start">
                                        <div class="flex-grow-1">
                                            <div class="fw-bold text-${getNotificationColor(notification.type)}">${notification.title}</div>
                                            <div class="text-muted">${notification.message}</div>
                                            <small class="text-muted">${formatDate(notification.created_at)}</small>
                                        </div>
                                        <button class="btn btn-sm btn-outline-secondary ms-2 mark-read-btn" 
                                                data-notification-id="${notification.id}">
                                            <i class="material-symbols-rounded" style="font-size: 16px;">check</i>
                                        </button>
                                    </div>
                                </a>
                            </li>
                        `).join('');
                    }
                }
            })
            .catch(error => console.error('Error cargando notificaciones:', error));
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
                        allNotificationsList.innerHTML = '<p class="text-muted text-center">No hay notificaciones</p>';
                    } else {
                        allNotificationsList.innerHTML = allNotifications.map(notification => `
                            <div class="card mb-2 ${notification.read_at ? 'bg-light' : 'border-primary'}">
                                <div class="card-body p-3">
                                    <div class="d-flex justify-content-between align-items-start">
                                        <div class="flex-grow-1">
                                            <h6 class="card-title mb-1 text-${getNotificationColor(notification.type)}">${notification.title}</h6>
                                            <p class="card-text mb-1">${notification.message}</p>
                                            <small class="text-muted">${formatDate(notification.created_at)}</small>
                                        </div>
                                        ${!notification.read_at ? `
                                            <button class="btn btn-sm btn-outline-primary mark-read-btn" 
                                                    data-notification-id="${notification.id}">
                                                <i class="material-symbols-rounded" style="font-size: 16px;">check</i>
                                            </button>
                                        ` : ''}
                                    </div>
                                </div>
                            </div>
                        `).join('');
                    }
                }
            })
            .catch(error => console.error('Error cargando todas las notificaciones:', error));
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
                loadNotificationCount();
                loadUnreadNotifications();
                if (document.getElementById('allNotificationsModal').classList.contains('show')) {
                    loadAllNotifications();
                }
            }
        })
        .catch(error => console.error('Error marcando como leída:', error));
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
                loadNotificationCount();
                loadUnreadNotifications();
                if (document.getElementById('allNotificationsModal').classList.contains('show')) {
                    loadAllNotifications();
                }
            }
        })
        .catch(error => console.error('Error marcando todas como leídas:', error));
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
        const diffInHours = Math.floor((now - date) / (1000 * 60 * 60));
        
        if (diffInHours < 1) {
            return 'Hace unos minutos';
        } else if (diffInHours < 24) {
            return `Hace ${diffInHours} hora${diffInHours > 1 ? 's' : ''}`;
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
            markAsRead(notificationId);
        }
    });
    
    // Marcar todas como leídas en dropdown
    document.getElementById('mark-all-read')?.addEventListener('click', function(e) {
        e.preventDefault();
        markAllAsRead();
    });
    
    // Marcar todas como leídas en modal
    document.getElementById('modal-mark-all-read')?.addEventListener('click', function(e) {
        e.preventDefault();
        markAllAsRead();
    });
    
    // Ver todas las notificaciones
    document.getElementById('view-all-notifications')?.addEventListener('click', function(e) {
        e.preventDefault();
        loadAllNotifications();
        const modal = new bootstrap.Modal(document.getElementById('allNotificationsModal'));
        modal.show();
    });
    
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
