<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CaseStudy extends Model
{
    protected $fillable = [
        'title',
        'category',
        'narrative',
        'legal_analysis',
        'medical_analysis',
        'solution_tips',
    ];
}
