<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Department extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'description', 'country_id'];

    // Un departamento pertenece a un país
    public function country()
    {
        return $this->belongsTo(Country::class);
    }

    // Un departamento tiene muchos municipios (más adelante)
    public function municipalities()
    {
        return $this->hasMany(Municipality::class);
    }
}
