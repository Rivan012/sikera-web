<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EducationalModule extends Model
{
    protected $fillable = [
        'module_number',
        'title',
        'subtitle',
        'description',
        'badge_icon',
        'banner_image',
        'estimated_time',
    ];

    public function topics()
    {
        return $this->hasMany(ModuleTopic::class)->orderBy('order_index');
    }
}
