<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\EducationalModule;
use App\Models\ModuleTopic;
use App\Models\TestQuestion;
use App\Models\GoogleSheetSyncLog;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AlurSistemSikeraTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }

    public function test_akses_awal_redirect_ke_login(): void
    {
        $response = $this->get('/');
        $response->assertRedirect(route('login'));
    }

    public function test_halaman_masuk_bisa_diakses_langsung(): void
    {
        $response = $this->get('/login');
        $response->assertStatus(200);
        $response->assertSee('SIKERA');
        $response->assertSee('Alamat Email');
        $response->assertSee('Kata Sandi');
    }

    public function test_login_otomatis_mengarahkan_sesuai_role(): void
    {
        // 1. Login Mahasiswa -> diarahkan ke dashboard mahasiswa
        $mhsResponse = $this->post('/login', [
            'email' => 'sample1@unib.ac.id',
            'password' => 'password',
        ]);
        $mhsResponse->assertRedirect(route('mahasiswa.dashboard'));

        $this->post('/force-logout');

        // 2. Login Dosen PA -> diarahkan ke dashboard dosen
        $dosenResponse = $this->post('/login', [
            'email' => 'dosen@unib.ac.id',
            'password' => 'password',
        ]);
        $dosenResponse->assertRedirect(route('dosen.dashboard'));

        $this->post('/force-logout');

        // 3. Login Super Admin -> diarahkan ke dashboard admin
        $adminResponse = $this->post('/login', [
            'email' => 'admin@sikera.id',
            'password' => 'password',
        ]);
        $adminResponse->assertRedirect(route('admin.dashboard'));
    }

    public function test_pendaftaran_mahasiswa_baru_dengan_biodata_lengkap(): void
    {
        $response = $this->post('/register', [
            'name' => 'Budi Santoso',
            'initials' => 'BS',
            'usia' => 19,
            'gender' => 'L',
            'agama' => 'Islam',
            'prodi' => 'Informatika',
            'fakultas' => 'Fakultas Teknik (FT)',
            'pendidikan_terakhir' => 'SMA/SMK/MA/Sederajat',
            'nim' => 'G1A026099',
            'email' => 'budi@unib.ac.id',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $this->assertAuthenticated();
        $user = auth()->user();
        $this->assertEquals('mahasiswa', $user->role);
        $this->assertTrue($user->is_biodata_filled);
        $this->assertFalse($user->pretest_completed);

        // Langsung diarahkan ke Menu Utama Mahasiswa (tanpa pre-test di awal registrasi)
        $response->assertRedirect(route('mahasiswa.dashboard'));
    }

    public function test_pretest_modul_dikerjakan_sebelum_mengakses_materi_modul(): void
    {
        $mhs = User::where('email', 'mhs@unib.ac.id')->first();
        $this->actingAs($mhs);

        $module1 = EducationalModule::where('module_number', 1)->first();

        // 1. Coba akses modul 1 sebelum pretest -> diredirect ke pretest modul
        $response = $this->get("/modules/{$module1->id}");
        $response->assertRedirect(route('modules.pretest.show', $module1->id));

        // 2. Akses halaman Pre-Test Modul 1
        $pretestPage = $this->get("/modules/{$module1->id}/pretest");
        $pretestPage->assertStatus(200);
        $pretestPage->assertSee("Pre-Test Modul 1");

        // 3. Submit Pre-Test Modul 1
        $questions = TestQuestion::where('module_target', 1)->get();
        $payload = ['_token' => csrf_token()];
        foreach ($questions as $q) {
            $payload['q_' . $q->id] = $q->correct_answer;
        }

        $submitResp = $this->post("/modules/{$module1->id}/pretest", $payload);
        $submitResp->assertRedirect(route('modules.show', $module1->id));

        // 4. Setelah Pre-Test modul 1 selesai, sekarang bisa mengakses materi modul 1!
        $modulePage = $this->get("/modules/{$module1->id}");
        $modulePage->assertStatus(200);
        $modulePage->assertSee($module1->title);
    }

    public function test_posttest_modul_dikerjakan_setelah_selesai_materi(): void
    {
        $mhs = User::where('email', 'sample1@unib.ac.id')->first();
        $this->actingAs($mhs);

        $module1 = EducationalModule::where('module_number', 1)->first();

        // 1. Buka halaman Post-Test Modul 1
        $posttestPage = $this->get("/modules/{$module1->id}/posttest");
        $posttestPage->assertStatus(200);
        $posttestPage->assertSee("Post-Test Modul 1");

        // 2. Submit Post-Test Modul 1
        $questions = TestQuestion::where('module_target', 1)->get();
        $payload = ['_token' => csrf_token()];
        foreach ($questions as $q) {
            $payload['q_' . $q->id] = $q->correct_answer;
        }

        $submitResp = $this->post("/modules/{$module1->id}/posttest", $payload);
        $submitResp->assertRedirect(route('modules.index'));

        $this->assertDatabaseHas('evaluation_responses', [
            'user_id' => $mhs->id,
            'type' => 'post_test',
            'module_number' => 1,
            'total_score' => 100,
        ]);
    }

    public function test_kalender_haid_hanya_untuk_perempuan(): void
    {
        // 1. Mahasiswa Perempuan (Aisyah - P) -> dapat melihat & mencatat haid
        $mhsP = User::where('email', 'mhs@unib.ac.id')->first();
        $this->actingAs($mhsP);

        $responseP = $this->get('/selfcare');
        $responseP->assertStatus(200);
        $responseP->assertSee('Pelacak Siklus Haid');

        $storeP = $this->post('/selfcare/period', [
            'start_date' => now()->format('Y-m-d'),
            'cycle_length' => 28,
            'period_duration' => 5,
            'flow_level' => 'sedang',
            'flow_color' => 'Merah Terang',
            'nrs_pain_score' => 3,
        ]);
        $storeP->assertRedirect(route('selfcare.index'));
        $this->assertDatabaseHas('period_logs', ['user_id' => $mhsP->id]);

        $this->post('/force-logout');

        // 2. Mahasiswa Laki-laki (Sample 2 - L) -> tab haid disembunyikan & tolak submit haid
        $mhsL = User::where('email', 'sample2@unib.ac.id')->first();
        $this->actingAs($mhsL);

        $responseL = $this->get('/selfcare');
        $responseL->assertStatus(200);
        $responseL->assertDontSee('Pelacak Siklus Haid');
        $responseL->assertSee('Kalkulator IMT &amp; Status Gizi', false);

        $storeL = $this->post('/selfcare/period', [
            'start_date' => now()->format('Y-m-d'),
            'cycle_length' => 28,
            'period_duration' => 5,
            'flow_level' => 'sedang',
            'flow_color' => 'Merah Terang',
            'nrs_pain_score' => 3,
        ]);
        // Ditolak dan dialihkan kembali dengan warning
        $storeL->assertRedirect(route('selfcare.index'));
        $this->assertDatabaseMissing('period_logs', ['user_id' => $mhsL->id]);
    }

    public function test_alur_super_admin_dashboard_grafik(): void
    {
        $admin = User::where('role', 'admin')->first();
        $this->actingAs($admin);

        $response = $this->get('/admin/dashboard');
        $response->assertStatus(200);
        $response->assertSee('Dashboard Grafik');
        $response->assertSee('Grafik Peningkatan Pemahaman');
        $response->assertSee('Tingkat Efektivitas (N-Gain)');
    }

    public function test_superadmin_dapat_melihat_dan_memfilter_halaman_daftar_user(): void
    {
        $admin = User::where('role', 'admin')->first();
        $this->actingAs($admin);

        $allResponse = $this->get('/admin/users?role=all');
        $allResponse->assertStatus(200);
        $allResponse->assertSee('Daftar Pengguna');
        $allResponse->assertSee('Aisyah Putri Maharani');
        $allResponse->assertSee('Dr. Rina Novita');
    }

    public function test_superadmin_dapat_mengakses_dan_mengelola_modul(): void
    {
        $admin = User::where('role', 'admin')->first();
        $this->actingAs($admin);

        $indexResponse = $this->get('/admin/modules');
        $indexResponse->assertStatus(200);
        $indexResponse->assertSee('Unggah Modul Edukasi Baru');
    }

    public function test_superadmin_dapat_mengakses_halaman_lembar_data_google_sheets(): void
    {
        $admin = User::where('role', 'admin')->first();
        $this->actingAs($admin);

        $response = $this->get('/admin/sheets');
        $response->assertStatus(200);
        $response->assertSee('Lembar Data Google Sheets');
    }
}
