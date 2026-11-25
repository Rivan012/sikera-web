<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StorePosttestQuestionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->isAdmin() ?? false;
    }

    protected function prepareForValidation(): void
    {
        if (! $this->has('options') && $this->hasAny(['option_a', 'option_b', 'option_c', 'option_d'])) {
            $this->merge([
                'options' => [
                    ['label' => 'A', 'text' => trim((string) $this->input('option_a', ''))],
                    ['label' => 'B', 'text' => trim((string) $this->input('option_b', ''))],
                    ['label' => 'C', 'text' => trim((string) $this->input('option_c', ''))],
                    ['label' => 'D', 'text' => trim((string) $this->input('option_d', ''))],
                ],
            ]);
        }

        if (is_string($this->input('correct_answer'))) {
            $this->merge(['correct_answer' => strtoupper(trim($this->input('correct_answer')))]);
        }
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'question_text' => ['required', 'string', 'max:2000'],
            'options' => ['required', 'array', 'size:4'],
            'options.*.label' => ['required', 'string', Rule::in(['A', 'B', 'C', 'D'])],
            'options.*.text' => ['required', 'string', 'max:1000'],
            'correct_answer' => ['required', 'string', Rule::in(['A', 'B', 'C', 'D'])],
            'explanation' => ['nullable', 'string', 'max:2000'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'question_text.required' => 'Teks pertanyaan Post-Test wajib diisi.',
            'options.required' => 'Keempat pilihan jawaban (A, B, C, dan D) wajib diisi.',
            'options.*.text.required' => 'Setiap pilihan jawaban A sampai D tidak boleh kosong.',
            'correct_answer.required' => 'Pilih kunci jawaban yang benar.',
            'correct_answer.in' => 'Kunci jawaban harus salah satu dari A, B, C, atau D.',
        ];
    }
}
