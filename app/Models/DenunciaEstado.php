<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DenunciaEstado extends Model
{
    use HasFactory;

    protected $fillable = ['nombre'];

    public function reports()
    {
        return $this->hasMany(Report::class, 'denuncia_estado_id');
    }

    public function seguimientosOrigen()
    {
        return $this->hasMany(DenunciaSeguimiento::class, 'denuncia_estado_anterior_id');
    }

    public function seguimientosDestino()
    {
        return $this->hasMany(DenunciaSeguimiento::class, 'denuncia_estado_nuevo_id');
    }
}