<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ModuleTopic extends Model
{
    protected $fillable = [
        'educational_module_id',
        'topic_code',
        'title',
        'content_html',
        'youtube_video_id',
        'infographic_url',
        'pdf_path',
        'pdf_original_name',
        'pdf_size',
        'order_index',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'pdf_size' => 'integer',
            'order_index' => 'integer',
        ];
    }

    /**
     * @return BelongsTo<EducationalModule, $this>
     */
    public function module(): BelongsTo
    {
        return $this->belongsTo(EducationalModule::class, 'educational_module_id');
    }

    /**
     * @return HasMany<ModuleProgress, $this>
     */
    public function progresses(): HasMany
    {
        return $this->hasMany(ModuleProgress::class);
    }

    public function hasPdf(): bool
    {
        return filled($this->pdf_path);
    }

    public function formattedPdfSize(): ?string
    {
        if (! $this->pdf_size || $this->pdf_size <= 0) {
            return null;
        }

        if ($this->pdf_size >= 1048576) {
            return number_format($this->pdf_size / 1048576, 2, ',', '.').' MB';
        }

        return max(1, (int) round($this->pdf_size / 1024)).' KB';
    }
}
