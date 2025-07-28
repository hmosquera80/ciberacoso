<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DenunciaSeguimiento extends Model
{
    use HasFactory;

    protected $fillable = [
        'report_id',
        'user_id',
        'anotacion',
        'denuncia_estado_anterior_id',
        'denuncia_estado_nuevo_id',
    ];

    public function report()
    {
        return $this->belongsTo(Report::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function estadoAnterior()
    {
        return $this->belongsTo(DenunciaEstado::class, 'denuncia_estado_anterior_id');
    }

    public function estadoNuevo()
    {
        return $this->belongsTo(DenunciaEstado::class, 'denuncia_estado_nuevo_id');
    }
}