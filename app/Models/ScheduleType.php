<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ScheduleType extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'specialty_id',
        'days_of_week',
        'start_time',
        'end_time',
        'max_patients',
        'is_active'
    ];

    protected $casts = [
        'days_of_week' => 'array',
        'is_active' => 'boolean',
    ];

    public function specialty()
    {
        return $this->belongsTo(Specialty::class);
    }

    public function doctors()
    {
        return $this->hasMany(Doctor::class);
    }

    /**
     * Obtener tipos de horario activos
     */
    public static function active()
    {
        return static::where('is_active', true)->orderBy('name');
    }
} 