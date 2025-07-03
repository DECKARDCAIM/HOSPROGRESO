# 🕒 Configuración de Cron Job para Hospital Progreso

## ✅ **Estado del Sistema**
- ✅ Laravel Scheduler configurado
- ✅ Script PowerShell creado y probado
- ✅ Archivo BAT creado y probado
- ✅ Logs funcionando correctamente

## 📋 **Tareas Programadas**
El sistema ejecuta las siguientes tareas automáticamente:

### 🏥 **Verificación de Citas Perdidas**
- **Cada hora**: Revisión general de citas perdidas
- **Cada 30 minutos**: Durante horarios laborales (6:00 AM - 8:00 PM)
- **Tolerancia**: 2 horas después de la hora programada
- **Acciones**: Marca como "perdida" y crea notificación automática

## 🛠️ **Configuración en Windows Task Scheduler**

### **Paso 1: Abrir Task Scheduler**
1. Presiona `Win + R` y escribe `taskschd.msc`
2. Click en "Aceptar"

### **Paso 2: Crear Nueva Tarea**
1. En el panel derecho, click en **"Crear tarea..."**
2. En la pestaña **"General"**:
   - **Nombre**: `Hospital Progreso - Laravel Scheduler`
   - **Descripción**: `Ejecuta tareas programadas de Laravel para gestión de citas médicas`
   - ✅ Marcar: "Ejecutar tanto si el usuario está conectado como si no"
   - ✅ Marcar: "Ejecutar con los privilegios más altos"
   - **Configurar para**: Windows 10/11

### **Paso 3: Configurar Desencadenador**
1. Pestaña **"Desencadenadores"** → Click **"Nuevo..."**
2. **Iniciar la tarea**: Al programarse
3. **Configuración**:
   - ✅ Marcar: "Diariamente"
   - **Inicio**: Hoy
   - **Hora de inicio**: 06:00:00
   - ✅ Marcar: "Repetir tarea cada": 1 minuto
   - **Durante**: 1 día
   - ✅ Marcar: "Habilitado"

### **Paso 4: Configurar Acción**
1. Pestaña **"Acciones"** → Click **"Nueva..."**
2. **Acción**: Iniciar un programa
3. **Programa o script**: `C:\laragon\www\HOSPROGRESO\run-scheduler.bat`
4. **Iniciar en**: `C:\laragon\www\HOSPROGRESO`

### **Paso 5: Configurar Condiciones**
1. Pestaña **"Condiciones"**:
   - ❌ Desmarcar: "Iniciar la tarea solo si el equipo está en corriente alterna"
   - ❌ Desmarcar: "Detener si el equipo pasa a la batería"
   - ✅ Marcar: "Activar el equipo para ejecutar esta tarea"

### **Paso 6: Configurar Configuración**
1. Pestaña **"Configuración"**:
   - ✅ Marcar: "Permitir que la tarea se ejecute a petición"
   - ✅ Marcar: "Ejecutar la tarea lo antes posible después de una programación perdida"
   - ✅ Marcar: "Si la tarea en ejecución no termina cuando se solicita, forzar su detención"
   - **Si la tarea ya se está ejecutando**: "No iniciar una nueva instancia"

### **Paso 7: Finalizar**
1. Click **"Aceptar"**
2. Ingresa tu contraseña de administrador si se solicita

## 🔍 **Verificación del Funcionamiento**

### **Prueba Manual**
```cmd
# Navegar al directorio del proyecto
cd C:\laragon\www\HOSPROGRESO

# Ejecutar manualmente
run-scheduler.bat
```

### **Verificar Logs**
```cmd
# Ver últimas 20 líneas del log
Get-Content "storage\logs\scheduler.log" -Tail 20

# Ver log completo
Get-Content "storage\logs\scheduler.log"
```

### **Verificar en Task Scheduler**
1. Abrir Task Scheduler
2. Buscar la tarea "Hospital Progreso - Laravel Scheduler"
3. Click derecho → "Ejecutar" para probar
4. Revisar el **"Historial"** para ver ejecuciones

## 📊 **Monitoreo**

### **Archivos de Log**
- **Scheduler**: `storage/logs/scheduler.log`
- **Laravel**: `storage/logs/laravel.log`
- **Cron Appointments**: `storage/logs/cron-appointments.log`

### **Comandos de Monitoreo**
```bash
# Ver citas que serán marcadas como perdidas (simulación)
php artisan appointments:mark-missed --dry-run

# Ejecutar manualmente el marcado
php artisan appointments:mark-missed

# Ver schedules configurados
php artisan schedule:list

# Probar scheduler manualmente
php artisan schedule:run
```

## ⚠️ **Solución de Problemas**

### **Error: "PHP no está disponible"**
- Asegurate de que Laragon esté ejecutándose
- Verifica que PHP esté en el PATH

### **Error: "Acceso denegado"**
- Ejecuta Task Scheduler como administrador
- Asegurate de que la tarea tenga privilegios elevados

### **La tarea no se ejecuta**
1. Verifica que Laragon esté iniciado
2. Revisa el historial en Task Scheduler
3. Ejecuta manualmente `run-scheduler.bat`
4. Revisa los logs en `storage/logs/`

### **Citas no se marcan como perdidas**
- Verifica que haya citas con más de 2 horas de atraso
- Ejecuta: `php artisan appointments:mark-missed --dry-run`
- Revisa logs de Laravel para errores

## 🚀 **¡Listo!**

El sistema ahora funcionará automáticamente cada minuto, pero el Laravel Scheduler solo ejecutará las tareas cuando corresponda según la programación:

- ⏰ **Cada hora**: Verificación general
- ⏰ **Cada 30 min**: Durante horarios laborales (6 AM - 8 PM)
- 📧 **Notificaciones**: Emails en caso de errores
- 📝 **Logs**: Registro completo de todas las ejecuciones

¡El Hospital Progreso ahora tiene un sistema completamente automático para gestionar las citas perdidas! 