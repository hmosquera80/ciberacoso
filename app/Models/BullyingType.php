<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BullyingType extends Model
{
    use HasFactory;

    protected $fillable = ['description'];

    // Relación de muchos a muchos con Report
    public function reports()
    {
        return $this->belongsToMany(Report::class, 'report_bullying_type', 'bullying_type_id', 'report_id');
    }
}