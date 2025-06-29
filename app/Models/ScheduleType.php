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
    ];

    protected $casts = [
        'days_of_week' => 'array',
    ];

    public function specialty()
    {
        return $this->belongsTo(Specialty::class);
    }

    public function doctors()
    {
        return $this->hasMany(Doctor::class);
    }
} 