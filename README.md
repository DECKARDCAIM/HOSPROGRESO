# 🏥 HOSPROGRESO - Sistema de Gestión de Pacientes

<p align="center">
  <a href="https://laravel.com" target="_blank">
    <img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="Laravel Logo">
  </a>
</p>

<p align="center">
  <strong>Sistema Integral de Gestión de Pacientes</strong><br>
  Hospital Nacional de El Progreso, Guastatoya, Guatemala
</p>

---

## 📋 Información del Proyecto

**Desarrollado por:** Cristoffer Alexis Falla Marroquín  
**Carné Universitario:** 1890-21-8820  
**Institución Educativa:** Universidad Mariano Gálvez de Guatemala  
**Hospital:** Hospital Nacional de El Progreso  
**Ubicación:** Guastatoya, El Progreso, Guatemala  

---

## 🎯 Descripción del Sistema

HOSPROGRESO es un sistema integral de gestión hospitalaria diseñado específicamente para el Hospital Nacional de El Progreso. El sistema facilita la administración completa de pacientes, desde el registro inicial hasta el seguimiento médico, incluyendo generación de reportes SIGSA 3H requeridos por el Ministerio de Salud Pública y Asistencia Social (MSPAS) de Guatemala.

### 🌟 Características Principales

- ✅ **Gestión Completa de Historias Clínicas**
- ✅ **Sistema de Citas Médicas Automatizado**
- ✅ **Gestión de Consultas Médicas por Pasos**
- ✅ **Generación de Reportes SIGSA 3H**
- ✅ **Sistema de Notificaciones en Tiempo Real**
- ✅ **Gestión de Usuarios y Roles**
- ✅ **Marcado Automático de Citas Perdidas**
- ✅ **Interfaz Responsiva y Moderna**

---

## 🚀 Funcionalidades del Sistema

### 📋 Módulo de Gestión de Pacientes
- **Historias Clínicas:** Registro completo de pacientes con datos demográficos, médicos y sociales
- **Consultas Médicas:** Proceso estructurado en 4 pasos (Información Básica, Evaluación Médica, Pruebas y Exámenes, Tratamiento)
- **Expedientes Médicos:** Historial completo de consultas y tratamientos

### 📅 Sistema de Citas Médicas
- **Programación Automática:** Asignación inteligente de citas según disponibilidad médica
- **Gestión de Estados:** Pendiente, Confirmada, Atendida, Perdida, Cancelada, Reagendada
- **Notificaciones Automáticas:** Alertas para citas perdidas y cambios de estado
- **Reagendamiento:** Funcionalidad para reprogramar citas

### 👨‍⚕️ Gestión Médica
- **Especialidades Médicas:** Catálogo completo de especialidades
- **Doctores:** Gestión de médicos especialistas y horarios
- **Tipos de Horario:** Configuración flexible de horarios médicos

### 📊 Reportes SIGSA 3H
- **Generación Automática:** Reportes conforme a estándares del MSPAS
- **Filtros Avanzados:** Por fecha, especialidad, tipo de atención
- **Exportación:** Formatos Excel y PDF
- **Estadísticas:** Dashboard con métricas importantes

### 🔐 Administración del Sistema
- **Gestión de Usuarios:** Creación y administración de cuentas
- **Sistema de Roles:** Administrador, Médico, Recepcionista
- **Seguridad:** Autenticación y autorización robusta

### 🔧 Módulo de Mantenimiento

#### 👨‍⚕️ Gestión Médica
- **Especialidades Médicas:** Administración completa de especialidades hospitalarias
- **Doctores:** Gestión de médicos especialistas con asignación de horarios
- **Tipos de Horario:** Configuración flexible de horarios médicos (mañana, tarde, noche)

#### 📊 Catálogos Médicos
- **Sexos:** Masculino, Femenino, Otro (con validación de CUI)
- **Estados Civiles:** Soltero, Casado, Unido, Divorciado, Viudo
- **Comunidades Lingüísticas:** 22 comunidades lingüísticas de Guatemala
- **Etnias:** Maya, Garífuna, Xinca, Ladino/Mestizo, Otro
- **Discapacidades:** Catálogo de tipos de discapacidad física, mental, sensorial
- **Alergias:** Registro de alergias comunes (medicamentos, alimentos, ambientales)

#### 🧪 Estudios y Medicamentos
- **Pruebas de Laboratorio:** Catálogo completo de análisis clínicos
  - Hemograma, Química Sanguínea, Microbiología, Parasitología
  - Inmunología, Hormonas, Marcadores Tumorales
- **Exámenes:** Estudios de gabinete y diagnóstico
  - Rayos X, Ultrasonido, Ecocardiografía, Electrocardiograma
  - Tomografía, Resonancia Magnética, Endoscopias
- **Medicamentos:** Vademécum hospitalario completo
  - Antibióticos, Analgésicos, Antiinflamatorios
  - Medicamentos crónicos, Insulinas, Anticonvulsivantes

#### 🌍 Ubicaciones Geográficas
- **Países:** Gestión de países (principalmente Guatemala)
- **Departamentos:** 22 departamentos de Guatemala con códigos oficiales
- **Municipios:** 340 municipios guatemaltecos con relación departamental
- **Sistema de Cascada:** Selección automática País → Departamento → Municipio

#### 🏥 Roles Hospitalarios
- **Consulta Externa:** Atención ambulatoria
- **Emergencia:** Atención de urgencias médicas
- **Archivo Clínico:** Gestión de expedientes
- **Estadística:** Generación de reportes SIGSA
- **Administrador:** Control total del sistema
- **Encamamiento:** Hospitalización por género
- **Especialidades:** Ginecología, Pediatría, otros
- **Servicios:** Rayos X, Laboratorio, Ecocardiografía, UISAU

### 📑 Tipos de Control SIGSA 3H
- **Prenatal:** Control de embarazo
- **Puerperio:** Atención post-parto
- **Planificación Familiar:** Control anticonceptivo
- **Crecimiento y Desarrollo:** Pediatría preventiva
- **Enfermedades Crónicas:** Diabetes, Hipertensión, etc.

---

## 🛠️ Stack Tecnológico Completo

### Backend (Laravel 12.x)
- **Framework:** Laravel 12.x con PHP 8.2+
- **Base de Datos:** MySQL 8.0+ con Eloquent ORM
- **Autenticación:** Laravel UI con middleware personalizado
- **Migrations:** 34 migraciones para estructura completa
- **Seeders:** Datos iniciales para 13 catálogos principales
- **Middleware:** Autenticación, CSRF, roles de usuario
- **Artisan Commands:** Comandos personalizados para citas perdidas

### Frontend y UI
- **Templates:** Blade Templates con Material Dashboard 3.0
- **CSS Framework:** Bootstrap 5.2.3 con Material Design
- **Preprocesador:** Sass para estilos personalizados
- **Build Tool:** Vite 6.2.4 para compilación de assets
- **Icons:** Material Symbols Rounded + Font Awesome

### Librerías JavaScript Integradas
- **Charts:** Chart.js + AmCharts para gráficos avanzados
- **Forms:** Choices.js para multi-select avanzado
- **UI Components:**
  - SweetAlert para alertas elegantes
  - Perfect Scrollbar para scroll personalizado
  - Flatpickr para date pickers
  - Quill.js para editor de texto rico
  - PhotoSwipe para galería de imágenes
- **Data Tables:** DataTables.js para tablas interactivas
- **Calendar:** FullCalendar para gestión de citas
- **Maps:** Leaflet.js para mapas interactivos
- **3D Graphics:** Three.js para visualizaciones 3D
- **Animations:** Parallax, Tilt effects, CountUp.js
- **File Upload:** Dropzone.js para carga de archivos
- **Form Validation:** Multistep forms con validación

### Generación de Documentos
- **PDF:** DomPDF para reportes y comprobantes
  - Citas médicas con códigos QR
  - Reportes SIGSA 3H oficiales
  - Historias clínicas completas
- **Excel:** Maatwebsite Excel para exportaciones
  - Reportes estadísticos
  - Datos de pacientes
  - Métricas hospitalarias

### Sistema de Notificaciones
- **Toast Notifications:** Sistema personalizado con SweetAlert
- **Real-time:** Notificaciones automáticas en tiempo real
- **Badge System:** Contadores de notificaciones no leídas
- **Types:** Success, Info, Warning, Error con iconografía

### Programación de Tareas
- **Laravel Scheduler:** Automatización de procesos
- **Windows Task Scheduler:** Integración con SO
- **PowerShell Scripts:** Ejecución automatizada
- **Cron Jobs:** Marcado automático de citas perdidas
- **Logs:** Sistema completo de logging y monitoreo

---

## 📦 Instalación y Configuración

### Prerequisitos
```bash
- PHP >= 8.2
- Composer
- MySQL >= 8.0
- Node.js >= 16.x
- NPM
```

### 1. Clonar el Repositorio
```bash
git clone [URL_DEL_REPOSITORIO]
cd HOSPROGRESO
```

### 2. Instalar Dependencias
```bash
# Instalar dependencias de PHP
composer install

# Instalar dependencias de Node.js
npm install
```

### 3. Configuración del Entorno
```bash
# Copiar archivo de configuración
cp .env.example .env

# Generar clave de aplicación
php artisan key:generate
```

### 4. Configurar Base de Datos
Editar el archivo `.env` con los datos de tu base de datos:
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=hosprogreso
DB_USERNAME=tu_usuario
DB_PASSWORD=tu_contraseña
```

### 5. Ejecutar Migraciones y Seeders
```bash
# Crear tablas de la base de datos
php artisan migrate

# Poblar datos iniciales
php artisan db:seed
```

### 6. Configurar Permisos de Almacenamiento
```bash
php artisan storage:link
chmod -R 775 storage/
chmod -R 775 bootstrap/cache/
```

### 7. Compilar Assets
```bash
npm run dev
# O para producción:
npm run build
```

---

## 🕒 Configuración de Tareas Programadas (Cron Jobs)

El sistema incluye tareas automatizadas para el marcado de citas perdidas.

### Configuración Rápida en Windows
```bash
# Ejecutar el scheduler manualmente
php artisan schedule:run
```

---

## 👤 Usuario Administrador por Defecto

Después de ejecutar los seeders, se crea un usuario administrador:
- **Email:** admin@hosprogreso.gob.gt
- **Contraseña:** HosProgreso2024!

**⚠️ IMPORTANTE:** Cambiar estas credenciales en el primer acceso.

---

## 📂 Estructura del Proyecto

```
HOSPROGRESO/
├── app/
│   ├── Console/Commands/          # Comandos de Artisan
│   ├── Http/Controllers/          # Controladores
│   ├── Models/                    # Modelos Eloquent
│   ├── Services/                  # Servicios del sistema
│   └── Exports/                   # Exportadores de datos
├── database/
│   ├── migrations/                # Migraciones de BD
│   └── seeders/                   # Datos iniciales
├── resources/
│   └── views/                     # Vistas Blade
├── public/
│   ├── css/                       # Estilos
│   ├── js/                        # JavaScript
│   └── img/                       # Imágenes
└── routes/
    └── web.php                    # Rutas web
```

---

## 🔧 Comandos Artisan Personalizados

### 🕒 Gestión de Citas
```bash
# Marcar citas perdidas automáticamente
php artisan appointments:mark-missed

# Simulación del marcado (sin guardar cambios)
php artisan appointments:mark-missed --dry-run

# Ver estadísticas de citas del día
php artisan appointments:stats
```

### ⏰ Sistema de Tareas Programadas
```bash
# Ver todas las tareas programadas
php artisan schedule:list

# Ejecutar todas las tareas programadas manualmente
php artisan schedule:run

# Trabajar con el scheduler en modo desarrollo
php artisan schedule:work

# Probar tareas específicas
php artisan schedule:test
```

### 🗃️ Base de Datos y Migraciones
```bash
# Ejecutar migraciones
php artisan migrate

# Rollback de migraciones
php artisan migrate:rollback

# Refrescar base de datos con seeders
php artisan migrate:fresh --seed

# Ejecutar solo seeders específicos
php artisan db:seed --class=AdminUserSeeder
php artisan db:seed --class=CountrySeeder
php artisan db:seed --class=DepartmentSeeder
```

### 🔧 Mantenimiento del Sistema
```bash
# Limpiar caché de la aplicación
php artisan cache:clear

# Limpiar caché de configuración
php artisan config:clear

# Limpiar caché de rutas
php artisan route:clear

# Limpiar caché de vistas
php artisan view:clear

# Optimizar aplicación para producción
php artisan optimize

# Crear enlace simbólico para storage
php artisan storage:link
```

### 📊 Reportes y Estadísticas
```bash
# Generar reporte SIGSA 3H por fechas
php artisan reports:sigsa --from=2024-01-01 --to=2024-12-31

# Estadísticas de consultas médicas
php artisan medical:stats

# Limpiar logs antiguos
php artisan logs:cleanup --days=30
```

### 👥 Gestión de Usuarios
```bash
# Crear usuario administrador
php artisan user:create-admin

# Resetear contraseña de usuario
php artisan user:reset-password [email]

# Listar usuarios activos
php artisan user:list --active

# Desactivar usuarios inactivos
php artisan user:cleanup --inactive-days=90
```

---

## 📈 Funcionalidades Avanzadas

### 🔔 Sistema de Notificaciones Inteligente
- **Tiempo Real:** Notificaciones automáticas sin recargar página
- **Tipos de Alerta:** Success, Info, Warning, Error con iconografía específica
- **Citas Perdidas:** Detección automática cada 2 horas con notificación
- **Reagendamiento:** Alertas cuando se reprograman citas médicas
- **Badge Counter:** Contadores visuales de notificaciones no leídas
- **Sistema Toast:** Mensajes elegantes con SweetAlert integrado

### 📊 Reportes SIGSA 3H Avanzados
- **Estándares MSPAS:** Cumplimiento total con formatos oficiales guatemaltecos
- **Filtros Dinámicos:** Por especialidad, médico, fecha, tipo de atención
- **Exportación Multi-formato:** Excel con formato oficial + PDF personalizable
- **Estadísticas en Tiempo Real:** Dashboard con métricas hospitalarias
- **Validación de Datos:** Verificación automática antes de generar reportes
- **Membrete Institucional:** Headers oficiales del Hospital Nacional

### 🤖 Gestión de Citas con IA
- **Asignación Inteligente:** Algoritmo que encuentra el próximo cupo disponible
- **Validación Temporal:** Previene agendamiento en horarios pasados
- **Sistema Anti-colisión:** Evita doble agendamiento del mismo médico
- **Reagendamiento Automático:** Reasigna citas canceladas a nuevos slots
- **Tolerancia de 2 Horas:** Grace period antes de marcar cita como perdida
- **Estados Dinámicos:** Pendiente → Confirmada → Atendida (con lógica de negocio)

### 🏥 Consultas Médicas Multi-paso
- **Wizard de 4 Pasos:** Información Básica → Evaluación → Pruebas → Tratamiento
- **Guardado Automático:** Cada paso se guarda automáticamente
- **Validación por Paso:** Control de calidad en cada etapa
- **Sistema de Retroceso:** Navegación libre entre pasos completados
- **Finalización Inteligente:** Validación completa antes de cerrar consulta
- **Integración SIGSA:** Datos listos para reportes gubernamentales

### 🔐 Sistema de Roles Hospitalarios
- **13 Roles Predefinidos:** Desde Consulta Externa hasta UISAU
- **Middleware Personalizado:** Control de acceso por ruta y función
- **Permisos Granulares:** Acceso específico según área hospitalaria
- **Validación de CUI:** Integración con número de identificación guatemalteco
- **Activación/Desactivación:** Gestión de usuarios sin eliminar datos

### 🌐 Ubicación con Cascada Inteligente
- **22 Departamentos:** Todos los departamentos oficiales de Guatemala
- **340 Municipios:** Base de datos completa de municipios guatemaltecos
- **Selección en Cascada:** País → Departamento → Municipio automático
- **Validación Geográfica:** Verificación de coherencia en ubicaciones
- **Datos del INE:** Información oficial del Instituto Nacional de Estadística

---

## 🛡️ Seguridad y Protección de Datos

### 🔐 Autenticación y Autorización
- **Laravel UI:** Sistema de login robusto con throttling
- **Middleware Personalizado:** `CheckUserAccess` para control granular
- **Hash Seguro:** Bcrypt para encriptación de contraseñas
- **Session Management:** Control de sesiones activas por usuario
- **Remember Me:** Funcionalidad de recordar credenciales segura

### 🛡️ Protección de Aplicación
- **CSRF Protection:** Tokens en todos los formularios
- **XSS Prevention:** Escape automático en templates Blade
- **SQL Injection:** Eloquent ORM con consultas preparadas
- **Input Validation:** Validación server-side en todos los endpoints
- **Sanitización:** Limpieza automática de datos de entrada

### 👥 Control de Roles y Permisos
- **Role-Based Access:** 13 roles específicos hospitalarios
- **Middleware por Ruta:** Protección específica por funcionalidad
- **Estado de Usuario:** Activación/desactivación sin pérdida de datos
- **Auditoría:** Logs de acciones críticas del sistema
- **CUI Validation:** Validación de número de identificación guatemalteco

### 🔒 Seguridad de Datos Médicos
- **HIPAA Compliance:** Protección de datos médicos sensibles
- **Soft Deletes:** Eliminación lógica para mantener historial
- **Encriptación de Datos:** Información médica protegida
- **Backup Automático:** Respaldos programados de la base de datos
- **Logs de Acceso:** Registro de acceso a historias clínicas

---

## 🌟 Características Únicas del Sistema

### 🏥 Especialización Hospitalaria Guatemalteca
- **SIGSA 3H Nativo:** Diseñado específicamente para reportes del MSPAS
- **Datos Oficiales:** Departamentos y municipios según INE Guatemala
- **Comunidades Lingüísticas:** 22 idiomas oficiales guatemaltecos
- **CUI Validation:** Validación de Código Único de Identificación
- **Roles Hospitalarios:** Estructura específica del sector salud público

### 🔄 Automatización Inteligente
- **Citas Perdidas:** Detección automática cada 2 horas
- **Scheduler Integrado:** Tareas programadas con Windows Task Scheduler
- **Notificaciones Proactivas:** Alertas antes de problemas críticos
- **Backup Automático:** Respaldos programados de datos críticos
- **Logs Centralizados:** Sistema unificado de monitoreo

### 📊 Business Intelligence
- **Dashboard Ejecutivo:** Métricas en tiempo real para dirección
- **Indicadores KPI:** Seguimiento de objetivos hospitalarios
- **Tendencias:** Análisis predictivo de demanda de servicios
- **Alertas Tempranas:** Detección de anomalías en patrones de atención
- **Reportes Ejecutivos:** Información estratégica para toma de decisiones

### 🎯 Optimización de Procesos
- **Flujo Multi-paso:** Consultas médicas estructuradas en 4 etapas
- **Validación en Tiempo Real:** Verificación inmediata de datos
- **Sistema de Cascadas:** Selecciones dependientes automáticas
- **Reutilización de Datos:** Información compartida entre módulos
- **Reducción de Errores:** Validaciones múltiples y controles de calidad

---

## 📱 Compatibilidad y Rendimiento

### 🌐 Navegadores Soportados
- **Chrome:** 90+ (Recomendado para mejor rendimiento)
- **Firefox:** 88+ (Completamente compatible)
- **Safari:** 14+ (macOS/iOS)
- **Edge:** 90+ (Windows 10/11)

### 📱 Responsive Design
- **Desktop:** Optimizado para pantallas 1920x1080 y superiores
- **Tablet:** Compatible con iPad y tablets Android (768px+)
- **Móvil:** Funcional en smartphones (360px+)
- **Print:** Layouts específicos para impresión de reportes

### ⚡ Rendimiento
- **Carga Inicial:** <3 segundos en conexiones estándar
- **Navegación:** Transiciones instantáneas entre módulos
- **Base de Datos:** Consultas optimizadas con índices estratégicos
- **Assets:** Compresión y minificación automática con Vite
- **Caché:** Sistema de caché inteligente para datos frecuentes

---

## 🤝 Soporte y Mantenimiento

Para soporte técnico o consultas sobre el sistema:

**Desarrollador:** Cristoffer Alexis Falla Marroquín  
**Universidad:** Mariano Gálvez de Guatemala  
**Institución:** Hospital Nacional de El Progreso  

---

## 📄 Licencia

Este proyecto está desarrollado para uso exclusivo del Hospital Nacional de El Progreso como parte del programa académico de la Universidad Mariano Gálvez de Guatemala.

---

## 🙏 Agradecimientos

- Hospital Nacional de El Progreso
- Universidad Mariano Gálvez de Guatemala
- Ministerio de Salud Pública y Asistencia Social (MSPAS)
- Framework Laravel y su comunidad

---

<p align="center">
  <strong>Desarrollado con ❤️ para el Hospital Nacional de El Progreso</strong><br>
  Sistema de Gestión Hospitalaria - Guatemala 2025
</p>
