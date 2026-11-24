<?php

namespace App\Services;

use App\Models\ModuleTopic;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class ModuleTopicPdfService
{
    public const DISK = 'local';

    public const DIRECTORY = 'module-topics';

    /**
     * Simpan berkas PDF subbab ke disk privat dan hapus berkas lama bila diganti.
     *
     * @return array{pdf_path: string, pdf_original_name: string, pdf_size: int}
     */
    public function store(UploadedFile $file, ?string $previousPath = null): array
    {
        $storedPath = $file->store(self::DIRECTORY, self::DISK);

        if ($previousPath && $previousPath !== $storedPath) {
            $this->delete($previousPath);
        }

        $originalName = trim((string) basename($file->getClientOriginalName()));
        if ($originalName === '' || ! str_ends_with(strtolower($originalName), '.pdf')) {
            $originalName = 'materi-subbab.pdf';
        }

        return [
            'pdf_path' => $storedPath,
            'pdf_original_name' => Str::limit($originalName, 255, ''),
            'pdf_size' => (int) ($file->getSize() ?: 0),
        ];
    }

    public function delete(?string $path): void
    {
        if (blank($path)) {
            return;
        }

        if (Storage::disk(self::DISK)->exists($path)) {
            Storage::disk(self::DISK)->delete($path);
        }
    }

    public function exists(?string $path): bool
    {
        return filled($path) && Storage::disk(self::DISK)->exists($path);
    }

    /**
     * Streaming berkas PDF inline dengan dukungan HTTP Range agar PDF.js dapat merender flipbook.
     */
    public function streamResponse(ModuleTopic $topic, bool $allowCors = false): BinaryFileResponse
    {
        if (! $this->exists($topic->pdf_path)) {
            abort(404, 'Berkas PDF materi tidak ditemukan.');
        }

        $displayName = $topic->pdf_original_name ?: 'materi-'.$topic->topic_code.'.pdf';
        $safeFilename = preg_replace('/["\r\n\\\\\/]+/', '_', $displayName) ?: 'materi.pdf';

        $headers = [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline; filename="'.$safeFilename.'"',
            'X-Content-Type-Options' => 'nosniff',
            'Cache-Control' => 'private, max-age=3600',
        ];

        if ($allowCors) {
            $headers['Access-Control-Allow-Origin'] = '*';
            $headers['Access-Control-Allow-Methods'] = 'GET, OPTIONS';
            $headers['Access-Control-Allow-Headers'] = 'Authorization, Content-Type, Range';
            $headers['Access-Control-Expose-Headers'] = 'Content-Length, Content-Range, Accept-Ranges';
        }

        return response()->file(Storage::disk(self::DISK)->path($topic->pdf_path), $headers);
    }
}
