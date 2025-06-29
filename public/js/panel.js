document.addEventListener('DOMContentLoaded', function() {
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl)
    });

    // Auto-cerrar alertas después de 5 segundos
    setTimeout(function() {
        var alertElement = document.getElementById('notification-alert');
        if (alertElement) {
            var alert = bootstrap.Alert.getInstance(alertElement);
            if (alert) {
                alert.close();
            } else {
                alertElement.classList.remove('show');
                setTimeout(function() {
                    alertElement.remove();
                }, 150);
            }
        }
    }, 5000);

    // Deshabilitar botones submit en todos los formularios para evitar envíos múltiples
    document.querySelectorAll('form').forEach(function(form) {
        form.addEventListener('submit', function(e) {
            // Busca el botón submit dentro del formulario
            const submitBtn = form.querySelector('[type="submit"]');
            if (submitBtn && !submitBtn.disabled) {
                submitBtn.disabled = true;
                submitBtn.classList.add('disabled');
                // Guarda el texto original para restaurar si es necesario
                if (!submitBtn.dataset.originalText) {
                    submitBtn.dataset.originalText = submitBtn.innerHTML;
                }
                submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2" role="status"></span>Procesando...';
            }
        });
    });
});

// Mostrar el ícono solo si el texto está truncado visualmente
document.querySelectorAll('.desc-truncada').forEach(function(p) {
    var span = p.querySelector('.desc-text');
    var icon = p.querySelector('.info-icon');
    if (span && icon) {
        if (span.offsetWidth < span.scrollWidth) {
            icon.style.display = 'inline-block';
        } else {
            icon.style.display = 'none';
        }
    }
});

// Función global para mostrar notificaciones
function showNotification(message, type = 'success') {
    const alertDiv = document.createElement('div');
    alertDiv.className = `alert alert-${type} alert-dismissible fade show`;
    alertDiv.innerHTML = `
        ${message}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    `;
    
    // Insertar al inicio del contenido
    const content = document.querySelector('.container-fluid');
    if (content) {
        content.insertBefore(alertDiv, content.firstChild);
        
        // Auto-remover después de 5 segundos
        setTimeout(() => {
            if (alertDiv.parentNode) {
                alertDiv.remove();
            }
        }, 5000);
    }
}

// Función para mostrar toast de carga
function showLoadingToast(message = 'Procesando...') {
    const toastDiv = document.createElement('div');
    toastDiv.className = 'position-fixed top-0 end-0 p-3';
    toastDiv.style.zIndex = '9999';
    toastDiv.innerHTML = `
        <div class="toast show" role="alert">
            <div class="toast-header">
                <div class="spinner-border spinner-border-sm me-2" role="status">
                    <span class="visually-hidden">Cargando...</span>
                </div>
                <strong class="me-auto">Procesando</strong>
                <button type="button" class="btn-close" data-bs-dismiss="toast"></button>
            </div>
            <div class="toast-body">
                ${message}
            </div>
        </div>
    `;
    
    document.body.appendChild(toastDiv);
    return toastDiv;
}

// Función para ocultar toast de carga
function hideLoadingToast(toastElement) {
    if (toastElement && toastElement.parentNode) {
        toastElement.remove();
    }
}

