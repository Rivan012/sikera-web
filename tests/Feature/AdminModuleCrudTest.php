<?php

namespace Tests\Feature;

use App\Models\EducationalModule;
use App\Models\ModuleTopic;
use App\Models\TestQuestion;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AdminModuleCrudTest extends TestCase
{
    use DatabaseTransactions;

    protected User $admin;

    protected User $mahasiswa;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::where('role', 'admin')->first() ?? User::create([
            'name' => 'Admin Tester',
            'email' => 'admin_mod_test@sikera.id',
            'role' => 'admin',
            'password' => Hash::make('password'),
            'is_biodata_filled' => true,
        ]);

        $this->mahasiswa = User::where('role', 'mahasiswa')->first() ?? User::create([
            'name' => 'Mahasiswa Tester',
            'email' => 'mhs_mod_test@sikera.id',
            'role' => 'mahasiswa',
            'password' => Hash::make('password'),
            'is_biodata_filled' => true,
        ]);
    }

    public function test_superadmin_dapat_mengunduh_template_excel_modul(): void
    {
        $response = $this->actingAs($this->admin)->get(route('admin.modules.template'));

        $response->assertStatus(200);
        $response->assertHeader('content-disposition', 'attachment; filename=Format_Modul_Edukasi_SIKERA.xlsx');
    }

    public function test_mahasiswa_tidak_dapat_mengunduh_template_excel_modul(): void
    {
        $response = $this->actingAs($this->mahasiswa)->get(route('admin.modules.template'));

        $response->assertStatus(403);
    }

    public function test_superadmin_dapat_menghapus_modul_beserta_topik_dan_soal_terkait(): void
    {
        // 1. Buat data modul tiruan
        $module = EducationalModule::create([
            'module_number' => 888,
            'title' => 'Modul Uji Hapus Web',
            'subtitle' => 'Sub judul modul uji',
            'description' => 'Deskripsi modul uji',
            'estimated_time' => '15 Menit',
        ]);

        // 2. Buat topik turunan
        $topic = ModuleTopic::create([
            'educational_module_id' => $module->id,
            'topic_code' => 'MOD-888.1',
            'title' => 'Topik Uji Hapus',
            'content_html' => '<p>Konten uji</p>',
            'order_index' => 1,
        ]);

        // 3. Buat soal kuesioner terkait modul ini
        $question = TestQuestion::create([
            'module_target' => 888,
            'question_text' => 'Pertanyaan uji hapus modul?',
            'options' => ['A' => 'Opsi 1', 'B' => 'Opsi 2'],
            'correct_answer' => 'A',
            'explanation' => 'Penjelasan uji',
        ]);

        // 4. Lakukan penghapusan oleh admin via Web
        $response = $this->actingAs($this->admin)->delete(route('admin.modules.destroy', $module->id));

        $response->assertRedirect(route('admin.modules.index'));
        $response->assertSessionHas('success');

        // 5. Verifikasi modul, topik, dan soal terhapus dari database
        $this->assertDatabaseMissing('educational_modules', ['id' => $module->id]);
        $this->assertDatabaseMissing('module_topics', ['id' => $topic->id]);
        $this->assertDatabaseMissing('test_questions', ['id' => $question->id]);
    }

    public function test_mahasiswa_tidak_dapat_menghapus_modul_melalui_web(): void
    {
        $module = EducationalModule::create([
            'module_number' => 889,
            'title' => 'Modul Perlindungan Mahasiswa',
            'subtitle' => 'Sub judul',
            'description' => 'Deskripsi',
            'estimated_time' => '10 Menit',
        ]);

        $response = $this->actingAs($this->mahasiswa)->delete(route('admin.modules.destroy', $module->id));

        $response->assertStatus(403);
        $this->assertDatabaseHas('educational_modules', ['id' => $module->id]);
    }

    public function test_superadmin_dapat_menghapus_modul_melalui_rest_api(): void
    {
        $module = EducationalModule::create([
            'module_number' => 890,
            'title' => 'Modul Uji Hapus API',
            'subtitle' => 'Sub judul api',
            'description' => 'Deskripsi api',
            'estimated_time' => '10 Menit',
        ]);

        $topic = ModuleTopic::create([
            'educational_module_id' => $module->id,
            'topic_code' => 'MOD-890.1',
            'title' => 'Topik Uji API',
            'content_html' => '<p>Konten uji api</p>',
            'order_index' => 1,
        ]);

        $question = TestQuestion::create([
            'module_target' => 890,
            'question_text' => 'Pertanyaan uji API?',
            'options' => ['A' => 'Jawaban A', 'B' => 'Jawaban B'],
            'correct_answer' => 'A',
            'explanation' => 'Penjelasan api',
        ]);

        $response = $this->actingAs($this->admin)->deleteJson("/api/v1/modules/{$module->id}");

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
        ]);

        $this->assertDatabaseMissing('educational_modules', ['id' => $module->id]);
        $this->assertDatabaseMissing('module_topics', ['id' => $topic->id]);
        $this->assertDatabaseMissing('test_questions', ['id' => $question->id]);
    }

    public function test_mahasiswa_tidak_dapat_menghapus_modul_melalui_rest_api(): void
    {
        $module = EducationalModule::create([
            'module_number' => 891,
            'title' => 'Modul Proteksi API Mahasiswa',
            'subtitle' => 'Sub judul',
            'description' => 'Deskripsi',
            'estimated_time' => '10 Menit',
        ]);

        $response = $this->actingAs($this->mahasiswa)->deleteJson("/api/v1/modules/{$module->id}");

        $response->assertStatus(403);
        $this->assertDatabaseHas('educational_modules', ['id' => $module->id]);
    }

    public function test_superadmin_dapat_mengunggah_dan_mengimpor_modul_lewat_excel(): void
    {
        $templatePath = public_path('templates/format_modul_sikera.xlsx');
        $uploadedFile = new UploadedFile(
            $templatePath,
            'format_modul_sikera.xlsx',
            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            null,
            true
        );

        $response = $this->actingAs($this->admin)->post(route('admin.modules.import'), [
            'excel_file' => $uploadedFile,
            'overwrite' => '1',
        ]);

        $response->assertRedirect(route('admin.modules.index'));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('educational_modules', [
            'module_number' => 1,
            'title' => 'Anatomi, Fisiologi, & Higienitas Reproduksi',
        ]);

        $this->assertDatabaseHas('module_topics', [
            'topic_code' => '1.1',
            'title' => 'Pengenalan Anatomi Organ Reproduksi Pria & Wanita',
        ]);

        $this->assertDatabaseHas('test_questions', [
            'module_target' => 1,
            'correct_answer' => 'B',
        ]);
    }

    public function test_mahasiswa_tidak_dapat_mengimpor_modul_lewat_excel(): void
    {
        $templatePath = public_path('templates/format_modul_sikera.xlsx');
        $uploadedFile = new UploadedFile(
            $templatePath,
            'format.xlsx',
            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            null,
            true
        );

        $response = $this->actingAs($this->mahasiswa)->post(route('admin.modules.import'), [
            'excel_file' => $uploadedFile,
        ]);

        $response->assertStatus(403);
    }

    public function test_validasi_gagal_jika_unggah_file_bukan_excel(): void
    {
        $fakeFile = UploadedFile::fake()->create('dokumen.pdf', 100, 'application/pdf');

        $response = $this->actingAs($this->admin)->post(route('admin.modules.import'), [
            'excel_file' => $fakeFile,
        ]);

        $response->assertSessionHasErrors('excel_file');
    }

    public function test_superadmin_dapat_mengimpor_modul_lewat_rest_api(): void
    {
        $templatePath = public_path('templates/format_modul_sikera.xlsx');
        $uploadedFile = new UploadedFile(
            $templatePath,
            'format.xlsx',
            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            null,
            true
        );

        $response = $this->actingAs($this->admin)->postJson('/api/v1/modules/import', [
            'excel_file' => $uploadedFile,
            'overwrite' => true,
        ]);

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
        ]);
    }

    public function test_mahasiswa_tidak_dapat_mengimpor_modul_lewat_rest_api(): void
    {
        $templatePath = public_path('templates/format_modul_sikera.xlsx');
        $uploadedFile = new UploadedFile(
            $templatePath,
            'format.xlsx',
            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            null,
            true
        );

        $response = $this->actingAs($this->mahasiswa)->postJson('/api/v1/modules/import', [
            'excel_file' => $uploadedFile,
        ]);

        $response->assertStatus(403);
    }
}
