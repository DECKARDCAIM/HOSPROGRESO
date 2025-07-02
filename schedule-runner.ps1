# Script de PowerShell para ejecutar Laravel Scheduler
# Archivo: schedule-runner.ps1
# Ejecutar con: PowerShell -ExecutionPolicy Bypass -File "C:\laragon\www\HOSPROGRESO\schedule-runner.ps1"

# Configuracion de rutas
$projectPath = "C:\laragon\www\HOSPROGRESO"
$phpCommand = "php"  # Usar PHP desde el PATH (Laragon ya lo configura)
$logFile = "$projectPath\storage\logs\scheduler.log"

# Asegurar que el directorio de logs existe
$logDir = Split-Path $logFile -Parent
if (-not (Test-Path $logDir)) {
    New-Item -ItemType Directory -Path $logDir -Force | Out-Null
}

# Funcion para escribir logs
function Write-Log {
    param($Message)
    $timestamp = Get-Date -Format "yyyy-MM-dd HH:mm:ss"
    $logMessage = "[$timestamp] $Message"
    Write-Output $logMessage
    Add-Content -Path $logFile -Value $logMessage
}

try {
    Write-Log "=== INICIANDO SCHEDULER ==="
    
    # Cambiar al directorio del proyecto
    Set-Location $projectPath
    Write-Log "Directorio cambiado a: $projectPath"
    
    # Verificar que PHP esta disponible
    try {
        $phpVersion = & $phpCommand --version 2>&1
        Write-Log "PHP disponible: $($phpVersion[0])"
    } catch {
        throw "PHP no esta disponible en el PATH. Asegurate de que Laragon este ejecutandose."
    }
    
    # Ejecutar el Laravel Scheduler
    Write-Log "Ejecutando Laravel Scheduler..."
    $result = & $phpCommand artisan schedule:run --verbose 2>&1
    
    # Log del resultado
    Write-Log "Resultado del scheduler:"
    $result | ForEach-Object { Write-Log "  $_" }
    
    # Verificar si hubo errores
    if ($LASTEXITCODE -eq 0) {
        Write-Log "Scheduler ejecutado exitosamente"
    } else {
        Write-Log "Error en scheduler. Codigo de salida: $LASTEXITCODE"
    }
    
    Write-Log "=== SCHEDULER COMPLETADO ==="
    
} catch {
    Write-Log "ERROR CRITICO: $($_.Exception.Message)"
    Write-Log "Stack trace: $($_.ScriptStackTrace)"
} finally {
    Write-Log "=== FIN DE EJECUCION ==="
} 