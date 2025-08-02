// Sistema de Notificaciones Toast
document.addEventListener('DOMContentLoaded', function() {
    // Función global para mostrar toast notifications
    window.showToast = function(type, title, message, duration = 4000) {
        const toastContainer = document.querySelector('.toast-container');
        if (!toastContainer) return;

        const icons = {
            success: 'bi-check-circle-fill',
            error: 'bi-exclamation-triangle-fill',
            info: 'bi-info-circle-fill',
            warning: 'bi-exclamation-triangle-fill'
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
                    <i class="bi ${icons[type] ?? 'bi-info-circle'} me-2"></i>
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
    };

    // Función específica para notificaciones de éxito
    window.showSuccessToast = function(message, title = 'Éxito') {
        return showToast('success', title, message);
    };

    // Función específica para notificaciones de error
    window.showErrorToast = function(message, title = 'Error') {
        return showToast('error', title, message);
    };

    // Función específica para notificaciones de información
    window.showInfoToast = function(message, title = 'Información') {
        return showToast('info', title, message);
    };

    // Función específica para notificaciones de advertencia
    window.showWarningToast = function(message, title = 'Advertencia') {
        return showToast('warning', title, message);
    };

    // Procesamiento de toasts desde sesión de Laravel
    function processLaravelToasts() {
        if (window.laravelToastData) {
            showToast(
                window.laravelToastData.type ?? 'info',
                window.laravelToastData.title ?? 'Notificación',
                window.laravelToastData.message ?? '',
                5000
            );
        }
    }

    // Ejecutar al cargar la página
    processLaravelToasts();
}); 