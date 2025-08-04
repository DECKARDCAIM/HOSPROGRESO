<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CompanionRelationship extends Model
{
    protected $fillable = [
        'name',
        'description',
        'is_active'
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    /**
     * Scope para obtener solo las relaciones activas
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
