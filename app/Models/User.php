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
        'password',
        'phone',
        'address',
        'birth_date',
        'gender',
        'profile_photo_path',
        'banner_photo_path',
        'role',
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
        ];
    }

    // Constantes para roles
    const ROLE_EMERGENCY = 'emergencia';
    const ROLE_CONSULTATION = 'consulta_externa';
    const ROLE_ADMIN = 'admin';

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

    // Métodos de ayuda para roles
    public function isEmergency()
    {
        return $this->role === self::ROLE_EMERGENCY;
    }

    public function isConsultation()
    {
        return $this->role === self::ROLE_CONSULTATION;
    }

    public function isAdmin()
    {
        return $this->role === self::ROLE_ADMIN;
    }

    public function getRoleLabel()
    {
        return match($this->role) {
            self::ROLE_EMERGENCY => 'Emergencia',
            self::ROLE_CONSULTATION => 'Consulta Externa',
            self::ROLE_ADMIN => 'Administrador',
            default => 'No definido'
        };
    }
}
