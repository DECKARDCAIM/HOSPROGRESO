# Módulo de Gestión Médica - HOSPROGRESO

## Descripción

Este módulo completo permite la gestión de expedientes clínicos, historias clínicas y consultas médicas para el sistema HOSPROGRESO. Incluye todos los catálogos necesarios y las funcionalidades principales para el registro y seguimiento de pacientes.

## Características Principales

### 🔹 Módulo de Datos del Paciente
- **Fecha de consulta**: Captura automática de fecha y hora del sistema
- **Número de historia clínica**: Generación automática de IDs únicos
- **Datos personales completos**: Nombres, apellidos, CUI, sexo, estado civil
- **Información demográfica**: Comunidad lingüística, etnia, ubicación geográfica
- **Condición médica**: Discapacidades y alergias (opcionales)
- **Escolaridad y ocupación**: Campos de texto libre

### 🔹 Módulo de Consulta Médica
- **Doctor tratante**: Asociación con especialidad médica
- **Detalle clínico**: Motivo, diagnóstico, notas de enfermería e ingreso
- **Recetas y estudios**: Medicamentos, laboratorios y exámenes
- **Referencia y contrarreferencia**: Campo condicional

## Estructura de la Base de Datos

### Tablas de Catálogos
- `sexes` - Sexo del paciente
- `civil_statuses` - Estado civil
- `linguistic_communities` - Comunidades lingüísticas
- `ethnicities` - Etnias
- `disabilities` - Discapacidades
- `allergies` - Alergias
- `laboratory_tests` - Pruebas de laboratorio
- `exams` - Exámenes médicos
- `medications` - Medicamentos

### Tablas Principales
- `doctors` - Información de doctores
- `clinical_records` - Expedientes clínicos
- `patients` - Historias clínicas
- `medical_consultations` - Consultas médicas

### Tablas Pivot
- `medical_consultation_laboratory_test` - Relación consultas-laboratorios
- `medical_consultation_exam` - Relación consultas-exámenes
- `medical_consultation_medication` - Relación consultas-medicamentos

## Instalación

### 1. Ejecutar Migraciones
```bash
php artisan migrate
```

### 2. Ejecutar Seeders
```bash
php artisan db:seed
```

### 3. Verificar Rutas
Las siguientes rutas estarán disponibles:

#### Catálogos
- `GET /sexes` - Gestión de sexos
- `GET /civil-statuses` - Gestión de estados civiles
- `GET /linguistic-communities` - Gestión de comunidades lingüísticas
- `GET /ethnicities` - Gestión de etnias
- `GET /disabilities` - Gestión de discapacidades
- `GET /allergies` - Gestión de alergias
- `GET /laboratory-tests` - Gestión de pruebas de laboratorio
- `GET /exams` - Gestión de exámenes
- `GET /medications` - Gestión de medicamentos

#### Módulos Principales
- `GET /doctors` - Gestión de doctores
- `GET /clinical-records` - Gestión de expedientes clínicos
- `GET /patients` - Gestión de historias clínicas
- `GET /medical-consultations` - Gestión de consultas médicas

## Flujo de Trabajo

### 1. Configuración Inicial
1. Crear especialidades médicas en `/especialidades`
2. Registrar doctores en `/doctors`
3. Configurar catálogos según necesidades

### 2. Registro de Pacientes
1. Crear expediente clínico en `/clinical-records`
2. Generar historia clínica en `/patients`
3. Registrar consulta médica en `/medical-consultations`

### 3. Seguimiento
- Cada paciente puede tener múltiples consultas
- Un expediente clínico puede tener una historia clínica
- Las consultas se asocian automáticamente con fecha y hora

## Validaciones Implementadas

### Expediente Clínico
- CUI único y obligatorio
- Fecha de nacimiento válida
- Cálculo automático de edad
- Ubicación geográfica jerárquica

### Historia Clínica
- Un expediente = Una historia clínica
- Número de historia único automático

### Consulta Médica
- Fecha y hora automática
- Asociación obligatoria con paciente y doctor
- Especialidad automática según doctor

## Características Técnicas

### Relaciones de Base de Datos
- **Uno a Muchos**: Expediente → Historias clínicas
- **Uno a Muchos**: Paciente → Consultas médicas
- **Muchos a Muchos**: Consultas ↔ Laboratorios, Exámenes, Medicamentos

### Validaciones
- Campos requeridos marcados con *
- Formatos de CUI validados
- Fechas de nacimiento coherentes
- Relaciones jerárquicas de ubicación

### Seguridad
- Middleware de autenticación en todas las rutas
- Validación de datos en controladores
- Protección CSRF en formularios

## Archivos Creados

### Migraciones (16 archivos)
- `2025_01_27_000001_create_sexes_table.php`
- `2025_01_27_000002_create_civil_statuses_table.php`
- `2025_01_27_000003_create_linguistic_communities_table.php`
- `2025_01_27_000004_create_ethnicities_table.php`
- `2025_01_27_000005_create_disabilities_table.php`
- `2025_01_27_000006_create_allergies_table.php`
- `2025_01_27_000007_create_laboratory_tests_table.php`
- `2025_01_27_000008_create_exams_table.php`
- `2025_01_27_000009_create_medications_table.php`
- `2025_01_27_000010_create_doctors_table.php`
- `2025_01_27_000011_create_clinical_records_table.php`
- `2025_01_27_000012_create_patients_table.php`
- `2025_01_27_000013_create_medical_consultations_table.php`
- `2025_01_27_000014_create_medical_consultation_laboratory_test_table.php`
- `2025_01_27_000015_create_medical_consultation_exam_table.php`
- `2025_01_27_000016_create_medical_consultation_medication_table.php`

### Modelos (12 archivos)
- `Sex.php`, `CivilStatus.php`, `LinguisticCommunity.php`, `Ethnicity.php`
- `Disability.php`, `Allergy.php`, `LaboratoryTest.php`, `Exam.php`, `Medication.php`
- `Doctor.php`, `ClinicalRecord.php`, `Patient.php`, `MedicalConsultation.php`

### Controladores (12 archivos)
- Controladores para cada modelo con operaciones CRUD completas

### Vistas
- Vistas index, create y edit para cada módulo
- Formularios completos con validación
- Interfaz consistente con el diseño existente

### Seeders (8 archivos)
- Datos iniciales para todos los catálogos
- Información básica para pruebas

## Notas de Desarrollo

### Escalabilidad
- Diseño modular para fácil extensión
- Catálogos reutilizables y administrables
- Relaciones bien definidas para consultas complejas

### Mantenimiento
- Código documentado y estructurado
- Validaciones centralizadas
- Mensajes de error descriptivos

### Rendimiento
- Paginación en listados
- Carga eager de relaciones
- Índices en campos de búsqueda

## Próximos Pasos

1. **Implementar búsqueda avanzada** en expedientes y consultas
2. **Agregar reportes** de consultas por período
3. **Implementar notificaciones** para citas y seguimientos
4. **Crear dashboard** con estadísticas médicas
5. **Agregar exportación** de datos a PDF/Excel

## Soporte

Para dudas o problemas técnicos, revisar:
- Logs de Laravel en `storage/logs/`
- Validaciones en controladores
- Relaciones en modelos
- Configuración de base de datos 