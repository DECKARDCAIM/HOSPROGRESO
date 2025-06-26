document.addEventListener('DOMContentLoaded', function() {
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl)
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

