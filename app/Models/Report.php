<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Report extends Model
{
    use HasFactory;

    protected $fillable = [
        'denunciante_nombre_completo',
        'denunciante_fecha_nacimiento',
        'denunciante_edad',
        'denunciante_municipio',
        'denunciante_colegio',
        'denunciante_curso_grado',
        'denunciante_identificacion',
        'afectado_quien',
        'agresor_conocido',
        'agresor_nombre',
        'tiempo_dias',
        'tiempo_meses',
        'tiempo_anios',
        'reportado_otro_medio',
        'reportado_cual_linea',
        'resumen_hechos',
        'contacto_deseado',
        'tiene_pruebas',
        'evidencia_path',
        'denuncia_estado_id', // ¡Añadir este campo!
    ];

    // Relación de muchos a muchos con SocialMedia
    public function socialMedia()
    {
        return $this->belongsToMany(SocialMedia::class, 'report_social_media', 'report_id', 'social_media_id');
    }

    // Relación de muchos a muchos con BullyingType
    public function bullyingTypes()
    {
        return $this->belongsToMany(BullyingType::class, 'report_bullying_type', 'report_id', 'bullying_type_id');
    }

    // Relación de muchos a muchos con Feeling
    public function feelings()
    {
        return $this->belongsToMany(Feeling::class, 'report_feeling', 'report_id', 'feeling_id');
    }

    // Relación con DenunciaEstado
    public function estado()
    {
        return $this->belongsTo(DenunciaEstado::class, 'denuncia_estado_id');
    }

    // Relación con DenunciaSeguimiento
    public function seguimientos()
    {
        return $this->hasMany(DenunciaSeguimiento::class);
    }
}