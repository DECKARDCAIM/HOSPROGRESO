<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Credenciales de Acceso - {{ $user->name }}</title>
    <style>
        @page {
            margin: 20mm 15mm 30mm 15mm;
            size: A4;
        }
        
        body {
            font-family: DejaVu Sans, Arial, sans-serif;
            font-size: 12px;
            line-height: 1.4;
            color: #333;
            margin: 0;
            padding: 0;
            height: 100vh;
            page-break-after: always;
        }
        
        .page-container {
            height: 100%;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }
        
        .content-wrapper {
            flex: 1;
            padding: 0 5mm;
        }
        
        .header {
            text-align: center;
            border-bottom: 2px solid #2563eb;
            padding-bottom: 15px;
            margin-bottom: 20px;
            background: #f8fafc;
            padding: 15px;
            border-radius: 8px;
        }
        
        .logo {
            font-size: 22px;
            font-weight: bold;
            color: #2563eb;
            margin-bottom: 5px;
        }
        
        .subtitle {
            font-size: 13px;
            color: #666;
            margin-bottom: 8px;
        }
        
        .document-number {
            font-size: 16px;
            font-weight: bold;
            background-color: #2563eb;
            color: white;
            padding: 8px 15px;
            border-radius: 5px;
            display: inline-block;
            margin-top: 8px;
        }
        
        .section {
            margin-bottom: 20px;
            page-break-inside: avoid;
            border: 1px solid #e5e7eb;
            border-radius: 6px;
            padding: 15px;
            background: #ffffff;
        }
        
        .section-title {
            font-size: 14px;
            font-weight: bold;
            color: #2563eb;
            border-bottom: 2px solid #2563eb;
            padding-bottom: 5px;
            margin-bottom: 12px;
            background: #f1f5f9;
            padding: 8px 12px;
            margin: -15px -15px 12px -15px;
            border-radius: 5px 5px 0 0;
        }
        
        .info-grid {
            display: table;
            width: 100%;
            border-collapse: collapse;
        }
        
        .info-row {
            display: table-row;
        }
        
        .info-label {
            display: table-cell;
            font-weight: bold;
            width: 35%;
            padding: 5px 15px 5px 0;
            vertical-align: top;
            border-bottom: 1px solid #f1f5f9;
        }
        
        .info-value {
            display: table-cell;
            padding: 5px 0;
            vertical-align: top;
            border-bottom: 1px solid #f1f5f9;
        }
        
         .credentials-section {
             margin-bottom: 20px;
             page-break-inside: avoid;
             border: 1px solid #e5e7eb;
             border-radius: 6px;
             padding: 15px;
             background: #ffffff;
         }
         
         .credentials-section .section-title {
             font-size: 14px;
             font-weight: bold;
             color: #2563eb;
             border-bottom: 2px solid #2563eb;
             padding-bottom: 5px;
             margin-bottom: 12px;
             background: #f1f5f9;
             padding: 8px 12px;
             margin: -15px -15px 12px -15px;
             border-radius: 5px 5px 0 0;
         }
         
         .credential-item {
             margin-bottom: 10px;
             display: flex;
             align-items: center;
         }
         
         .credential-label {
             font-weight: bold;
             width: 120px;
             color: #333;
         }
         
         .credential-value {
             flex: 1;
             padding: 8px 12px;
             background: #f8fafc;
             border-radius: 4px;
             font-family: 'Courier New', monospace;
             font-size: 13px;
             border: 1px solid #e5e7eb;
             font-weight: bold;
             color: #333;
         }
         
         .instructions-section {
             margin-bottom: 20px;
             page-break-inside: avoid;
             border: 1px solid #e5e7eb;
             border-radius: 6px;
             padding: 15px;
             background: #ffffff;
         }
         
         .instructions-section .section-title {
             font-size: 14px;
             font-weight: bold;
             color: #2563eb;
             border-bottom: 2px solid #2563eb;
             padding-bottom: 5px;
             margin-bottom: 12px;
             background: #f1f5f9;
             padding: 8px 12px;
             margin: -15px -15px 12px -15px;
             border-radius: 5px 5px 0 0;
         }
         
         .step-list {
             list-style: none;
             counter-reset: step-counter;
             margin: 0;
             padding: 0;
         }
         
         .step-list li {
             counter-increment: step-counter;
             margin-bottom: 8px;
             padding-left: 40px;
             position: relative;
             font-size: 11px;
             line-height: 1.4;
         }
         
         .step-list li::before {
             content: counter(step-counter);
             position: absolute;
             left: 0;
             top: 0;
             background: #2563eb;
             color: white;
             width: 25px;
             height: 25px;
             border-radius: 50%;
             display: flex;
             align-items: center;
             justify-content: center;
             font-weight: bold;
             font-size: 11px;
         }
         
         .warning-section {
             margin-bottom: 20px;
             page-break-inside: avoid;
             border: 1px solid #e5e7eb;
             border-radius: 6px;
             padding: 15px;
             background: #ffffff;
             text-align: center;
         }
         
         .warning-section .section-title {
             font-size: 14px;
             font-weight: bold;
             color: #2563eb;
             border-bottom: 2px solid #2563eb;
             padding-bottom: 5px;
             margin-bottom: 12px;
             background: #f1f5f9;
             padding: 8px 12px;
             margin: -15px -15px 12px -15px;
             border-radius: 5px 5px 0 0;
         }
         
         .warning-text {
             color: #333;
             font-size: 11px;
             line-height: 1.4;
             margin: 0;
         }
        
        .generated-info {
            text-align: right;
            font-size: 10px;
            color: #666;
            margin-top: 15px;
            padding: 8px;
            background: #f8fafc;
            border-radius: 4px;
        }
        
        .footer {
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            height: 25mm;
            background: #2563eb;
            color: white;
            text-align: center;
            padding: 8px 0;
            font-size: 10px;
            border-top: 2px solid #1e40af;
        }
        
        .footer-content {
            margin-bottom: 5px;
        }
        
        .page-number {
            position: absolute;
            bottom: 5px;
            right: 15px;
            font-weight: bold;
        }
        
        /* Ajustes específicos para impresión */
        @media print {
            body { 
                margin: 0; 
                padding: 0;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }
            .footer { 
                position: fixed; 
                bottom: 0; 
            }
            .page-container {
                height: 100vh;
                page-break-after: always;
            }
        }
        
        /* Evitar cortes de página en secciones importantes */
        .section, .credentials-section, .instructions-section, .warning-section {
            page-break-inside: avoid;
        }
    </style>
</head>
<body>
    <div class="page-container">
        <div class="content-wrapper">
            <!-- Header -->
            <div class="header">
                <div class="logo">HOSPITAL NACIONAL DE PROGRESO</div>
                <div class="subtitle">Sistema de Registro de Pacientes - HOSPROGRESO</div>
                <div class="document-number">CREDENCIALES DE ACCESO</div>
            </div>

            <!-- Información del Usuario -->
            <div class="section">
                <div class="section-title">Información del Usuario</div>
                <div class="info-grid">
                    <div class="info-row">
                        <div class="info-label">Nombre Completo:</div>
                        <div class="info-value">{{ $user->name }}</div>
                    </div>
                    <div class="info-row">
                        <div class="info-label">Correo Electrónico:</div>
                        <div class="info-value">{{ $user->email }}</div>
                    </div>
                    <div class="info-row">
                        <div class="info-label">Rol en el Sistema:</div>
                        <div class="info-value">{{ $user->role->name ?? 'Sin rol asignado' }}</div>
                    </div>
                    <div class="info-row">
                        <div class="info-label">Estado:</div>
                        <div class="info-value">{{ $user->is_active ? 'Activo' : 'Inactivo' }}</div>
                    </div>
                    @if($user->cui)
                    <div class="info-row">
                        <div class="info-label">CUI:</div>
                        <div class="info-value">{{ $user->cui }}</div>
                    </div>
                    @endif
                    @if($user->phone)
                    <div class="info-row">
                        <div class="info-label">Teléfono:</div>
                        <div class="info-value">{{ $user->phone }}</div>
                    </div>
                    @endif
                </div>
            </div>

             <!-- Credenciales de Acceso -->
             <div class="credentials-section">
                 <div class="section-title">🔐 Credenciales de Acceso</div>
                 <div class="info-grid">
                     <div class="info-row">
                         <div class="info-label">Usuario/Email:</div>
                         <div class="info-value">{{ $user->email }}</div>
                     </div>
                     <div class="info-row">
                         <div class="info-label">Contraseña:</div>
                         <div class="info-value">{{ $plainPassword }}</div>
                     </div>
                 </div>
             </div>

             <!-- Instrucciones para Cambiar Contraseña -->
             <div class="instructions-section">
                 <div class="section-title">📋 Instrucciones para Cambiar la Contraseña</div>
                 <div class="info-grid">
                     <div class="info-row">
                         <div class="info-label">Paso 1:</div>
                         <div class="info-value"><strong>Inicia Sesión:</strong> Ve a <strong>hosprogreso.local</strong> e ingresa con las credenciales proporcionadas arriba.</div>
                     </div>
                     <div class="info-row">
                         <div class="info-label">Paso 2:</div>
                         <div class="info-value"><strong>Accede a tu Perfil:</strong> Una vez dentro del sistema, haz clic en tu nombre de usuario en la esquina superior derecha.</div>
                     </div>
                     <div class="info-row">
                         <div class="info-label">Paso 3:</div>
                         <div class="info-value"><strong>Selecciona "Mi Perfil":</strong> En el menú desplegable, selecciona la opción <strong>"Mi Perfil"</strong>.</div>
                     </div>
                     <div class="info-row">
                         <div class="info-label">Paso 4:</div>
                         <div class="info-value"><strong>Editar Perfil:</strong> En la página de tu perfil, busca y haz clic en el botón <strong>"Editar Perfil"</strong>.</div>
                     </div>
                     <div class="info-row">
                         <div class="info-label">Paso 5:</div>
                         <div class="info-value"><strong>Contraseña Actual:</strong> En el campo <strong>"Contraseña Actual"</strong>, ingresa la contraseña que aparece en este documento: <strong>{{ $plainPassword }}</strong></div>
                     </div>
                     <div class="info-row">
                         <div class="info-label">Paso 6:</div>
                         <div class="info-value"><strong>Nueva Contraseña:</strong> En el campo <strong>"Nueva Contraseña"</strong>, ingresa tu nueva contraseña segura.</div>
                     </div>
                     <div class="info-row">
                         <div class="info-label">Paso 7:</div>
                         <div class="info-value"><strong>Confirma la Contraseña:</strong> Repite la nueva contraseña en el campo <strong>"Confirmar Nueva Contraseña"</strong>.</div>
                     </div>
                     <div class="info-row">
                         <div class="info-label">Paso 8:</div>
                         <div class="info-value"><strong>Guarda los Cambios:</strong> Haz clic en <strong>"Actualizar Perfil"</strong> para guardar tu nueva contraseña.</div>
                     </div>
                     <div class="info-row">
                         <div class="info-label">Paso 9:</div>
                         <div class="info-value"><strong>¡Listo!:</strong> Tu contraseña ha sido cambiada exitosamente. Guarda esta nueva contraseña en un lugar seguro.</div>
                     </div>
                 </div>
             </div>

            <!-- Advertencias de Seguridad -->
            <div class="warning-section">
                <div class="section-title">⚠️ IMPORTANTE - MEDIDAS DE SEGURIDAD</div>
                <p class="warning-text">
                    <strong>• Cambia tu contraseña inmediatamente</strong> después del primer inicio de sesión.<br>
                    <strong>• No compartas</strong> estas credenciales con otras personas.<br>
                    <strong>• Usa una contraseña segura</strong> con al menos 8 caracteres, incluyendo mayúsculas, minúsculas y números.<br>
                    <strong>• Destruye este documento</strong> después de cambiar tu contraseña.
                </p>
            </div>

            <!-- Información de Generación del PDF -->
            <div class="generated-info">
                <strong>PDF generado el:</strong> {{ now()->format('d/m/Y \a \l\a\s H:i') }}<br>
                <strong>Usuario:</strong> {{ Auth::user()->name ?? 'Sistema' }}
            </div>
        </div>
    </div>

    <!-- Footer con numeración de páginas -->
    <div class="footer">
        <div class="footer-content">
            <div>Hospital Nacional de Progreso - Sistema de Gestión Hospitalaria</div>
            <div>Teléfono: (502) 0000-0000 | Email: sistema@hospitalprogreso.gt</div>
            <div style="margin-top: 3px; font-size: 9px;">
                Este documento contiene información confidencial. Manéjalo con cuidado y destrúyelo después de cambiar tu contraseña.
            </div>
        </div>
        <div class="page-number">
            <script type="text/php">
                if (isset($pdf)) {
                    $pdf->page_script('
                        $font = $fontMetrics->get_font("DejaVu Sans", "normal");
                        $size = 9;
                        $pageText = "Página " . $PAGE_NUM . " de " . $PAGE_COUNT;
                        $pdf->text(500, 820, $pageText, $font, $size, array(1,1,1));
                    ');
                }
            </script>
        </div>
    </div>
</body>
</html>