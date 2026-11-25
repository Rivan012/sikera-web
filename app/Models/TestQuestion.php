<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class TestQuestion extends Model
{
    public const TYPE_PRETEST = 'pre_test';

    public const TYPE_POSTTEST = 'post_test';

    protected $fillable = [
        'question_type',
        'module_target',
        'question_text',
        'options',
        'correct_answer',
        'explanation',
    ];

    protected $casts = [
        'module_target' => 'integer',
        'options' => 'array',
    ];

    /**
     * @param  Builder<self>  $query
     * @return Builder<self>
     */
    public function scopePreTest(Builder $query): Builder
    {
        return $query->where('question_type', self::TYPE_PRETEST);
    }

    /**
     * @param  Builder<self>  $query
     * @return Builder<self>
     */
    public function scopePostTest(Builder $query): Builder
    {
        return $query->where('question_type', self::TYPE_POSTTEST);
    }

    /**
     * @return array<int, array{label: string, text: string}>
     */
    public function getOptionsAttribute(mixed $value): array
    {
        $raw = is_string($value) ? json_decode($value, true) : $value;

        return static::normalizeOptions($raw);
    }

    /**
     * @return array<int, array{label: string, text: string}>
     */
    public function getFormattedOptionsAttribute(): array
    {
        return $this->options;
    }

    /**
     * @return array<int, array{label: string, text: string}>
     */
    public static function normalizeOptions(mixed $rawOptions): array
    {
        if (empty($rawOptions)) {
            return [];
        }

        if (is_string($rawOptions)) {
            $decoded = json_decode($rawOptions, true);
            $rawOptions = is_array($decoded) ? $decoded : [$rawOptions];
        }

        if (! is_array($rawOptions)) {
            return [];
        }

        $normalized = [];
        $index = 0;

        foreach ($rawOptions as $key => $val) {
            if (is_array($val) && (isset($val['label']) || isset($val['text']))) {
                $label = strtoupper(trim((string) ($val['label'] ?? chr(65 + $index))));
                $text = trim((string) ($val['text'] ?? ''));
            } elseif (is_string($key) && ! is_numeric($key)) {
                $label = strtoupper(trim($key));
                $text = is_string($val) ? trim($val) : (string) json_encode($val);
            } else {
                $label = chr(65 + $index);
                $text = is_string($val) ? trim($val) : (string) json_encode($val);
            }

            $normalized[] = [
                'label' => $label,
                'text' => $text,
            ];
            $index++;
        }

        return $normalized;
    }
}
