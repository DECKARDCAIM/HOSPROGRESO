<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Gate;
use App\Models\Permission;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Paginator::useBootstrap();
        
        // Registrar políticas globales para permisos
        $this->registerPermissionGates();
    }

    /**
     * Registrar Gates dinámicos para todos los permisos
     */
    private function registerPermissionGates(): void
    {
        try {
            // Obtener todos los permisos y registrar gates
            $permissions = Permission::all();
            
            foreach ($permissions as $permission) {
                Gate::define($permission->slug, function ($user) use ($permission) {
                    return $user->hasPermission($permission->slug);
                });
            }
        } catch (\Exception $e) {
            // Si las tablas no existen aún (durante migraciones), no hacer nada
            // Esto evita errores durante php artisan migrate
        }
    }
}
