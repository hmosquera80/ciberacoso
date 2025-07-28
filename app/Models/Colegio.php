<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Colegio extends Model
{
    use HasFactory;

    protected $fillable = [
        'nombre',
        'municipio_id',
        'activo',
    ];

    protected $casts = [
        'activo' => 'boolean',
    ];

    // Relación: Un colegio pertenece a un municipio
    public function municipio()
    {
        return $this->belongsTo(Municipio::class);
    }

    // Relación: Un colegio puede tener muchos usuarios
    public function users()
    {
        return $this->hasMany(User::class);
    }
}