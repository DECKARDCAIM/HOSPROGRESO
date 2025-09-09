// Panel Principal - Funcionalidades Básicas
document.addEventListener('DOMContentLoaded', function() {
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

    // Deshabilitar botones submit para evitar envíos múltiples
    document.querySelectorAll('form').forEach(function(form) {
        form.addEventListener('submit', function(e) {
            const submitBtn = form.querySelector('[type="submit"]');
            if (submitBtn && !submitBtn.disabled) {
                submitBtn.disabled = true;
                submitBtn.classList.add('disabled');
                
                // Guardar texto original
                if (!submitBtn.dataset.originalText) {
                    submitBtn.dataset.originalText = submitBtn.innerHTML;
                }
                
                // Mostrar spinner
                submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2" role="status text-white"></span>Procesando...';

                // Restaurar el botón si hay error (después de 10 segundos)
                setTimeout(() => {
                    if (submitBtn.dataset.originalText) {
                        submitBtn.innerHTML = submitBtn.dataset.originalText;
                        submitBtn.disabled = false;
                        submitBtn.classList.remove('disabled');
                    }
                }, 10000);
            }
        });
    });

    // Manejo de texto truncado con tooltip
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
});

// Función global para mostrar notificaciones (compatibilidad hacia atrás)
function showNotification(message, type = 'success') {
    const alertDiv = document.createElement('div');
    alertDiv.className = `alert alert-${type} alert-dismissible fade show`;
    alertDiv.innerHTML = `
        ${message}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    `;
    
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
                <div class="spinner-border spinner-border-sm me-2 text-white" role="status">
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

// Función para restaurar botón después de submit
function restoreSubmitButton(form) {
    const submitBtn = form.querySelector('[type="submit"]');
    if (submitBtn && submitBtn.dataset.originalText) {
        submitBtn.innerHTML = submitBtn.dataset.originalText;
        submitBtn.disabled = false;
        submitBtn.classList.remove('disabled');
    }
}



