<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreModuleRequest extends FormRequest
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
            'module_number' => ['required', 'integer', 'min:1', 'unique:educational_modules,module_number'],
            'title' => ['required', 'string', 'max:255'],
            'subtitle' => ['nullable', 'string', 'max:255'],
            'description' => ['required', 'string'],
            'banner_mode' => ['nullable', 'string', 'in:auto,upload,url'],
            'banner_image' => ['nullable', 'string', 'max:500'],
            'banner_file' => ['nullable', 'file', 'mimes:jpeg,png,jpg,gif,svg,webp', 'max:5120'],
            'banner_theme' => ['nullable', 'string', 'in:teal,amber,rose,indigo,emerald,purple'],
            'estimated_time' => ['required', 'string', 'max:100'],
            'topic_code' => ['required', 'string', 'max:20'],
            'topic_title' => ['required', 'string', 'max:255'],
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
            'module_number.required' => 'Nomor modul wajib diisi.',
            'module_number.unique' => 'Nomor modul sudah digunakan oleh modul lain.',
            'title.required' => 'Judul modul wajib diisi.',
            'description.required' => 'Deskripsi modul wajib diisi.',
            'estimated_time.required' => 'Estimasi waktu belajar wajib diisi.',
            'topic_code.required' => 'Kode subbab pertama wajib diisi.',
            'topic_title.required' => 'Judul subbab pertama wajib diisi.',
            'pdf_file.required' => 'Berkas PDF materi subbab pertama wajib diunggah.',
            'pdf_file.file' => 'Berkas PDF materi tidak valid.',
            'pdf_file.mimes' => 'Berkas materi subbab harus berformat PDF (.pdf).',
            'pdf_file.mimetypes' => 'Berkas materi subbab harus berupa dokumen PDF yang valid.',
            'pdf_file.max' => 'Ukuran berkas PDF maksimal 20 MB.',
        ];
    }
}
