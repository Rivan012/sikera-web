<?php

namespace Tests\Feature;

use App\Models\EducationalModule;
use App\Models\TestQuestion;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
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

        $module = EducationalModule::where('module_number', 1)->firstOrFail();
        $moduleData = collect($response->json('data.modules'))->firstWhere('id', $module->id);

        $this->assertSame(
            url("/api/v1/modules/{$module->id}/banner"),
            $moduleData['banner_image']
        );
    }

    public function test_api_modul_pretest_baca_dan_posttest(): void
    {
        $mhs = User::where('email', 'mhs@unib.ac.id')->first();
        $token = $mhs->createToken('test')->plainTextToken;
        $module1 = EducationalModule::where('module_number', 1)->first();
        $topic1 = $module1->topics()->first();

        $moduleDetail = $this->withHeader('Authorization', "Bearer {$token}")
            ->getJson("/api/v1/modules/{$module1->id}");

        $moduleDetail->assertOk()
            ->assertJsonPath('data.module.banner_image', url("/api/v1/modules/{$module1->id}/banner"));

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

    public function test_api_returns_404_for_removed_trivia_and_myth_fact_endpoints(): void
    {
        $mhs = User::where('email', 'sample1@unib.ac.id')->first();
        $token = $mhs->createToken('test')->plainTextToken;

        foreach (['trivia', 'myth-fact'] as $feature) {
            $this->withHeader('Authorization', "Bearer {$token}")
                ->getJson("/api/v1/gamification/{$feature}")
                ->assertNotFound();

            $this->withHeader('Authorization', "Bearer {$token}")
                ->postJson("/api/v1/gamification/{$feature}")
                ->assertNotFound();
        }

        $this->withHeader('Authorization', "Bearer {$token}")
            ->getJson('/api/v1/gamification/diary')
            ->assertOk()
            ->assertJson(['success' => true]);
    }

    public function test_api_module_banner_returns_png_jpg_and_svg_content_types(): void
    {
        $module = EducationalModule::where('module_number', 1)->firstOrFail();
        $createdFiles = [];

        try {
            foreach ([
                'png' => 'image/png',
                'jpg' => 'image/jpeg',
                'svg' => 'image/svg+xml',
            ] as $extension => $contentType) {
                $fileName = 'api-banner-'.Str::uuid().'.'.$extension;
                $filePath = public_path($fileName);
                file_put_contents($filePath, '<svg></svg>');
                $createdFiles[] = $filePath;

                $module->update(['banner_image' => '/'.$fileName]);

                $this->get("/api/v1/modules/{$module->id}/banner")
                    ->assertOk()
                    ->assertHeader('content-type', $contentType);
            }
        } finally {
            foreach ($createdFiles as $filePath) {
                if (is_file($filePath)) {
                    unlink($filePath);
                }
            }
        }

        $token = User::where('email', 'sample1@unib.ac.id')->firstOrFail()
            ->createToken('test')
            ->plainTextToken;

        $modulesResponse = $this->withHeader('Authorization', "Bearer {$token}")
            ->getJson('/api/v1/modules');
        $modulesResponse->assertOk();
        $moduleData = collect($modulesResponse->json('data.modules'))->firstWhere('id', $module->id);

        $this->assertSame(
            url("/api/v1/modules/{$module->id}/banner"),
            $moduleData['banner_image']
        );
    }
}
