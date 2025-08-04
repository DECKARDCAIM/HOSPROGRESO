<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ContraceptiveMethod extends Model
{
    protected $fillable = [
        'name',
        'description',
        'type',
        'is_active'
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    /**
     * Scope para obtener solo los métodos activos
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Obtener la etiqueta del tipo de método
     */
    public function getTypeLabel()
    {
        $labels = [
            'hormonal' => 'Hormonal',
            'barrera' => 'Barrera',
            'natural' => 'Natural',
            'quirurgico' => 'Quirúrgico',
            'emergencia' => 'Emergencia',
            'otro' => 'Otro'
        ];

        return $labels[$this->type] ?? 'Otro';
    }
}
