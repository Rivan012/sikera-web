<?php

namespace Tests\Feature;

use App\Models\EducationalModule;
use App\Models\ModuleProgress;
use App\Models\ModuleTopic;
use App\Models\TestQuestion;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia as Assert;
use PHPUnit\Framework\Attributes\TestWith;
use Tests\TestCase;

class AlurSistemSikeraTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }

    public function test_akses_awal_merender_landing_inertia(): void
    {
        $this->get('/')
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page->component('Welcome'));
    }

    public function test_halaman_masuk_bisa_diakses_langsung(): void
    {
        $this->get('/login')
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page->component('Auth/Login'));
    }

    public function test_login_otomatis_mengarahkan_sesuai_role(): void
    {
        $mhsResponse = $this->post('/login', [
            'email' => 'sample1@unib.ac.id',
            'password' => 'password',
        ]);
        $mhsResponse->assertRedirect(route('mahasiswa.dashboard'));

        $this->post('/force-logout');

        $dosenResponse = $this->post('/login', [
            'email' => 'dosen@unib.ac.id',
            'password' => 'password',
        ]);
        $dosenResponse->assertRedirect(route('dosen.dashboard'));

        $this->post('/force-logout');

        $adminResponse = $this->post('/login', [
            'email' => 'admin@sikera.id',
            'password' => 'password',
        ]);
        $adminResponse->assertRedirect(route('admin.dashboard'));
    }

    #[TestWith(['UINFAS - Fakultas Tarbiyah dan Tadris'])]
    #[TestWith(['DEHASEN - Fakultas Hukum'])]
    #[TestWith(['Fakultas Hukum (FH)'])]
    #[TestWith([null])]
    public function test_pendaftaran_mahasiswa_baru_dengan_biodata_lengkap(?string $fakultas): void
    {
        $this->get(route('register'))
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page->component('Auth/Register'));

        $response = $this->post('/register', [
            'name' => 'Bunga Santoso',
            'menarche_age' => 13,
            'initials' => 'BS',
            'usia' => 19,
            'gender' => 'P',
            'agama' => 'Islam',
            'prodi' => 'Informatika',
            'fakultas' => $fakultas,
            'pendidikan_terakhir' => 'SMA/SMK/MA/Sederajat',
            'nim' => 'G1A026099',
            'email' => 'bunga@unib.ac.id',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $this->assertAuthenticated();
        $user = auth()->user();
        $this->assertEquals('mahasiswa', $user->role);
        $this->assertDatabaseHas('users', ['id' => $user->id, 'menarche_age' => 13, 'fakultas' => $fakultas]);
        $this->assertTrue($user->is_biodata_filled);
        $this->assertFalse($user->pretest_completed);

        $response->assertRedirect(route('mahasiswa.dashboard'));
    }

    public function test_pretest_modul_dikerjakan_sebelum_mengakses_materi_modul(): void
    {
        $mhs = User::where('email', 'mhs@unib.ac.id')->firstOrFail();
        $this->actingAs($mhs);

        $module1 = EducationalModule::where('module_number', 1)->firstOrFail();

        $this->get("/modules/{$module1->id}")
            ->assertRedirect(route('modules.pretest.show', $module1->id));

        $this->get("/modules/{$module1->id}/pretest")
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->component('Evaluation/ModulePretest')
                ->where('module.module_number', 1)
            );

        $questions = TestQuestion::preTest()->where('module_target', 1)->get();
        $answers = [];
        foreach ($questions as $q) {
            $answers[$q->id] = $q->correct_answer;
        }

        $this->post("/modules/{$module1->id}/pretest", ['answers' => $answers])
            ->assertRedirect(route('modules.show', $module1->id));

        $this->get("/modules/{$module1->id}")
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->component('Modules/Show')
                ->where('module.title', $module1->title)
            );
    }

    public function test_posttest_terpadu_terbuka_3_hari_setelah_seluruh_modul_selesai(): void
    {
        $mhs = User::where('email', 'mhs@unib.ac.id')->firstOrFail();
        $this->actingAs($mhs);

        $module1 = EducationalModule::where('module_number', 1)->firstOrFail();
        $this->get("/modules/{$module1->id}/posttest")
            ->assertRedirect(route('posttest.show'));

        foreach (ModuleTopic::all() as $topic) {
            ModuleProgress::updateOrCreate(
                ['user_id' => $mhs->id, 'module_topic_id' => $topic->id],
                ['is_completed' => true, 'completed_at' => now()]
            );
        }

        $this->get('/posttest')
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->component('Evaluation/Posttest')
                ->where('gate.state', 'waiting_delay')
                ->where('gate.is_posttest_unlocked', false)
            );

        $this->travel(3)->days();
        $this->travel(1)->minutes();

        $this->get('/posttest')
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->component('Evaluation/Posttest')
                ->where('gate.state', 'ready')
                ->where('gate.is_posttest_unlocked', true)
            );

        $questions = TestQuestion::postTest()->get();
        $answers = [];
        foreach ($questions as $q) {
            $answers[$q->id] = $q->correct_answer;
        }

        $this->post('/posttest', ['answers' => $answers])
            ->assertRedirect(route('posttest.show'));

        $this->assertAuthenticatedAs($mhs);
        $this->assertDatabaseHas('evaluation_responses', [
            'user_id' => $mhs->id,
            'type' => 'post_test',
            'module_number' => null,
            'total_score' => 100,
        ]);
    }

    public function test_kalender_haid_hanya_untuk_perempuan(): void
    {
        $mhsP = User::where('email', 'mhs@unib.ac.id')->firstOrFail();
        $this->actingAs($mhsP);

        $this->get('/selfcare')
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->component('SelfCare/Index')
                ->where('isFemale', true)
            );

        $this->post('/selfcare/period', [
            'start_date' => now()->format('Y-m-d'),
            'cycle_length' => 28,
            'period_duration' => 5,
            'flow_level' => 'sedang',
            'flow_color' => 'Merah Terang',
            'nrs_pain_score' => 3,
        ])->assertRedirect(route('selfcare.index'));
        $this->assertDatabaseHas('period_logs', ['user_id' => $mhsP->id]);

        $this->post('/force-logout');

        $mhsL = User::where('email', 'sample2@unib.ac.id')->firstOrFail();
        $this->actingAs($mhsL);

        $this->get('/selfcare')
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->component('SelfCare/Index')
                ->where('isFemale', false)
            );

        $this->post('/selfcare/period', [
            'start_date' => now()->format('Y-m-d'),
            'cycle_length' => 28,
            'period_duration' => 5,
            'flow_level' => 'sedang',
            'flow_color' => 'Merah Terang',
            'nrs_pain_score' => 3,
        ])->assertRedirect(route('selfcare.index'));
        $this->assertDatabaseMissing('period_logs', ['user_id' => $mhsL->id]);
    }

    public function test_alur_super_admin_dashboard_grafik(): void
    {
        $admin = User::where('role', 'admin')->firstOrFail();
        $this->actingAs($admin)
            ->get('/admin/dashboard')
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->component('Admin/Dashboard')
                ->has('scoresByModule')
                ->has('avgNGain')
            );
    }

    public function test_superadmin_dapat_melihat_dan_memfilter_halaman_daftar_user(): void
    {
        $admin = User::where('role', 'admin')->firstOrFail();
        $this->actingAs($admin)
            ->get('/admin/users?role=all')
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->component('Admin/Users/Index')
                ->where('roleFilter', 'all')
            );
    }

    public function test_superadmin_dapat_mengakses_dan_mengelola_modul(): void
    {
        $admin = User::where('role', 'admin')->firstOrFail();
        $this->actingAs($admin)
            ->get('/admin/modules')
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page->component('Admin/Modules/Index'));
    }

    public function test_superadmin_dapat_mengakses_halaman_lembar_data_google_sheets(): void
    {
        $admin = User::where('role', 'admin')->firstOrFail();
        $this->actingAs($admin)
            ->get('/admin/sheets')
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page->component('Admin/Sheets/Index'));
    }

    public function test_halaman_pretest_dapat_merender_soal_dengan_format_opsi_apapun(): void
    {
        $mhs = User::where('email', 'mhs@unib.ac.id')->firstOrFail();
        $this->actingAs($mhs);

        $module = EducationalModule::create([
            'module_number' => 999,
            'title' => 'Modul Uji Format Opsi',
            'subtitle' => 'Sub judul modul uji',
            'description' => 'Deskripsi modul uji',
            'estimated_time' => '10 Menit',
        ]);

        TestQuestion::create([
            'question_type' => TestQuestion::TYPE_PRETEST,
            'module_target' => 999,
            'question_text' => 'Berapa usia reproduksi sehat menurut BKKBN?',
            'options' => [
                'A' => '21 tahun perempuan dan 25 tahun laki-laki',
                'B' => '15 tahun perempuan dan 17 tahun laki-laki',
                'C' => '17 tahun perempuan dan 19 tahun laki-laki',
                'D' => 'Bebas usia berapa pun',
            ],
            'correct_answer' => 'A',
            'explanation' => 'Penjelasan usia reproduksi sehat.',
        ]);

        $this->get("/modules/{$module->id}/pretest")
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->component('Evaluation/ModulePretest')
                ->where('questions.0.options.0.text', '21 tahun perempuan dan 25 tahun laki-laki')
            );
    }
}
