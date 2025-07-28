<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Feeling extends Model
{
    use HasFactory;

    protected $fillable = ['description'];

    // Relación de muchos a muchos con Report
    public function reports()
    {
        return $this->belongsToMany(Report::class, 'report_feeling', 'feeling_id', 'report_id');
    }
}