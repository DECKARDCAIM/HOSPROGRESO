<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'cui',
        'password',
        'phone',
        'address',
        'birth_date',
        'gender',
        'profile_photo_path',
        'banner_photo_path',
        'role_id',
        'is_active',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be appended.
     *
     * @var list<string>
     */
    protected $appends = [
        'profile_photo_url',
        'banner_photo_url',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'birth_date' => 'date',
            'is_active' => 'boolean',
        ];
    }

    /**
     * Relación con el rol
     */
    public function role()
    {
        return $this->belongsTo(Role::class);
    }

    /**
     * Get the URL to the user's profile photo.
     *
     * @return string
     */
    public function getProfilePhotoUrlAttribute()
    {
        if ($this->profile_photo_path) {
            return asset('storage/' . $this->profile_photo_path);
        }

        return $this->defaultProfilePhotoUrl();
    }

    /**
     * Get the default profile photo URL if no profile photo has been uploaded.
     *
     * @return string
     */
    protected function defaultProfilePhotoUrl()
    {
        $name = trim(collect(explode(' ', $this->name))->map(function ($segment) {
            return mb_substr($segment, 0, 1);
        })->join(' '));

        return 'https://ui-avatars.com/api/?name='.urlencode($name).'&color=7F9CF5&background=EBF4FF';
    }

    /**
     * Get the URL to the user's banner photo.
     *
     * @return string
     */
    public function getBannerPhotoUrlAttribute()
    {
        if ($this->banner_photo_path) {
            return asset('storage/' . $this->banner_photo_path);
        }
        // Retornar un banner por defecto
        return asset('img/carrusel/bk1.webp');
    }

    /**
     * Verificar si el usuario es administrador
     */
    public function isAdmin()
    {
        return $this->role && $this->role->name === 'Administrador';
    }

    /**
     * Verificar si el usuario tiene un rol específico
     */
    public function hasRole($roleName)
    {
        return $this->role && $this->role->name === $roleName;
    }

    /**
     * Obtener el nombre del rol del usuario
     */
    public function getRoleName()
    {
        return $this->role ? $this->role->name : 'Sin rol asignado';
    }

    /**
     * Verificar si el usuario puede acceder al sistema
     */
    public function canAccess()
    {
        return $this->is_active && $this->role_id && $this->role && $this->role->is_active;
    }

    /**
     * Verificar si el usuario es de Emergencia
     */
    public function isEmergency()
    {
        return $this->role_id === Role::EMERGENCIA_ID;
    }

    /**
     * Verificar si el usuario es de Consulta Externa
     */
    public function isConsultation()
    {
        return $this->role_id === Role::CONSULTA_EXTERNA_ID;
    }

    /**
     * Verificar si el usuario es de Archivo Clínico
     */
    public function isArchive()
    {
        return $this->role_id === Role::ARCHIVO_CLINICO_ID;
    }

    /**
     * Verificar si el usuario es administrador (usando ID fijo)
     */
    public function isAdminById()
    {
        return $this->role_id === Role::ADMINISTRADOR_ID;
    }

    /**
     * Verificar si el usuario tiene un permiso específico
     */
    public function hasPermission($slug)
    {
        return $this->role && $this->role->permissions->contains('slug', $slug);
    }

    /**
     * Obtener el área de trabajo del usuario (emergencia o consulta_externa)
     */
    public function getWorkArea()
    {
        if ($this->isAdmin()) {
            return 'all'; // Administrador ve todo
        }

        $hasEmergencia = $this->hasAnyPermission([
            'emergencia.acceso',
            'emergencia.expedientes.ver', 'emergencia.expedientes.crear', 'emergencia.expedientes.editar', 'emergencia.expedientes.eliminar',
            'emergencia.consultas.ver', 'emergencia.consultas.crear', 'emergencia.consultas.editar', 'emergencia.consultas.eliminar',
            'emergencia.citas.ver', 'emergencia.citas.crear', 'emergencia.citas.editar', 'emergencia.citas.eliminar'
        ]);

        $hasConsultaExterna = $this->hasAnyPermission([
            'consulta_externa.acceso',
            'consulta_externa.expedientes.ver', 'consulta_externa.expedientes.crear', 'consulta_externa.expedientes.editar', 'consulta_externa.expedientes.eliminar',
            'consulta_externa.consultas.ver', 'consulta_externa.consultas.crear', 'consulta_externa.consultas.editar', 'consulta_externa.consultas.eliminar',
            'consulta_externa.citas.ver', 'consulta_externa.citas.crear', 'consulta_externa.citas.editar', 'consulta_externa.citas.eliminar'
        ]);

        if ($hasEmergencia && $hasConsultaExterna) {
            return 'both';
        } elseif ($hasEmergencia) {
            return 'emergencia';
        } elseif ($hasConsultaExterna) {
            return 'consulta_externa';
        }

        return 'none';
    }

    /**
     * Verificar si el usuario tiene alguno de los permisos especificados
     */
    public function hasAnyPermission(array $permissions)
    {
        if ($this->isAdmin()) {
            return true;
        }

        foreach ($permissions as $permission) {
            if ($this->hasPermission($permission)) {
                return true;
            }
        }

        return false;
    }
}
