<?php

namespace Tests\Feature;

use App\Models\EvaluationResponse;
use App\Models\TestQuestion;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AdminEvaluationTest extends TestCase
{
    use DatabaseTransactions;

    protected User $admin;

    protected User $mahasiswa;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::where('email', 'admin_eval_test@sikera.id')->first() ?? User::create([
            'name' => 'Admin Evaluasi Test',
            'email' => 'admin_eval_test@sikera.id',
            'role' => 'admin',
            'password' => Hash::make('password'),
            'is_biodata_filled' => true,
        ]);

        $this->mahasiswa = User::where('email', 'mhs_eval_test@sikera.id')->first() ?? User::create([
            'name' => 'Aulia Rahmawati',
            'initials' => 'AR',
            'email' => 'mhs_eval_test@sikera.id',
            'nim' => 'G1A026077',
            'role' => 'mahasiswa',
            'gender' => 'P',
            'usia' => 20,
            'prodi' => 'Kedokteran',
            'fakultas' => 'Kedokteran dan Ilmu Kesehatan',
            'password' => Hash::make('password'),
            'is_biodata_filled' => true,
        ]);
    }

    public function test_admin_dapat_mengakses_halaman_daftar_evaluasi(): void
    {
        $response = $this->actingAs($this->admin)->get(route('admin.evaluations.index'));

        $response->assertStatus(200);
        $response->assertSee('Analisis &amp; Lembar Jawaban Evaluasi', false);
        $response->assertSee('Total Submission');
        $response->assertSee('Rata-Rata N-Gain Score');
    }

    public function test_non_admin_ditolak_mengakses_halaman_evaluasi(): void
    {
        // Mahasiswa ditolak dengan status 403
        $responseMhs = $this->actingAs($this->mahasiswa)->get(route('admin.evaluations.index'));
        $responseMhs->assertStatus(403);

        // Guest diarahkan ke halaman login
        auth()->logout();
        $responseGuest = $this->get(route('admin.evaluations.index'));
        $responseGuest->assertRedirect(route('login'));
    }

    public function test_admin_dapat_melihat_detail_lembar_jawaban_dan_komparasi_ngain(): void
    {
        $question = TestQuestion::create([
            'module_target' => 1,
            'question_text' => 'Organ utama yang berfungsi menghasilkan sel telur (ovum) adalah?',
            'options' => [
                ['label' => 'A', 'text' => 'Ovarium (Indung Telur)'],
                ['label' => 'B', 'text' => 'Tuba Falopi'],
                ['label' => 'C', 'text' => 'Uterus (Rahim)'],
                ['label' => 'D', 'text' => 'Vagina'],
            ],
            'correct_answer' => 'A',
            'explanation' => 'Ovarium menghasilkan hormon estrogen dan progesteron serta sel ovum matang.',
        ]);

        // 1. Buat Pre-Test (skor 40, jawaban salah 'B')
        EvaluationResponse::create([
            'user_id' => $this->mahasiswa->id,
            'type' => 'pre_test',
            'module_number' => 1,
            'raw_answers' => [$question->id => 'B'],
            'scores_per_module' => ['Modul 1' => 40],
            'total_score' => 40,
            'submitted_at' => now()->subDay(),
        ]);

        // 2. Buat Post-Test (skor 100, jawaban benar 'A', N-Gain 1.0)
        $posttest = EvaluationResponse::create([
            'user_id' => $this->mahasiswa->id,
            'type' => 'post_test',
            'module_number' => 1,
            'raw_answers' => [$question->id => 'A'],
            'scores_per_module' => ['Modul 1' => 100],
            'total_score' => 100,
            'n_gain_score' => 1.0,
            'submitted_at' => now(),
        ]);

        $response = $this->actingAs($this->admin)->get(route('admin.evaluations.show', $posttest->id));

        $response->assertStatus(200);
        $response->assertSee('Lembar Jawaban: Aulia Rahmawati');
        $response->assertSee('Kedokteran');
        $response->assertSee('G1A026077');
        $response->assertSee('Organ utama yang berfungsi menghasilkan sel telur');
        $response->assertSee('BENAR');
        $response->assertSee('Ovarium (Indung Telur)');
        $response->assertSee('Pilihan Responden &bull; Kunci Benar', false);
        $response->assertSee('Pembahasan Ilmiah &amp; Analisis Medis', false);
        $response->assertSee('Kalkulasi N-Gain Efektivitas Riset');
        $response->assertSee('1.000');
        $response->assertSee('Efektivitas Tinggi');
    }

    public function test_filter_dan_pencarian_evaluasi(): void
    {
        $eval = EvaluationResponse::create([
            'user_id' => $this->mahasiswa->id,
            'type' => 'post_test',
            'module_number' => 2,
            'raw_answers' => ['1' => 'A'],
            'scores_per_module' => ['Modul 2' => 85],
            'total_score' => 85,
            'n_gain_score' => 0.75,
            'submitted_at' => now(),
        ]);

        // Filter tipe post_test
        $responseType = $this->actingAs($this->admin)->get(route('admin.evaluations.index', [
            'type' => 'post_test',
            'module' => '2',
        ]));
        $responseType->assertStatus(200);
        $responseType->assertSee('Aulia Rahmawati');
        $responseType->assertSee('Modul 2');
        $responseType->assertSee('85');

        // Cari berdasarkan nama
        $responseSearch = $this->actingAs($this->admin)->get(route('admin.evaluations.index', [
            'q' => 'Aulia',
        ]));
        $responseSearch->assertStatus(200);
        $responseSearch->assertSee('Aulia Rahmawati');
    }

    public function test_admin_dapat_melihat_jawaban_salah_dan_kunci_jawaban_yang_benar(): void
    {
        $question = TestQuestion::create([
            'module_target' => 2,
            'question_text' => 'Berapa kali sebaiknya pembalut diganti dalam sehari saat menstruasi?',
            'options' => [
                ['label' => 'A', 'text' => '1 kali sehari'],
                ['label' => 'B', 'text' => 'Setiap 4-6 jam sekali (3-5 kali sehari)'],
                ['label' => 'C', 'text' => 'Hanya jika sudah penuh'],
                ['label' => 'D', 'text' => '2 hari sekali'],
            ],
            'correct_answer' => 'B',
            'explanation' => 'Mengganti pembalut teratur setiap 4-6 jam mencegah kelembaban berlebih dan infeksi jamur/bakteri.',
        ]);

        $eval = EvaluationResponse::create([
            'user_id' => $this->mahasiswa->id,
            'type' => 'post_test',
            'module_number' => 2,
            'raw_answers' => [$question->id => 'A'], // Jawaban salah
            'scores_per_module' => ['Modul 2' => 0],
            'total_score' => 0,
            'submitted_at' => now(),
        ]);

        $response = $this->actingAs($this->admin)->get(route('admin.evaluations.show', $eval->id));

        $response->assertStatus(200);
        $response->assertSee('SALAH');
        $response->assertSee('Pilihan Responden (Salah)');
        $response->assertSee('Kunci Jawaban yang Benar');
        $response->assertSee('Setiap 4-6 jam sekali');
    }

    public function test_admin_dapat_mengunduh_data_posttest_format_excel(): void
    {
        $question = TestQuestion::create([
            'module_target' => 1,
            'question_text' => 'Berapa hari rata-rata siklus menstruasi normal?',
            'options' => [
                ['label' => 'A', 'text' => '14 hari'],
                ['label' => 'B', 'text' => '28 hari (rentang 21-35 hari)'],
                ['label' => 'C', 'text' => '45 hari'],
                ['label' => 'D', 'text' => '60 hari'],
            ],
            'correct_answer' => 'B',
            'explanation' => 'Siklus normal berkisar antara 21-35 hari dengan rata-rata 28 hari.',
        ]);

        EvaluationResponse::create([
            'user_id' => $this->mahasiswa->id,
            'type' => 'post_test',
            'module_number' => 1,
            'raw_answers' => [$question->id => 'B'],
            'scores_per_module' => ['Modul 1' => 100],
            'total_score' => 100,
            'n_gain_score' => 0.85,
            'submitted_at' => now(),
        ]);

        $response = $this->actingAs($this->admin)->get(route('admin.evaluations.export'));

        $response->assertStatus(200);
        $this->assertStringContainsString('Data_Post_Test_SIKERA_', $response->headers->get('content-disposition'));
        $this->assertStringContainsString('.xlsx', $response->headers->get('content-disposition'));
    }

    public function test_non_admin_tidak_dapat_mengunduh_excel(): void
    {
        $responseMhs = $this->actingAs($this->mahasiswa)->get(route('admin.evaluations.export'));
        $responseMhs->assertStatus(403);

        auth()->logout();
        $responseGuest = $this->get(route('admin.evaluations.export'));
        $responseGuest->assertRedirect(route('login'));
    }
}
