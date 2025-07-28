<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Municipio extends Model
{
    use HasFactory;

    protected $fillable = [
        'nombre',
        'activo',
    ];

    protected $casts = [
        'activo' => 'boolean',
    ];

    // Relación: Un municipio puede tener muchos colegios
    public function colegios()
    {
        return $this->hasMany(Colegio::class);
    }

    // Relación: Un municipio puede tener muchos usuarios a través de colegios
    public function users()
    {
        return $this->hasManyThrough(User::class, Colegio::class);
    }

    // Scope para obtener solo municipios activos
    public function scopeActivos($query)
    {
        return $query->where('activo', true);
    }

    // Método helper para verificar si el municipio está activo
    public function isActivo()
    {
        return $this->activo;
    }

    // Método helper para contar colegios asociados
    public function getTotalColegiosAttribute()
    {
        return $this->colegios()->count();
    }

    // Método helper para contar colegios activos
    public function getColegiosActivosAttribute()
    {
        return $this->colegios()->where('activo', true)->count();
    }
}