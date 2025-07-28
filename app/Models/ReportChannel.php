<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReportChannel extends Model
{
    use HasFactory;

    protected $fillable = ['name'];

    // Este modelo no tiene una relación de muchos a muchos directa con Report
    // ya que es una selección única en la tabla reports.
    // Solo lo creamos para poder "sembrar" los datos.
}