<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Colegio extends Model
{
    use HasFactory;

    protected $fillable = ['nombre', 'municipio_id', 'activo'];

    public function municipio()
    {
        return $this->belongsTo(Municipio::class);
    }

    public function users()
    {
        return $this->hasMany(User::class);
    }
}