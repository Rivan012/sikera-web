<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreModuleTopicRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->isAdmin() ?? false;
    }

    protected function prepareForValidation(): void
    {
        $rawVideo = $this->input('youtube_video_id');
        if (is_string($rawVideo)) {
            $trimmed = trim($rawVideo);
            if ($trimmed === '') {
                $this->merge(['youtube_video_id' => null]);
            } elseif (preg_match('/(?:v=|youtu\.be\/|embed\/)([\w\-]{6,20})/', $trimmed, $matches)) {
                $this->merge(['youtube_video_id' => $matches[1]]);
            } else {
                $this->merge(['youtube_video_id' => $trimmed]);
            }
        }
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'topic_code' => ['required', 'string', 'max:20'],
            'title' => ['required', 'string', 'max:255'],
            'youtube_video_id' => ['nullable', 'string', 'max:255'],
            'pdf_file' => ['required', 'file', 'mimes:pdf', 'mimetypes:application/pdf', 'max:20480'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'topic_code.required' => 'Kode subbab wajib diisi.',
            'title.required' => 'Judul subbab wajib diisi.',
            'pdf_file.required' => 'Berkas PDF subbab wajib diunggah.',
            'pdf_file.file' => 'Berkas PDF yang diunggah tidak valid.',
            'pdf_file.mimes' => 'Berkas subbab harus berformat PDF (.pdf).',
            'pdf_file.mimetypes' => 'Berkas subbab harus berupa dokumen PDF yang valid.',
            'pdf_file.max' => 'Ukuran berkas PDF maksimal 20 MB.',
        ];
    }
}
