<?php

// Script temporal para actualizar permisos de importación para el rol Archivo Clínico
// Visita: http://hosprogreso.test/update_import_permissions.php

require_once '../vendor/autoload.php';

// Cargar la aplicación Laravel
$app = require_once '../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Permission;
use App\Models\Role;

echo "<h1>🔧 Actualizando Permisos de Importación</h1>";
echo "<hr>";

try {
    // Buscar el rol Archivo Clínico
    $archivoRole = Role::where('name', 'Archivo Clínico')->first();
    
    if (!$archivoRole) {
        echo "<p style='color: red;'>❌ ERROR: No se encontró el rol 'Archivo Clínico'</p>";
        exit;
    }
    
    echo "<p>✅ Rol 'Archivo Clínico' encontrado (ID: {$archivoRole->id})</p>";
    
    // Buscar el permiso import.acceso
    $importPermission = Permission::where('slug', 'import.acceso')->first();
    
    if (!$importPermission) {
        echo "<p style='color: red;'>❌ ERROR: No se encontró el permiso 'import.acceso'</p>";
        exit;
    }
    
    echo "<p>✅ Permiso 'import.acceso' encontrado (ID: {$importPermission->id})</p>";
    
    // Verificar si el rol ya tiene este permiso
    $hasPermission = $archivoRole->permissions()->where('slug', 'import.acceso')->exists();
    
    if ($hasPermission) {
        echo "<p style='color: orange;'>⚠️ El rol ya tiene el permiso 'import.acceso'</p>";
    } else {
        // Agregar el permiso al rol
        $archivoRole->permissions()->attach($importPermission->id);
        echo "<p style='color: green; font-weight: bold;'>✅ Permiso 'import.acceso' agregado al rol 'Archivo Clínico'</p>";
    }
    
    // Verificar que NO tenga el permiso de importar
    $hasImportPermission = $archivoRole->permissions()->where('slug', 'import.importar')->exists();
    
    if ($hasImportPermission) {
        echo "<p style='color: red;'>❌ PROBLEMA: El rol tiene permiso 'import.importar' - esto debe corregirse manualmente</p>";
    } else {
        echo "<p style='color: green;'>✅ CORRECTO: El rol NO tiene permiso 'import.importar'</p>";
    }
    
    echo "<hr>";
    echo "<h3>📋 PERMISOS ACTUALES DEL ROL 'ARCHIVO CLÍNICO':</h3>";
    $permissions = $archivoRole->permissions()->orderBy('slug')->get(['slug', 'name']);
    foreach ($permissions as $perm) {
        echo "<p>✅ {$perm->slug} - {$perm->name}</p>";
    }
    
    echo "<hr>";
    echo "<h3>🧪 RESULTADO ESPERADO:</h3>";
    echo "<p>1. ✅ Puedes VER la página: <a href='/import' target='_blank'>http://hosprogreso.test/import</a></p>";
    echo "<p>2. ❌ NO puedes IMPORTAR archivos (botón debería estar bloqueado o redirigir a permission-denied)</p>";
    echo "<p>3. ❌ NO puedes generar BACKUP (debería redirigir a permission-denied)</p>";
    
    echo "<hr>";
    echo "<p style='color: blue; font-weight: bold;'>🗑️ IMPORTANTE: Elimina este archivo después de usarlo por seguridad</p>";
    echo "<p><strong>Comando:</strong> <code>rm public/update_import_permissions.php</code></p>";
    
} catch (Exception $e) {
    echo "<p style='color: red;'>❌ ERROR: " . $e->getMessage() . "</p>";
    echo "<pre>" . $e->getTraceAsString() . "</pre>";
}

echo "<hr>";
echo "<p><small>Script ejecutado: " . date('Y-m-d H:i:s') . "</small></p>";