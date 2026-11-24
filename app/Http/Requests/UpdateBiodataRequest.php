<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class UpdateBiodataRequest extends UpdateProfileRequest
{
    /** @return array<string, mixed> */
    public function rules(): array
    {
        return [
            'name' => ['sometimes', 'required', 'string', 'max:255'],
            ...$this->biodataRules(true),
        ];
    }

    /** @return array<string, mixed> */
    public function completedBiodata(): array
    {
        $data = $this->validated();

        return [
            ...$data,
            'initials' => $data['initials'] ?? $this->user()->initials,
            'fakultas' => $data['fakultas'] ?? $this->user()->fakultas,
            'is_biodata_filled' => true,
        ];
    }

    protected function failedValidation(Validator $validator): void
    {
        if ($this->is('api/*')) {
            throw new HttpResponseException(response()->json([
                'success' => false,
                'message' => 'Data biodata tidak valid.',
                'errors' => $validator->errors(),
            ], 422));
        }

        parent::failedValidation($validator);
    }
}
