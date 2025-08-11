@extends('layouts.panel')

@section('title', 'Importar Datos Temporales')
@section('breadcrumb', 'Importar Datos')

@section('content')
<meta name="csrf-token" content="{{ csrf_token() }}">

<div class="container-fluid py-4">
    <!-- Sección de Importar Datos -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header bg-success text-white">
                    <div class="row align-items-center">
                        <div class="col-md-8">
                            <h6 class="text-white mb-0">
                                <i class="bi bi-file-earmark-excel me-2"></i> Importar Datos Temporales
                            </h6>
                            <p class="text-sm text-white opacity-8 mb-0">
                                Importa registros temporales de pacientes desde archivos Excel.
                            </p>
                        </div>
                        <div class="col-md-4 text-end">
                            @can('import.importar')
                                <button
                                    type="button"
                                    class="btn btn-sm btn-white"
                                    id="headerSubmitBtn"
                                    disabled
                                    onclick="submitForm()">
                                    <i class="bi bi-upload me-2"></i>Importar Temporales
                                </button>
                            @else
                                <button
                                    type="button"
                                    class="btn btn-sm btn-secondary"
                                    disabled
                                    title="Solo administradores pueden importar">
                                    <i class="bi bi-lock me-2"></i>Solo Administradores
                                </button>
                            @endcan
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    @can('import.importar')
                        {{-- Selector de archivo visible --}}
                        <div class="mb-3">
                            <label for="excel_file" class="form-label fw-bold">
                                <i class="bi bi-file-earmark-excel text-success me-2"></i>
                                Seleccionar Archivo Excel
                            </label>
                            <input
                                type="file"
                                class="form-control form-control-lg"
                                id="excel_file"
                                name="excel_file"
                                accept=".xlsx,.xls"
                                onchange="validateFile(this)"
                                required>
                        </div>
                        
                        {{-- Form oculto para el envío AJAX --}}
                        <form
                            id="importForm"
                            action="{{ route('import.process') }}"
                            method="POST"
                            enctype="multipart/form-data"
                            style="display:none;">
                            @csrf
                        </form>
                        
                        <div class="text-muted">
                            <i class="bi bi-info-circle me-1"></i>
                            Formatos: .xlsx, .xls | Tamaño máximo: 50 MB
                        </div>
                    @else
                        <div class="text-center py-5">
                            <i class="bi bi-person-gear fs-1 text-secondary"></i>
                            <h5 class="mt-3">Función solo para Administradores</h5>
                            <p class="text-muted">
                                Contacta al administrador para importar datos.
                            </p>
                        </div>
                    @endcan
                </div>
            </div>
        </div>
    </div>

    <!-- Sección de Exportar Datos -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header bg-info text-white">
                    <div class="row align-items-center">
                        <div class="col-md-8">
                            <h6 class="text-white mb-0">
                                <i class="bi bi-download me-2"></i> Exportar Base de Datos
                            </h6>
                            <p class="text-sm text-white opacity-8 mb-0">
                                Descarga copia de seguridad completa del sistema.
                            </p>
                        </div>
                        <div class="col-md-4 text-end">
                            @can('import.importar')
                                <button
                                    type="button"
                                    class="btn btn-sm btn-white"
                                    onclick="generateBackup('full')">
                                    <i class="bi bi-download me-2"></i>Exportar ahora
                                </button>
                            @else
                                <button
                                    type="button"
                                    class="btn btn-sm btn-secondary"
                                    disabled>
                                    <i class="bi bi-lock me-2"></i>Solo Administradores
                                </button>
                            @endcan
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="alert alert-info mb-0">
                        <i class="bi bi-info-circle me-2"></i>
                        La exportación se generará en formato SQL y se descargará automáticamente.
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal: Progreso de Importación con estilo del buscador global -->
<div id="loadingModal" class="search-modal" style="display:none; padding-top:14vh;">
  <div class="search-modal-overlay"></div>
  <div class="search-modal-content" style="max-width: 520px;">
    <div class="search-header">
      <div class="search-icon"><i class="bi bi-file-earmark-excel"></i></div>
      <div class="text-white fw-bold">Importando Archivo</div>
      <button class="search-close ms-auto" id="closeImportModal" type="button"><i class="bi bi-x"></i></button>
    </div>
    <div class="search-results" style="max-height:none; padding: 24px;">
      <div class="text-center">
        <div class="d-flex justify-content-center">
          <div class="spinner-border text-info mb-3" role="status">
            <span class="visually-hidden">Procesando...</span>
          </div>
        </div>
        <h5 id="modalTitle">Procesando archivo Excel...</h5>
        <p id="modalMessage" class="text-muted mb-3">Preparando archivo para importación...</p>
        <div class="progress mb-2" style="height:10px;">
          <div id="progressBar" class="progress-bar progress-bar-striped progress-bar-animated bg-info" role="progressbar" style="width:0%; transition:width .3s ease-out;"></div>
        </div>
        <small id="progressText" class="text-info fw-bold">Iniciando...</small>
        <div><small id="processingSpeed" class="text-muted"></small></div>
      </div>
    </div>
  </div>
</div>

<!-- Modal: Progreso de Backup con mismo estilo del buscador global -->
<div id="backupModal" class="search-modal" style="display:none; padding-top:14vh;">
  <div class="search-modal-overlay"></div>
  <div class="search-modal-content" style="max-width:520px;">
    <div class="search-header">
      <div class="search-icon"><i class="bi bi-database-fill"></i></div>
      <div class="text-white fw-bold">Generando Backup</div>
      <button class="search-close ms-auto" id="closeBackupModal" type="button"><i class="bi bi-x"></i></button>
    </div>
    <div class="search-results" style="max-height:none; padding:24px;">
      <div class="text-center">
        <div class="spinner-border text-info mb-3" role="status"><span class="visually-hidden">Generando backup...</span></div>
        <h5 id="backupModalTitle">Generando backup de la base de datos...</h5>
        <p class="text-muted mb-3" id="backupModalMessage">Este proceso puede tomar varios minutos dependiendo del tamaño de la base de datos.</p>
        <div class="progress mb-3" style="height:10px;">
          <div class="progress-bar progress-bar-striped progress-bar-animated bg-info" role="progressbar" id="backupProgressBar" style="width:0%"></div>
        </div>
        <small class="text-muted" id="backupProgressText">Iniciando generación de backup...</small>
      </div>
    </div>
  </div>
</div>
@endsection

@push('scripts')
<script>
// Variables globales
let sessionId, progressInterval, startTime, lastProgressUpdate = 0;

// Valida extensión y tamaño del archivo
function validateFile(input) {
  const file = input.files[0];
  const btn = document.getElementById('headerSubmitBtn');
  
  if (!file) {
    btn.disabled = true;
    return;
  }
  
  const validExt = /\.(xlsx|xls)$/i.test(file.name);
  const validSize = file.size <= 50 * 1024 * 1024; // 50MB
  
  if (!validExt || !validSize) {
    alert('Por favor selecciona un archivo Excel válido (.xlsx o .xls) menor a 50MB.');
    input.value = '';
    btn.disabled = true;
    return;
  }
  
  btn.disabled = false;
}

// Inicia la importación por AJAX
async function submitForm() {
  const fileInput = document.getElementById('excel_file');
  const file = fileInput.files[0];
  
  if (!file) {
    alert('Por favor selecciona un archivo Excel.');
    return;
  }
  
  // Genera sessionId único
  sessionId = 'import_' + Date.now() + '_' + Math.random().toString(36).substr(2, 9);
  
  // Deshabilita botón y muestra modal
  const btn = document.getElementById('headerSubmitBtn');
  btn.disabled = true;
  btn.innerHTML = '<i class="bi bi-hourglass-split me-2"></i>Procesando...';
  
  // Muestra modal de progreso
  // Mostrar modal con mismo estilo que buscador global
  document.getElementById('loadingModal').style.display = 'flex';
  document.getElementById('loadingModal').classList.add('show');
  
  // Inicializa tracking de tiempo
  startTime = Date.now();
  lastProgressUpdate = 0;
  
  // Inicia el polling de progreso ANTES de enviar
  startRealProgressMonitoring();
  
  // Prepara FormData con el archivo real del input visible
  const formData = new FormData();
  formData.append('_token', document.querySelector('meta[name="csrf-token"]').content);
  formData.append('excel_file', file);
  formData.append('session_id', sessionId);
  
  try {
    // Envía por AJAX
    const response = await fetch('{{ route("import.process") }}', {
      method: 'POST',
      body: formData
    });
    
    if (!response.ok) {
      console.error('Error al iniciar importación:', await response.text());
    }
  } catch (error) {
    console.error('Error en la importación:', error);
  }
}

// Polling MUY AGRESIVO cada 100ms para capturar CADA cambio
function startRealProgressMonitoring() {
  progressInterval = setInterval(async () => {
    try {
      // Agregar timestamp para evitar cache del navegador
      const timestamp = Date.now();
      const response = await fetch(`{{ route('import.progress') }}?session_id=${sessionId}&t=${timestamp}`, {
        cache: 'no-cache',
        headers: {
          'Cache-Control': 'no-cache',
          'Pragma': 'no-cache'
        }
      });
      
      const data = await response.json();
      
      updateProgressUI(data);
      
      // Si termina, detener polling
      if (['completed', 'error'].includes(data.status)) {
        clearInterval(progressInterval);
        
        if (data.status === 'completed') {
          // Esperar 3 segundos antes de cerrar
          setTimeout(() => {
            const lm = document.getElementById('loadingModal');
            lm.classList.remove('show');
            lm.style.display = 'none';
            
            // Restablecer botón
            const btn = document.getElementById('headerSubmitBtn');
            btn.disabled = false;
            btn.innerHTML = '<i class="bi bi-upload me-2"></i>Importar Temporales';
            
            // Limpiar input de archivo
            document.getElementById('excel_file').value = '';
            btn.disabled = true;
          }, 3000);
        }
      }
    } catch (error) {
      console.error('Error consultando progreso:', error);
    }
  }, 100); // polling rápido para UI fluida
}

// Actualiza la UI del progreso
function updateProgressUI(data) {
  const progressBar = document.getElementById('progressBar');
  const progressText = document.getElementById('progressText');
  const modalTitle = document.getElementById('modalTitle');
  const modalMessage = document.getElementById('modalMessage');
  const processingSpeed = document.getElementById('processingSpeed');
  
  // Actualiza barra con transición suave
  progressBar.style.width = data.percentage + '%';
  
  switch (data.status) {
    case 'loading':
      modalTitle.textContent = 'Cargando archivo Excel...';
      progressText.textContent = 'Preparando archivo...';
      modalMessage.textContent = data.message || 'Leyendo estructura del archivo Excel...';
      processingSpeed.textContent = '';
      break;
      
    case 'processing':
      modalTitle.textContent = 'Importando registros...';
      
      // Formato prominente del contador: 125/700 (17.9%)
      if (data.current && data.total) {
        progressText.textContent = `${data.current}/${data.total} (${data.percentage}%)`;
        modalMessage.textContent = `Procesando e importando registros a la base de datos.`;
        
        // Calcular velocidad solo si ha pasado tiempo suficiente
        const elapsed = (Date.now() - startTime) / 1000;
        if (elapsed > 2 && data.current > lastProgressUpdate) {
          const recordsPerSecond = (data.current / elapsed).toFixed(1);
          const remainingRecords = data.total - data.current;
          const estimatedSeconds = Math.round(remainingRecords / (data.current / elapsed));
          
          let speedText = `⚡ ${recordsPerSecond} registros/seg`;
          
          if (estimatedSeconds > 0 && estimatedSeconds < 3600) {
            const minutes = Math.floor(estimatedSeconds / 60);
            const seconds = estimatedSeconds % 60;
            
            if (minutes > 0) {
              speedText += ` • ⏱️ ${minutes}m ${seconds}s restantes`;
            } else {
              speedText += ` • ⏱️ ${seconds}s restantes`;
            }
          }
          
          processingSpeed.textContent = speedText;
          lastProgressUpdate = data.current;
        } else if (elapsed <= 2) {
          processingSpeed.textContent = '⏳ Calculando velocidad...';
        }
      } else {
        progressText.textContent = `${data.percentage}%`;
        modalMessage.textContent = data.message || 'Procesando registros...';
      }
      break;
      
    case 'completed':
      modalTitle.textContent = '¡Importación completada exitosamente!';
      
      if (data.current && data.total) {
        progressText.textContent = `${data.total}/${data.total} (100%)`;
        modalMessage.textContent = `¡Proceso completado! Se importaron ${data.total} registros correctamente.`;
      } else {
        progressText.textContent = '100%';
        modalMessage.textContent = data.message || '¡Importación completada exitosamente!';
      }
      
      processingSpeed.textContent = '✅ ¡Completado!';
      break;
      
    case 'error':
      modalTitle.textContent = 'Error en la importación';
      progressText.textContent = 'Error';
      modalMessage.textContent = data.message || 'Ocurrió un error durante el proceso.';
      processingSpeed.textContent = '❌ Error';
      progressBar.classList.remove('bg-info');
      progressBar.classList.add('bg-danger');
      break;
  }
}

// Cerrar modal manualmente si el usuario lo desea
document.getElementById('closeImportModal')?.addEventListener('click', () => {
  const lm = document.getElementById('loadingModal');
  lm.classList.remove('show');
  lm.style.display = 'none';
});

// Función para generar backup
function generateBackup(type) {
    const bm = document.getElementById('backupModal');
    bm.style.display = 'flex';
    bm.classList.add('show');
    
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
                bm.classList.remove('show');
                bm.style.display = 'none';
            }, 2000);
        }
    }, 150);
    
    setTimeout(() => {
        clearInterval(progressInterval);
    }, estimatedTime + 3000);
}

// Cerrar modal de backup manualmente
document.getElementById('closeBackupModal')?.addEventListener('click', () => {
  const bm = document.getElementById('backupModal');
  bm.classList.remove('show');
  bm.style.display = 'none';
});
</script>
@endpush