@extends('layouts.panel')

@section('title', 'Base de Datos')
@section('breadcrumb', 'Base de Datos')

@section('content')
<div class="container-fluid py-4">
    <!-- Sección de Importación -->
    <div class="row mb-4">
        <div class="col-12">
<div class="card">
                <div class="card-header bg-info text-white">
                    <div class="row align-items-center">
                        <div class="col-md-8">
                            <h6 class="text-white mb-0">
                                <i class="bi bi-file-earmark-excel me-2"></i>
            Importar Datos desde Excel
                            </h6>
                            <p class="text-sm text-white opacity-8 mb-0">
                                Importa registros de pacientes desde archivos Excel al sistema.
                            </p>
    </div>
                        <div class="col-md-4 text-end">
                            <button type="button" class="btn btn-sm btn-white" id="headerSubmitBtn" disabled onclick="submitForm()">
                                <i class="bi bi-upload me-2"></i>Importar Datos
                            </button>
                </div>
            </div>
        </div>

                <div class="card-body">
                <form action="{{ route('import.process') }}" method="POST" enctype="multipart/form-data" id="importForm">
                    @csrf
                    
                        <div class="row">
                            <div class="col-12">
                    <div class="form-group mb-4">
                                    <label for="excel_file" class="form-label fw-bold">
                                        <i class="bi bi-file-earmark-excel text-success me-2"></i>
                            Seleccionar Archivo Excel
                        </label>
                        <input type="file" 
                                           class="form-control form-control-lg @error('excel_file') is-invalid @enderror" 
                               id="excel_file" 
                               name="excel_file" 
                               accept=".xlsx,.xls"
                                           required
                                           onchange="validateFile(this)">
                        @error('excel_file')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <div class="form-text">
                                        <i class="bi bi-info-circle me-1"></i>
                            Formatos permitidos: .xlsx, .xls. Tamaño máximo: 50MB
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Sección de Backup -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header bg-info text-white">
                    <div class="row align-items-center">
                        <div class="col-md-8">
                            <h6 class="text-white mb-0">
                                <i class="bi bi-database-fill me-2"></i>
                                Backup de Base de Datos
                            </h6>
                            <p class="text-sm text-white opacity-8 mb-0">
                                Genera copias de seguridad de toda la información del sistema.
                            </p>
                        </div>
                    </div>
                </div>
                
                <div class="card-body">
                                        <div class="row justify-content-center">
                        <div class="col-md-6 mb-3">
                            <div class="card border-info h-100">
                                <div class="card-body text-center">
                                    <div class="icon icon-shape bg-info shadow text-center border-radius-md mb-3">
                                        <i class="bi bi-database-fill text-white text-lg"></i>
                                    </div>
                                    <h6 class="card-title">Backup Completo</h6>
                                    <p class="card-text text-sm">Copia completa de toda la base de datos. Incluye todos los datos y estructura. Ideal para migración y restauración completa del sistema.</p>
                                    <button type="button" class="btn btn-info btn-sm" onclick="generateBackup('full')">
                                        <i class="bi bi-download me-2"></i>Generar Backup Completo
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row mt-4">
                        <div class="col-12">
                            <div class="alert alert-info border border-info text-white">
                                <i class="bi bi-info-circle me-2"></i>
                                <strong>Información importante:</strong> El backup se generará en formato SQL y se descargará automáticamente. 
                                Incluye toda la estructura de tablas y datos del sistema. Este archivo puede ser importado en cualquier servidor MySQL 
                                para restaurar completamente el sistema.
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal de carga con barra de progreso -->
<div class="modal fade" id="loadingModal" tabindex="-1" data-bs-backdrop="static" data-bs-keyboard="false">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-body text-center">
                <div class="spinner-border text-info mb-3" role="status">
                    <span class="visually-hidden">Procesando...</span>
                </div>
                <h5 id="modalTitle">Procesando archivo Excel...</h5>
                <p class="text-muted mb-3" id="modalMessage">Este proceso puede tomar varios minutos dependiendo del tamaño del archivo.</p>
                
                <!-- Barra de progreso -->
                <div class="progress mb-3" style="height: 10px;">
                    <div class="progress-bar progress-bar-striped progress-bar-animated bg-info" 
                         role="progressbar" 
                         id="progressBar" 
                         style="width: 0%"></div>
                </div>
                <small class="text-muted" id="progressText">Iniciando procesamiento...</small>
            </div>
        </div>
    </div>
</div>

<!-- Modal de Backup -->
<div class="modal fade" id="backupModal" tabindex="-1" data-bs-backdrop="static" data-bs-keyboard="false">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-gradient-info text-white">
                <h5 class="modal-title text-white">
                    <i class="bi bi-database-fill me-2 text-white"></i>
                    Generando Backup
                </h5> 
            </div>
            <div class="modal-body text-center">
                <div class="spinner-border text-info mb-3" role="status">
                    <span class="visually-hidden">Generando backup...</span>
                </div>
                <h5 id="backupModalTitle">Generando backup de la base de datos...</h5>
                <p class="text-muted mb-3" id="backupModalMessage">Este proceso puede tomar varios minutos dependiendo del tamaño de la base de datos.</p>
                
                <!-- Barra de progreso -->
                <div class="progress mb-3" style="height: 10px;">
                    <div class="progress-bar progress-bar-striped progress-bar-animated bg-info" 
                         role="progressbar" 
                         id="backupProgressBar" 
                         style="width: 0%"></div>
                </div>
                <small class="text-muted" id="backupProgressText">Iniciando generación de backup...</small>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
// Función para validar archivo
function validateFile(input) {
    const file = input.files[0];
    const headerSubmitBtn = document.getElementById('headerSubmitBtn');
    
    if (!file) {
        headerSubmitBtn.disabled = true;
        return;
    }
    
    // Validar tipo de archivo
    const allowedTypes = [
        'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet', // .xlsx
        'application/vnd.ms-excel', // .xls
        'application/octet-stream' // Para algunos navegadores
    ];
    
    const isValidType = allowedTypes.includes(file.type) || file.name.match(/\.(xlsx|xls)$/i);
    
    if (!isValidType) {
        if (typeof showToast !== 'undefined') {
            showToast('error', 'Error', 'Por favor selecciona un archivo Excel válido (.xlsx o .xls)');
        } else {
            alert('Por favor selecciona un archivo Excel válido (.xlsx o .xls)');
        }
        input.value = '';
        headerSubmitBtn.disabled = true;
        return;
    }
    
    // Validar tamaño (50MB máximo)
    const maxSize = 50 * 1024 * 1024; // 50MB en bytes
    if (file.size > maxSize) {
        if (typeof showToast !== 'undefined') {
            showToast('error', 'Error', 'El archivo es demasiado grande. El tamaño máximo es 50MB.');
        } else {
            alert('El archivo es demasiado grande. El tamaño máximo es 50MB.');
        }
        input.value = '';
        headerSubmitBtn.disabled = true;
        return;
    }
    
    // Si todo está bien, habilitar el botón
    headerSubmitBtn.disabled = false;
}

// Función para enviar el formulario
function submitForm() {
    const fileInput = document.getElementById('excel_file');
    const file = fileInput.files[0];
    
    if (!file) {
        if (typeof showToast !== 'undefined') {
            showToast('error', 'Error', 'Por favor selecciona un archivo Excel');
        } else {
            alert('Por favor selecciona un archivo Excel');
        }
        return;
    }
    
    // Validar que el archivo sea un Excel válido
    const allowedTypes = [
        'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet', // .xlsx
        'application/vnd.ms-excel', // .xls
        'application/octet-stream' // Para algunos navegadores
    ];
    
    if (!allowedTypes.includes(file.type) && !file.name.match(/\.(xlsx|xls)$/i)) {
        if (typeof showToast !== 'undefined') {
            showToast('error', 'Error', 'Por favor selecciona un archivo Excel válido (.xlsx o .xls)');
        } else {
            alert('Por favor selecciona un archivo Excel válido (.xlsx o .xls)');
        }
        return;
    }
    
    // Mostrar modal de carga
    const modal = new bootstrap.Modal(document.getElementById('loadingModal'));
    modal.show();
    
    // Deshabilitar botón de envío y cambiar texto
    const headerSubmitBtn = document.getElementById('headerSubmitBtn');
    headerSubmitBtn.disabled = true;
    headerSubmitBtn.innerHTML = '<i class="bi bi-hourglass-split me-2"></i>Procesando...';
    
    // Calcular progreso basado en el archivo
    const fileSize = file.size;
    const fileSizeMB = (fileSize / (1024 * 1024)).toFixed(2);
    
    // Estimar tiempo basado en tamaño del archivo
    let estimatedTime = 0;
    if (fileSizeMB < 1) {
        estimatedTime = 3000;
    } else if (fileSizeMB < 5) {
        estimatedTime = 8000;
    } else if (fileSizeMB < 10) {
        estimatedTime = 15000;
    } else {
        estimatedTime = 25000;
    }
    
    // Simular progreso hasta 100%
    const progressBar = document.getElementById('progressBar');
    const progressText = document.getElementById('progressText');
    const modalTitle = document.getElementById('modalTitle');
    const modalMessage = document.getElementById('modalMessage');
    let progress = 0;
    const startTime = Date.now();
    let reached100 = false;
    
    const progressInterval = setInterval(() => {
        const elapsed = Date.now() - startTime;
        const estimatedProgress = Math.min((elapsed / estimatedTime) * 100, 100);
        
        // Solo subir, nunca bajar
        if (estimatedProgress > progress) {
            progress = estimatedProgress;
        }
        
        // Asegurar que no baje del 100% una vez que llegue
        if (reached100) {
            progress = 100;
        }
        
        progressBar.style.width = progress + '%';
        progressText.textContent = `Procesando archivo (${fileSizeMB} MB)... ${Math.round(progress)}%`;
        
        // Al llegar al 100%, cambiar mensaje y mantener cargando
        if (progress >= 100 && !reached100) {
            progress = 100;
            progressBar.style.width = '100%';
            progressText.textContent = `Procesando archivo (${fileSizeMB} MB)... 100%`;
            
            // Cambiar mensaje al 100%
            modalTitle.textContent = 'Importando datos...';
            modalMessage.textContent = 'Estamos importando los datos a la base de datos. Esto puede tomar tiempo dependiendo de la cantidad de registros que desea importar.';
            reached100 = true;
            
            // Mantener cargando hasta que termine la importación real
            // El modal se cerrará automáticamente cuando se complete la respuesta del servidor
        }
    }, 150);
    
    // Limpiar intervalo después del tiempo estimado
    setTimeout(() => {
        clearInterval(progressInterval);
    }, estimatedTime + 3000);
    
    // Enviar el formulario
    document.getElementById('importForm').submit();
}

// Función para generar backup
function generateBackup(type) {
    const backupModal = new bootstrap.Modal(document.getElementById('backupModal'));
    backupModal.show();
    
    const backupModalTitle = document.getElementById('backupModalTitle');
    const backupModalMessage = document.getElementById('backupModalMessage');
    const backupProgressBar = document.getElementById('backupProgressBar');
    const backupProgressText = document.getElementById('backupProgressText');
    
    // Solo manejar backup completo
    const title = 'Generando Backup Completo...';
    const message = 'Creando una copia completa de toda la base de datos. Esto puede tomar varios minutos dependiendo del tamaño de los datos.';
    const estimatedTime = 30000;
    const route = '{{ route("import.backup.full") }}';
    
    backupModalTitle.textContent = title;
    backupModalMessage.textContent = message;
    
    // Simular progreso
    let progress = 0;
    const startTime = Date.now();
    let reached100 = false;
    
    const progressInterval = setInterval(() => {
        const elapsed = Date.now() - startTime;
        const estimatedProgress = Math.min((elapsed / estimatedTime) * 100, 100);
        
        if (estimatedProgress > progress) {
            progress = estimatedProgress;
        }
        
        if (reached100) {
            progress = 100;
        }
        
        backupProgressBar.style.width = progress + '%';
        backupProgressText.textContent = `Generando backup... ${Math.round(progress)}%`;
        
        if (progress >= 100 && !reached100) {
            progress = 100;
            backupProgressBar.style.width = '100%';
            backupProgressText.textContent = 'Finalizando backup... 100%';
            reached100 = true;
            
            // Realizar la petición al servidor
            setTimeout(() => {
                window.location.href = route;
                backupModal.hide();
            }, 2000);
        }
    }, 150);
    
    setTimeout(() => {
        clearInterval(progressInterval);
    }, estimatedTime + 3000);
}


</script>
@endpush