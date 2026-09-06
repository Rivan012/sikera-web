<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\EducationalModule;
use App\Models\ModuleTopic;
use App\Models\TestQuestion;
use App\Models\CaseStudy;
use App\Models\KesproFeed;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MobileApiTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }

    public function test_api_health_check(): void
    {
        $response = $this->getJson('/api/v1/health');
        $response->assertStatus(200)
            ->assertJson(['success' => true]);
    }

    public function test_api_register_dan_login(): void
    {
        // 1. Register Mobile
        $regResponse = $this->postJson('/api/v1/auth/register', [
            'name' => 'Dewi Anggraini',
            'initials' => 'DA',
            'usia' => 18,
            'gender' => 'P',
            'agama' => 'Islam',
            'prodi' => 'Farmasi',
            'fakultas' => 'Fakultas Matematika dan Ilmu Pengetahuan Alam (FMIPA)',
            'pendidikan_terakhir' => 'SMA/SMK/MA/Sederajat',
            'nim' => 'F1A026001',
            'email' => 'dewi@unib.ac.id',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $regResponse->assertStatus(201)
            ->assertJsonStructure([
                'success',
                'data' => [
                    'token',
                    'user' => ['id', 'name', 'email', 'gender'],
                ],
            ]);

        $token = $regResponse->json('data.token');

        // 2. Cek Profil /me dengan Bearer Token
        $meResponse = $this->withHeader('Authorization', "Bearer {$token}")
            ->getJson('/api/v1/auth/me');

        $meResponse->assertStatus(200)
            ->assertJson(['success' => true, 'data' => ['user' => ['email' => 'dewi@unib.ac.id']]]);

        // 3. Login Mobile
        $loginResponse = $this->postJson('/api/v1/auth/login', [
            'email' => 'dewi@unib.ac.id',
            'password' => 'password123',
        ]);

        $loginResponse->assertStatus(200)
            ->assertJsonStructure(['success', 'data' => ['token']]);
    }

    public function test_api_dashboard_mahasiswa(): void
    {
        $mhs = User::where('email', 'sample1@unib.ac.id')->first();
        $token = $mhs->createToken('test')->plainTextToken;

        $response = $this->withHeader('Authorization', "Bearer {$token}")
            ->getJson('/api/v1/dashboard');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'data' => [
                    'user',
                    'modules',
                    'posters',
                    'selfcare',
                ],
            ]);
    }

    public function test_api_modul_pretest_baca_dan_posttest(): void
    {
        $mhs = User::where('email', 'mhs@unib.ac.id')->first();
        $token = $mhs->createToken('test')->plainTextToken;
        $module1 = EducationalModule::where('module_number', 1)->first();
        $topic1 = $module1->topics()->first();

        // 1. Coba baca materi sebelum Pre-Test -> Ditolak (403 Forbidden)
        $readForbidden = $this->withHeader('Authorization', "Bearer {$token}")
            ->getJson("/api/v1/modules/{$module1->id}/topic/{$topic1->id}");

        $readForbidden->assertStatus(403)
            ->assertJson(['requires_pretest' => true]);

        // 2. Ambil Soal Pre-Test
        $pretestQuestions = $this->withHeader('Authorization', "Bearer {$token}")
            ->getJson("/api/v1/modules/{$module1->id}/pretest");

        $pretestQuestions->assertStatus(200)
            ->assertJsonStructure(['data' => ['questions']]);

        // 3. Submit Pre-Test
        $questions = TestQuestion::where('module_target', 1)->get();
        $answers = [];
        foreach ($questions as $q) {
            $answers[$q->id] = $q->correct_answer;
        }

        $submitPretest = $this->withHeader('Authorization', "Bearer {$token}")
            ->postJson("/api/v1/modules/{$module1->id}/pretest", [
                'answers' => $answers,
            ]);

        $submitPretest->assertStatus(200)
            ->assertJson(['success' => true, 'data' => ['score' => 100]]);

        // 4. Setelah Pre-Test, baca materi sekarang berhasil (200 OK)
        $readSuccess = $this->withHeader('Authorization', "Bearer {$token}")
            ->getJson("/api/v1/modules/{$module1->id}/topic/{$topic1->id}");

        $readSuccess->assertStatus(200)
            ->assertJson(['success' => true, 'data' => ['points_earned' => 10]]);

        // 5. Submit Post-Test Modul 1
        $submitPosttest = $this->withHeader('Authorization', "Bearer {$token}")
            ->postJson("/api/v1/modules/{$module1->id}/posttest", [
                'answers' => $answers,
            ]);

        $submitPosttest->assertStatus(200)
            ->assertJsonStructure(['data' => ['posttest_score', 'n_gain_score']]);
    }

    public function test_api_selfcare_period_dan_bmi(): void
    {
        // 1. Mahasiswa Perempuan
        $mhsP = User::where('email', 'mhs@unib.ac.id')->first();
        $tokenP = $mhsP->createToken('test')->plainTextToken;

        $periodResp = $this->withHeader('Authorization', "Bearer {$tokenP}")
            ->postJson('/api/v1/selfcare/period', [
                'start_date' => now()->format('Y-m-d'),
                'cycle_length' => 28,
                'period_duration' => 5,
                'flow_level' => 'sedang',
                'flow_color' => 'Merah Terang',
                'nrs_pain_score' => 2,
            ]);

        $periodResp->assertStatus(201)
            ->assertJson(['success' => true]);

        // 2. BMI
        $bmiResp = $this->withHeader('Authorization', "Bearer {$tokenP}")
            ->postJson('/api/v1/selfcare/bmi', [
                'weight_kg' => 52.5,
                'height_cm' => 158.0,
            ]);

        $bmiResp->assertStatus(201)
            ->assertJsonStructure(['data' => ['bmi_log' => ['bmi_value', 'category']]]);
    }

    public function test_api_gamifikasi_trivia_dan_myth_fact(): void
    {
        $mhs = User::where('email', 'sample1@unib.ac.id')->first();
        $token = $mhs->createToken('test')->plainTextToken;

        // 1. Trivia
        $triviaResp = $this->withHeader('Authorization', "Bearer {$token}")
            ->getJson('/api/v1/gamification/trivia');

        $triviaResp->assertStatus(200);

        // 2. Mitos vs Fakta
        $mythResp = $this->withHeader('Authorization', "Bearer {$token}")
            ->getJson('/api/v1/gamification/myth-fact');

        $mythResp->assertStatus(200);
    }
}
