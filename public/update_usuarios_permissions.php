<?php
// Archivo temporal para actualizar permisos de usuarios

// Configurar el entorno de Laravel
$_SERVER['SERVER_NAME'] = 'hosprogreso.test';
$_SERVER['REQUEST_URI'] = '/';
$_SERVER['REQUEST_METHOD'] = 'GET';

require __DIR__ . '/../bootstrap/app.php';

$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$response = $kernel->handle(
    $request = Illuminate\Http\Request::capture()
);

use App\Models\Role;
use App\Models\Permission;

try {
    echo "<h1>🔧 Actualizando Permisos de Usuarios</h1>";
    
    // Buscar el rol Archivo Clínico
    $archivoRole = Role::where('name', 'Archivo Clínico')->first();
    
    if (!$archivoRole) {
        echo "<p style='color: red;'>❌ Error: No se encontró el rol 'Archivo Clínico'</p>";
        exit;
    }
    
    echo "<p>✅ Rol encontrado: <strong>{$archivoRole->name}</strong></p>";
    
    // Verificar si ya tiene el permiso usuarios.ver
    $hasPermission = $archivoRole->permissions()->where('slug', 'usuarios.ver')->exists();
    
    if ($hasPermission) {
        echo "<p style='color: orange;'>⚠️ El rol ya tiene el permiso 'usuarios.ver'</p>";
    } else {
        echo "<p>📝 Agregando permiso 'usuarios.ver' al rol...</p>";
        
        // Obtener permisos actuales + el nuevo
        $archivoPermissions = Permission::whereIn('slug', [
            // Permisos propios del archivo clínico
            'archivo_clinico.acceso',
            'archivo_clinico.expedientes_recientes',
            'archivo_clinico.expedientes_archivados',
            'archivo_clinico.mostrar',
            // Permisos para VER expedientes clínicos (solo lectura)
            'emergencia.expedientes.ver',
            'consulta_externa.expedientes.ver',
            // Permiso para imprimir expedientes
            'emergencia.expedientes.imprimir',
            'consulta_externa.expedientes.imprimir',
            // Permiso para VER la página de importación (sin importar)
            'import.acceso',
            // Permiso para VER usuarios (solo lectura, sin crear/editar/eliminar)
            'usuarios.ver',
        ])->get();
        
        // Sincronizar permisos
        $archivoRole->permissions()->sync($archivoPermissions->pluck('id'));
        
        echo "<p style='color: green;'>✅ Permiso 'usuarios.ver' agregado exitosamente</p>";
    }
    
    // Mostrar permisos actuales del rol
    echo "<h3>📋 Permisos Actuales del Rol 'Archivo Clínico':</h3>";
    echo "<ul>";
    foreach ($archivoRole->permissions()->orderBy('slug')->get() as $permission) {
        $style = ($permission->slug === 'usuarios.ver') ? 'color: green; font-weight: bold;' : '';
        echo "<li style='$style'>{$permission->slug} - {$permission->name}</li>";
    }
    echo "</ul>";
    
    echo "<hr>";
    echo "<h3>🧪 Verificaciones:</h3>";
    echo "<p>Ahora deberías poder:</p>";
    echo "<ul>";
    echo "<li>✅ <strong>Acceder a '/usuarios'</strong> - Ver lista de usuarios</li>";
    echo "<li>❌ <strong>No acceder a '/usuarios/create'</strong> - Sin permisos de crear</li>";
    echo "<li>❌ <strong>No acceder a '/usuarios/{id}/edit'</strong> - Sin permisos de editar</li>";
    echo "</ul>";
    
    echo "<hr>";
    echo "<p><strong>¡Actualización completada!</strong></p>";
    echo "<p><a href='/usuarios' style='background: #007bff; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;'>🧪 Probar Acceso a Usuarios</a></p>";
    
} catch (Exception $e) {
    echo "<p style='color: red;'>❌ Error: " . $e->getMessage() . "</p>";
    echo "<pre>" . $e->getTraceAsString() . "</pre>";
}
?>