<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

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

    /**
     * @return HasMany<ModuleTopic, $this>
     */
    public function topics(): HasMany
    {
        return $this->hasMany(ModuleTopic::class)->orderBy('order_index');
    }
}
