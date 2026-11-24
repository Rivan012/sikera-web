<?php

namespace Tests\Feature;

use App\Models\EducationalModule;
use App\Models\EvaluationResponse;
use App\Models\ModuleTopic;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ModulePdfFlipbookTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;

    protected User $mahasiswa;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::create([
            'name' => 'Admin Flipbook',
            'email' => 'admin_flipbook@sikera.id',
            'role' => 'admin',
            'password' => Hash::make('password'),
            'is_biodata_filled' => true,
        ]);

        $this->mahasiswa = User::create([
            'name' => 'Mahasiswa Flipbook',
            'email' => 'mhs_flipbook@unib.ac.id',
            'role' => 'mahasiswa',
            'gender' => 'P',
            'password' => Hash::make('password'),
            'is_biodata_filled' => true,
        ]);
    }

    private function fakePdf(string $filename = 'materi-uji.pdf'): UploadedFile
    {
        $minimalPdf = "%PDF-1.4\n1 0 obj<< /Type /Catalog /Pages 2 0 R >>endobj\n2 0 obj<< /Type /Pages /Kids [3 0 R] /Count 1 >>endobj\n3 0 obj<< /Type /Page /Parent 2 0 R /MediaBox [0 0 300 400] >>endobj\nxref\n0 4\n0000000000 65535 f \n0000000009 00000 n \n0000000058 00000 n \n0000000115 00000 n \ntrailer<< /Size 4 /Root 1 0 R >>\nstartxref\n190\n%%EOF";

        return UploadedFile::fake()->createWithContent($filename, $minimalPdf);
    }

    public function test_admin_dapat_membuat_modul_baru_dengan_upload_pdf_subbab_pertama(): void
    {
        Storage::fake('local');

        $response = $this->actingAs($this->admin)->post(route('admin.modules.store'), [
            'module_number' => 5,
            'title' => 'Kesehatan Reproduksi Digital',
            'subtitle' => 'Panduan literasi digital sehat',
            'description' => 'Materi edukasi berbasis dokumen PDF flipbook.',
            'banner_mode' => 'url',
            'banner_image' => '/images/banners/banner-module-1.svg',
            'estimated_time' => '15 Menit',
            'topic_code' => '5.1',
            'topic_title' => 'Etika Privasi Digital',
            'youtube_video_id' => 'https://youtu.be/v3F4QW_h32M',
            'pdf_file' => $this->fakePdf('etika-privasi.pdf'),
        ]);

        $response->assertRedirect(route('admin.modules.index'));
        $response->assertSessionHas('success');

        $module = EducationalModule::where('module_number', 5)->first();
        $this->assertNotNull($module);

        $topic = ModuleTopic::where('educational_module_id', $module->id)->first();
        $this->assertNotNull($topic);
        $this->assertSame('5.1', $topic->topic_code);
        $this->assertSame('etika-privasi.pdf', $topic->pdf_original_name);
        $this->assertSame('v3F4QW_h32M', $topic->youtube_video_id);
        $this->assertNull($topic->content_html);
        $this->assertGreaterThan(0, $topic->pdf_size);
        Storage::disk('local')->assertExists($topic->pdf_path);
    }

    public function test_admin_gagal_membuat_modul_jika_pdf_subbab_kosong_atau_bukan_pdf(): void
    {
        Storage::fake('local');

        $missingResponse = $this->actingAs($this->admin)->post(route('admin.modules.store'), [
            'module_number' => 6,
            'title' => 'Modul Tanpa PDF',
            'description' => 'Deskripsi uji validasi.',
            'estimated_time' => '10 Menit',
            'topic_code' => '6.1',
            'topic_title' => 'Subbab Kosong',
        ]);

        $missingResponse->assertSessionHasErrors([
            'pdf_file' => 'Berkas PDF materi subbab pertama wajib diunggah.',
        ]);

        $invalidFile = UploadedFile::fake()->createWithContent('catatan.txt', 'bukan file pdf');
        $wrongTypeResponse = $this->actingAs($this->admin)->post(route('admin.modules.store'), [
            'module_number' => 6,
            'title' => 'Modul File Salah',
            'description' => 'Deskripsi uji validasi.',
            'estimated_time' => '10 Menit',
            'topic_code' => '6.1',
            'topic_title' => 'Subbab Bukan PDF',
            'pdf_file' => $invalidFile,
        ]);

        $wrongTypeResponse->assertSessionHasErrors('pdf_file');
        $this->assertDatabaseMissing('educational_modules', ['module_number' => 6]);
    }

    public function test_admin_dapat_menambah_subbab_pdf_dan_mengganti_berkas_pdf_lama(): void
    {
        Storage::fake('local');

        $module = EducationalModule::create([
            'module_number' => 7,
            'title' => 'Modul Uji Subbab PDF',
            'description' => 'Deskripsi modul uji.',
            'estimated_time' => '12 Menit',
        ]);

        $storeResponse = $this->actingAs($this->admin)->post(route('admin.modules.topics.store', $module->id), [
            'topic_code' => '7.1',
            'title' => 'Subbab Versi Awal',
            'pdf_file' => $this->fakePdf('versi-1.pdf'),
        ]);

        $storeResponse->assertRedirect(route('admin.modules.index'));
        $storeResponse->assertSessionHas('success');

        $topic = ModuleTopic::where('educational_module_id', $module->id)->firstOrFail();
        $oldPath = $topic->pdf_path;
        Storage::disk('local')->assertExists($oldPath);

        $updateResponse = $this->actingAs($this->admin)->put(route('admin.topics.update', $topic->id), [
            'topic_code' => '7.1',
            'title' => 'Subbab Versi Revisi',
            'youtube_video_id' => 'https://www.youtube.com/watch?v=e7zP-b-YnS0',
            'pdf_file' => $this->fakePdf('versi-2.pdf'),
        ]);

        $updateResponse->assertRedirect(route('admin.modules.index'));
        $updateResponse->assertSessionHas('success');

        $topic->refresh();
        $this->assertSame('Subbab Versi Revisi', $topic->title);
        $this->assertSame('e7zP-b-YnS0', $topic->youtube_video_id);
        $this->assertSame('versi-2.pdf', $topic->pdf_original_name);
        $this->assertNotSame($oldPath, $topic->pdf_path);
        Storage::disk('local')->assertMissing($oldPath);
        Storage::disk('local')->assertExists($topic->pdf_path);
    }

    public function test_mahasiswa_tidak_dapat_mengunggah_atau_mengubah_pdf_subbab(): void
    {
        Storage::fake('local');

        $module = EducationalModule::create([
            'module_number' => 8,
            'title' => 'Modul Terlindungi',
            'description' => 'Deskripsi.',
            'estimated_time' => '10 Menit',
        ]);

        $topic = ModuleTopic::create([
            'educational_module_id' => $module->id,
            'topic_code' => '8.1',
            'title' => 'Subbab Asli',
            'order_index' => 1,
        ]);

        $this->actingAs($this->mahasiswa)
            ->post(route('admin.modules.topics.store', $module->id), [
                'topic_code' => '8.2',
                'title' => 'Subbab Ilegal',
                'pdf_file' => $this->fakePdf('ilegal.pdf'),
            ])
            ->assertForbidden();

        $this->actingAs($this->mahasiswa)
            ->put(route('admin.topics.update', $topic->id), [
                'topic_code' => '8.1',
                'title' => 'Diubah Mahasiswa',
                'pdf_file' => $this->fakePdf('ubah.pdf'),
            ])
            ->assertForbidden();

        $this->assertSame([], Storage::disk('local')->allFiles());
    }

    public function test_menghapus_subbab_dan_modul_menghapus_berkas_pdf_dari_storage(): void
    {
        Storage::fake('local');

        $module = EducationalModule::create([
            'module_number' => 9,
            'title' => 'Modul Uji Bersih Storage',
            'description' => 'Deskripsi.',
            'estimated_time' => '10 Menit',
        ]);

        $path1 = $this->fakePdf('satu.pdf')->store('module-topics', 'local');
        $path2 = $this->fakePdf('dua.pdf')->store('module-topics', 'local');

        $topic1 = ModuleTopic::create([
            'educational_module_id' => $module->id,
            'topic_code' => '9.1',
            'title' => 'Subbab Satu',
            'pdf_path' => $path1,
            'pdf_original_name' => 'satu.pdf',
            'pdf_size' => 1024,
            'order_index' => 1,
        ]);

        $topic2 = ModuleTopic::create([
            'educational_module_id' => $module->id,
            'topic_code' => '9.2',
            'title' => 'Subbab Dua',
            'pdf_path' => $path2,
            'pdf_original_name' => 'dua.pdf',
            'pdf_size' => 1024,
            'order_index' => 2,
        ]);

        $this->actingAs($this->admin)
            ->delete(route('admin.topics.destroy', $topic1->id))
            ->assertRedirect(route('admin.modules.index'));

        Storage::disk('local')->assertMissing($path1);
        Storage::disk('local')->assertExists($path2);

        $this->actingAs($this->admin)
            ->delete(route('admin.modules.destroy', $module->id))
            ->assertRedirect(route('admin.modules.index'));

        Storage::disk('local')->assertMissing($path2);
        $this->assertModelMissing($topic2);
    }

    public function test_mahasiswa_wajib_pretest_sebelum_mengakses_flipbook_dan_stream_pdf(): void
    {
        Storage::fake('local');

        $module = EducationalModule::create([
            'module_number' => 10,
            'title' => 'Modul Gating Flipbook',
            'description' => 'Deskripsi.',
            'estimated_time' => '15 Menit',
        ]);

        $storedPath = $this->fakePdf('materi-flipbook.pdf')->store('module-topics', 'local');

        $topic = ModuleTopic::create([
            'educational_module_id' => $module->id,
            'topic_code' => '10.1',
            'title' => 'Subbab Flipbook Terkunci',
            'pdf_path' => $storedPath,
            'pdf_original_name' => 'materi-flipbook.pdf',
            'pdf_size' => 2048,
            'order_index' => 1,
        ]);

        // Sebelum Pre-Test: halaman baca dan PDF stream ditolak / dialihkan ke Pre-Test.
        $this->actingAs($this->mahasiswa)
            ->get(route('modules.topic', [$module->id, $topic->id]))
            ->assertRedirect(route('modules.pretest.show', $module->id));

        $this->actingAs($this->mahasiswa)
            ->get(route('modules.topic.pdf', [$module->id, $topic->id]))
            ->assertRedirect(route('modules.pretest.show', $module->id));

        $this->actingAs($this->mahasiswa)
            ->getJson(route('modules.topic.pdf', [$module->id, $topic->id]))
            ->assertForbidden();

        // Catat penyelesaian Pre-Test modul 10.
        EvaluationResponse::create([
            'user_id' => $this->mahasiswa->id,
            'type' => 'pre_test',
            'module_number' => 10,
            'raw_answers' => ['1' => 'A'],
            'total_score' => 80,
            'submitted_at' => now(),
        ]);

        $pageResponse = $this->actingAs($this->mahasiswa)
            ->get(route('modules.topic', [$module->id, $topic->id]));

        $pageResponse->assertOk();
        $pageResponse->assertSee('data-sikera-flipbook', false);
        $pageResponse->assertSee(route('modules.topic.pdf', [$module->id, $topic->id]), false);
        $pageResponse->assertSee('materi-flipbook.pdf');

        $pdfResponse = $this->actingAs($this->mahasiswa)
            ->get(route('modules.topic.pdf', [$module->id, $topic->id]));

        $pdfResponse->assertOk();
        $pdfResponse->assertHeader('content-type', 'application/pdf');
        $this->assertStringContainsString('inline;', (string) $pdfResponse->headers->get('content-disposition'));
    }

    public function test_subbab_lama_tanpa_pdf_menampilkan_fallback_html_dan_pdf_route_404(): void
    {
        $module = EducationalModule::create([
            'module_number' => 11,
            'title' => 'Modul Fallback HTML',
            'description' => 'Deskripsi.',
            'estimated_time' => '10 Menit',
        ]);

        $topic = ModuleTopic::create([
            'educational_module_id' => $module->id,
            'topic_code' => '11.1',
            'title' => 'Subbab Format Lama',
            'content_html' => '<p>Isi materi HTML lama tetap terbaca.</p>',
            'order_index' => 1,
        ]);

        $pageResponse = $this->actingAs($this->admin)
            ->get(route('modules.topic', [$module->id, $topic->id]));

        $pageResponse->assertOk();
        $pageResponse->assertDontSee('data-sikera-flipbook', false);
        $pageResponse->assertSee('Isi materi HTML lama tetap terbaca.');

        $this->actingAs($this->admin)
            ->get(route('modules.topic.pdf', [$module->id, $topic->id]))
            ->assertNotFound();
    }

    public function test_api_mengembalikan_metadata_pdf_dan_melindungi_stream_pdf_dengan_pretest(): void
    {
        Storage::fake('local');

        $module = EducationalModule::create([
            'module_number' => 12,
            'title' => 'Modul API Flipbook',
            'description' => 'Deskripsi.',
            'estimated_time' => '10 Menit',
        ]);

        $storedPath = $this->fakePdf('api-materi.pdf')->store('module-topics', 'local');

        $topic = ModuleTopic::create([
            'educational_module_id' => $module->id,
            'topic_code' => '12.1',
            'title' => 'Subbab API PDF',
            'pdf_path' => $storedPath,
            'pdf_original_name' => 'api-materi.pdf',
            'pdf_size' => 1536,
            'order_index' => 1,
        ]);

        $token = $this->mahasiswa->createToken('test')->plainTextToken;

        $this->withHeader('Authorization', "Bearer {$token}")
            ->getJson("/api/v1/modules/{$module->id}/topic/{$topic->id}/pdf")
            ->assertForbidden()
            ->assertJson(['requires_pretest' => true]);

        EvaluationResponse::create([
            'user_id' => $this->mahasiswa->id,
            'type' => 'pre_test',
            'module_number' => 12,
            'raw_answers' => ['1' => 'A'],
            'total_score' => 90,
            'submitted_at' => now(),
        ]);

        $this->withHeader('Authorization', "Bearer {$token}")
            ->getJson("/api/v1/modules/{$module->id}/topic/{$topic->id}")
            ->assertOk()
            ->assertJsonPath('data.topic.format', 'pdf_flipbook')
            ->assertJsonPath('data.topic.has_pdf', true)
            ->assertJsonPath('data.topic.pdf_original_name', 'api-materi.pdf')
            ->assertJsonPath('data.topic.pdf_url', url("/api/v1/modules/{$module->id}/topic/{$topic->id}/pdf"));

        $this->withHeader('Authorization', "Bearer {$token}")
            ->get("/api/v1/modules/{$module->id}/topic/{$topic->id}/pdf")
            ->assertOk()
            ->assertHeader('content-type', 'application/pdf');
    }
}
