<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SocialMedia extends Model
{
    use HasFactory;

    // Campos que se pueden asignar masivamente
    protected $fillable = ['name'];

    // Relación de muchos a muchos con Report (una red social puede estar en muchas denuncias)
    public function reports()
    {
        return $this->belongsToMany(Report::class, 'report_social_media', 'social_media_id', 'report_id');
    }
}