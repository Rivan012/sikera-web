<?php

namespace Tests\Feature;

use App\Models\EvaluationResponse;
use App\Models\TestQuestion;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Hash;
use Inertia\Testing\AssertableInertia as Assert;
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
        $this->actingAs($this->admin)
            ->get(route('admin.evaluations.index'))
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->component('Admin/Evaluations/Index')
                ->has('evaluations.data')
                ->has('avgNGain')
            );
    }

    public function test_non_admin_ditolak_mengakses_halaman_evaluasi(): void
    {
        $this->actingAs($this->mahasiswa)
            ->get(route('admin.evaluations.index'))
            ->assertStatus(403);

        auth()->logout();
        $this->get(route('admin.evaluations.index'))
            ->assertRedirect(route('login'));
    }

    public function test_admin_dapat_melihat_detail_lembar_jawaban_dan_komparasi_ngain(): void
    {
        $question = TestQuestion::create([
            'question_type' => TestQuestion::TYPE_POSTTEST,
            'module_target' => null,
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

        EvaluationResponse::create([
            'user_id' => $this->mahasiswa->id,
            'type' => 'pre_test',
            'module_number' => 1,
            'raw_answers' => [$question->id => 'B'],
            'scores_per_module' => ['Modul 1' => 40],
            'total_score' => 40,
            'submitted_at' => now()->subDays(4),
        ]);

        $posttest = EvaluationResponse::create([
            'user_id' => $this->mahasiswa->id,
            'type' => 'post_test',
            'module_number' => null,
            'raw_answers' => [$question->id => 'A'],
            'scores_per_module' => null,
            'total_score' => 100,
            'n_gain_score' => 1.0,
            'submitted_at' => now(),
        ]);

        $this->actingAs($this->admin)
            ->get(route('admin.evaluations.show', $posttest->id))
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->component('Admin/Evaluations/Show')
                ->where('student.name', 'Aulia Rahmawati')
                ->where('student.nim', 'G1A026077')
                ->where('evaluation.total_score', 100)
                ->where('comparisonScore', 40)
                ->where('questionItems.0.is_correct', true)
            );
    }

    public function test_filter_dan_pencarian_evaluasi(): void
    {
        EvaluationResponse::create([
            'user_id' => $this->mahasiswa->id,
            'type' => 'post_test',
            'module_number' => null,
            'raw_answers' => ['1' => 'A'],
            'scores_per_module' => null,
            'total_score' => 85,
            'n_gain_score' => 0.75,
            'submitted_at' => now(),
        ]);

        $this->actingAs($this->admin)
            ->get(route('admin.evaluations.index', [
                'type' => 'post_test',
                'module' => 'general',
            ]))
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->component('Admin/Evaluations/Index')
                ->where('typeFilter', 'post_test')
                ->where('moduleFilter', 'general')
                ->where('evaluations.data.0.user.name', 'Aulia Rahmawati')
            );

        $this->actingAs($this->admin)
            ->get(route('admin.evaluations.index', ['q' => 'Aulia']))
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->component('Admin/Evaluations/Index')
                ->where('evaluations.data.0.user.name', 'Aulia Rahmawati')
            );
    }

    public function test_admin_dapat_melihat_jawaban_salah_dan_kunci_jawaban_yang_benar(): void
    {
        $question = TestQuestion::create([
            'question_type' => TestQuestion::TYPE_POSTTEST,
            'module_target' => null,
            'question_text' => 'Berapa kali sebaiknya pembalut diganti dalam sehari saat menstruasi?',
            'options' => [
                ['label' => 'A', 'text' => '1 kali sehari'],
                ['label' => 'B', 'text' => 'Setiap 4 sampai 6 jam sekali'],
                ['label' => 'C', 'text' => 'Hanya jika sudah penuh'],
                ['label' => 'D', 'text' => '2 hari sekali'],
            ],
            'correct_answer' => 'B',
            'explanation' => 'Mengganti pembalut teratur mencegah kelembapan berlebih.',
        ]);

        $eval = EvaluationResponse::create([
            'user_id' => $this->mahasiswa->id,
            'type' => 'post_test',
            'module_number' => null,
            'raw_answers' => [$question->id => 'A'],
            'scores_per_module' => null,
            'total_score' => 0,
            'submitted_at' => now(),
        ]);

        $this->actingAs($this->admin)
            ->get(route('admin.evaluations.show', $eval->id))
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->component('Admin/Evaluations/Show')
                ->where('questionItems.0.is_correct', false)
                ->where('questionItems.0.user_answer', 'A')
                ->where('questionItems.0.correct_answer', 'B')
            );
    }

    public function test_admin_dapat_mengunduh_data_posttest_format_excel(): void
    {
        $question = TestQuestion::create([
            'question_type' => TestQuestion::TYPE_POSTTEST,
            'module_target' => null,
            'question_text' => 'Berapa hari rata-rata siklus menstruasi normal?',
            'options' => [
                ['label' => 'A', 'text' => '14 hari'],
                ['label' => 'B', 'text' => '28 hari (rentang 21 sampai 35 hari)'],
                ['label' => 'C', 'text' => '45 hari'],
                ['label' => 'D', 'text' => '60 hari'],
            ],
            'correct_answer' => 'B',
            'explanation' => 'Siklus normal berkisar antara 21 sampai 35 hari.',
        ]);

        EvaluationResponse::create([
            'user_id' => $this->mahasiswa->id,
            'type' => 'post_test',
            'module_number' => null,
            'raw_answers' => [$question->id => 'B'],
            'scores_per_module' => null,
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
        $this->actingAs($this->mahasiswa)
            ->get(route('admin.evaluations.export'))
            ->assertStatus(403);

        auth()->logout();
        $this->get(route('admin.evaluations.export'))
            ->assertRedirect(route('login'));
    }
}
