<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TestQuestion extends Model
{
    protected $fillable = [
        'module_target',
        'question_text',
        'options',
        'correct_answer',
        'explanation',
    ];

    protected $casts = [
        'options' => 'array',
    ];

    /**
     * Pastikan format opsi selalu terstandarisasi sebagai array of objects:
     * [
     *     ['label' => 'A', 'text' => '...'],
     *     ['label' => 'B', 'text' => '...'],
     * ]
     *
     * @param  mixed  $value
     * @return array<int, array{label: string, text: string}>
     */
    public function getOptionsAttribute($value): array
    {
        $raw = is_string($value) ? json_decode($value, true) : $value;

        return static::normalizeOptions($raw);
    }

    /**
     * Accessor eksplisit untuk mendapatkan opsi yang terformat rapi
     *
     * @return array<int, array{label: string, text: string}>
     */
    public function getFormattedOptionsAttribute(): array
    {
        return $this->options;
    }

    /**
     * Normalisasi format opsi fleksibel (baik dari Excel, JSON seeder, atau input manual)
     *
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
            // Kasus 1: Opsi sudah berupa array/map dengan 'label' dan 'text'
            if (is_array($val) && (isset($val['label']) || isset($val['text']))) {
                $label = strtoupper(trim((string) ($val['label'] ?? chr(65 + $index))));
                $text = trim((string) ($val['text'] ?? ''));
            }
            // Kasus 2: Associative array format Excel / Bank Soal ('A' => 'Teks Opsi A')
            elseif (is_string($key) && ! is_numeric($key)) {
                $label = strtoupper(trim($key));
                $text = is_string($val) ? trim($val) : (string) json_encode($val);
            }
            // Kasus 3: Numeric array (0 => 'Teks Opsi A')
            else {
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
