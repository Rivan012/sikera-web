<?php

namespace Tests\Feature;

use App\Models\EducationalModule;
use App\Models\EvaluationResponse;
use App\Models\ModuleProgress;
use App\Models\ModuleTopic;
use App\Models\TestQuestion;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

class EvaluationDelayedPosttestAndAdminQuestionsTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;

    protected User $mahasiswa;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();

        $this->admin = User::where('role', 'admin')->firstOrFail();
        $this->mahasiswa = User::create([
            'name' => 'Rani Evaluasi',
            'initials' => 'RE',
            'email' => 'rani.evaluasi@unib.ac.id',
            'nim' => 'G1A026111',
            'role' => 'mahasiswa',
            'gender' => 'P',
            'usia' => 19,
            'agama' => 'Islam',
            'prodi' => 'Informatika',
            'fakultas' => 'Fakultas Teknik (FT)',
            'pendidikan_terakhir' => 'SMA/SMK/MA/Sederajat',
            'is_biodata_filled' => true,
            'pretest_completed' => false,
            'posttest_completed' => false,
            'password' => Hash::make('password'),
        ]);
    }

    public function test_admin_dapat_mengelola_soal_pretest_berdasarkan_modul_secara_terpisah(): void
    {
        $this->actingAs($this->admin)
            ->get(route('admin.pretest-questions.index', ['module' => 2]))
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->component('Admin/PretestQuestions/Index')
                ->where('selectedModule', 2)
            );

        $this->actingAs($this->admin)
            ->post(route('admin.pretest-questions.store'), [
                'question_text' => 'Soal tanpa nomor modul',
                'option_a' => 'A',
                'option_b' => 'B',
                'option_c' => 'C',
                'option_d' => 'D',
                'correct_answer' => 'A',
            ])
            ->assertSessionHasErrors('module_target');

        $this->actingAs($this->admin)
            ->post(route('admin.pretest-questions.store'), [
                'module_target' => 2,
                'question_text' => 'Berapa rentang siklus haid normal menurut FIGO?',
                'option_a' => '10 sampai 15 hari',
                'option_b' => '21 sampai 35 hari',
                'option_c' => '40 sampai 55 hari',
                'option_d' => 'Lebih dari 60 hari',
                'correct_answer' => 'B',
                'explanation' => 'Rentang fisiologis siklus haid adalah 21 sampai 35 hari.',
            ])
            ->assertRedirect(route('admin.pretest-questions.index', ['module' => 2]));

        $created = TestQuestion::preTest()
            ->where('module_target', 2)
            ->where('question_text', 'Berapa rentang siklus haid normal menurut FIGO?')
            ->first();
        $this->assertNotNull($created);
        $this->assertSame('pre_test', $created->question_type);

        $this->actingAs($this->admin)
            ->put(route('admin.pretest-questions.update', $created->id), [
                'module_target' => 2,
                'question_text' => 'Berapa rentang siklus menstruasi fisiologis menurut FIGO?',
                'option_a' => '10 sampai 15 hari',
                'option_b' => '21 sampai 35 hari',
                'option_c' => '40 sampai 55 hari',
                'option_d' => 'Lebih dari 60 hari',
                'correct_answer' => 'C',
                'explanation' => 'Penjelasan diperbarui.',
            ])
            ->assertRedirect(route('admin.pretest-questions.index', ['module' => 2]));

        $created->refresh();
        $this->assertSame('C', $created->correct_answer);

        $this->actingAs($this->admin)
            ->delete(route('admin.pretest-questions.destroy', $created->id))
            ->assertRedirect(route('admin.pretest-questions.index', ['module' => 2]));

        $this->assertModelMissing($created);
    }

    public function test_admin_dapat_mengelola_soal_posttest_umum_secara_terpisah_dan_mahasiswa_ditolak(): void
    {
        $this->actingAs($this->mahasiswa)
            ->get(route('admin.pretest-questions.index'))
            ->assertForbidden();

        $this->actingAs($this->mahasiswa)
            ->get(route('admin.posttest-questions.index'))
            ->assertForbidden();

        $this->actingAs($this->admin)
            ->get(route('admin.posttest-questions.index'))
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->component('Admin/PosttestQuestions/Index')
                ->has('questions')
            );

        $this->actingAs($this->admin)
            ->post(route('admin.posttest-questions.store'), [
                'question_text' => 'Skor PBAC berapa yang mengindikasikan menoragia / HMB?',
                'option_a' => 'Lebih dari atau sama dengan 100 poin',
                'option_b' => 'Kurang dari 20 poin',
                'option_c' => 'Antara 10 sampai 30 poin',
                'option_d' => 'Tepat 50 poin',
                'correct_answer' => 'A',
                'explanation' => 'Skor PBAC >= 100 berkorelasi dengan kehilangan darah >= 80 ml.',
            ])
            ->assertRedirect(route('admin.posttest-questions.index'));

        $postQuestion = TestQuestion::postTest()
            ->where('question_text', 'Skor PBAC berapa yang mengindikasikan menoragia / HMB?')
            ->first();
        $this->assertNotNull($postQuestion);
        $this->assertNull($postQuestion->module_target);

        // Menghapus modul hanya menghapus pre_test modul tersebut dan menjaga soal post_test tetap utuh.
        $mod4 = EducationalModule::where('module_number', 4)->firstOrFail();
        $this->actingAs($this->admin)
            ->delete(route('admin.modules.destroy', $mod4->id))
            ->assertRedirect(route('admin.modules.index'));

        $this->assertDatabaseHas('test_questions', [
            'id' => $postQuestion->id,
            'question_type' => TestQuestion::TYPE_POSTTEST,
        ]);
    }

    public function test_posttest_terkunci_sebelum_semua_modul_selesai_dan_menunggu_3_hari_penuh(): void
    {
        $this->actingAs($this->mahasiswa);

        // 1. Belum menyelesaikan semua modul -> terkunci & POST ditolak 403
        $this->get('/posttest')
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->component('Evaluation/Posttest')
                ->where('gate.state', 'locked_incomplete')
                ->where('gate.is_posttest_unlocked', false)
            );

        $this->post('/posttest', ['answers' => ['1' => 'A']])->assertForbidden();

        // 2. Kerjakan Pre-Test pada 2 modul (skor 50 dan 50 -> rata-rata 50)
        EvaluationResponse::create([
            'user_id' => $this->mahasiswa->id,
            'type' => 'pre_test',
            'module_number' => 1,
            'raw_answers' => ['1' => 'A'],
            'total_score' => 50,
            'submitted_at' => now(),
        ]);
        EvaluationResponse::create([
            'user_id' => $this->mahasiswa->id,
            'type' => 'pre_test',
            'module_number' => 2,
            'raw_answers' => ['3' => 'B'],
            'total_score' => 50,
            'submitted_at' => now(),
        ]);

        // 3. Tandai semua subbab kecuali yang terakhir selesai, lalu buka subbab terakhir via HTTP
        $allTopics = ModuleTopic::orderBy('id')->get();
        $lastTopic = $allTopics->last();

        EvaluationResponse::create([
            'user_id' => $this->mahasiswa->id,
            'type' => 'pre_test',
            'module_number' => EducationalModule::findOrFail($lastTopic->educational_module_id)->module_number,
            'raw_answers' => ['7' => 'A'],
            'total_score' => 50,
            'submitted_at' => now(),
        ]);

        foreach ($allTopics->slice(0, -1) as $tp) {
            ModuleProgress::create([
                'user_id' => $this->mahasiswa->id,
                'module_topic_id' => $tp->id,
                'is_completed' => true,
                'completed_at' => now(),
            ]);
        }

        $this->get(route('modules.topic', [$lastTopic->educational_module_id, $lastTopic->id]))
            ->assertOk();

        $this->mahasiswa->refresh();
        $this->assertNotNull($this->mahasiswa->all_modules_completed_at);

        // 4. Pada hari ke-2 (belum 72 jam): masih waiting_delay dan POST ditolak 403
        $this->travel(2)->days();

        $this->get('/posttest')
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->component('Evaluation/Posttest')
                ->where('gate.state', 'waiting_delay')
                ->where('gate.is_posttest_unlocked', false)
            );

        $this->post('/posttest', ['answers' => ['1' => 'A']])->assertForbidden();

        // 5. Setelah lewat 3 hari penuh (+1 hari + 5 menit dari posisi +2 hari): terbuka & hitung N-Gain total
        $this->travel(1)->days();
        $this->travel(5)->minutes();

        $postQuestions = TestQuestion::postTest()->orderBy('id')->get();
        $answers = [];
        foreach ($postQuestions as $q) {
            $answers[$q->id] = $q->correct_answer;
        }

        $this->post('/posttest', ['answers' => $answers])
            ->assertRedirect(route('posttest.show'));

        // Mahasiswa tetap login (tidak di-logout paksa)
        $this->assertAuthenticatedAs($this->mahasiswa);

        // Pre-Test avg = 50, Post-Test = 100 -> N-Gain = (100 - 50) / (100 - 50) = 1.0
        $this->assertDatabaseHas('evaluation_responses', [
            'user_id' => $this->mahasiswa->id,
            'type' => 'post_test',
            'module_number' => null,
            'total_score' => 100,
            'n_gain_score' => 1.0,
        ]);
    }

    public function test_api_v1_posttest_3_hari_dan_admin_questions_crud(): void
    {
        $mhsToken = $this->mahasiswa->createToken('mhs')->plainTextToken;
        $adminToken = $this->admin->createToken('admin')->plainTextToken;

        // 1. Status awal: modules_incomplete (403 pada GET /api/v1/posttest)
        $this->withToken($mhsToken)
            ->getJson('/api/v1/posttest')
            ->assertForbidden()
            ->assertJsonPath('reason', 'modules_incomplete');

        // 2. Selesaikan seluruh subbab -> masuk waiting_period (403 sebelum 3 hari)
        foreach (ModuleTopic::all() as $tp) {
            ModuleProgress::create([
                'user_id' => $this->mahasiswa->id,
                'module_topic_id' => $tp->id,
                'is_completed' => true,
                'completed_at' => now(),
            ]);
        }

        $this->withToken($mhsToken)
            ->getJson('/api/v1/posttest/status')
            ->assertOk()
            ->assertJsonPath('data.posttest_gate.state', 'waiting_delay');

        $this->withToken($mhsToken)
            ->getJson('/api/v1/posttest')
            ->assertForbidden()
            ->assertJsonPath('reason', 'waiting_period');

        // 3. Admin CRUD soal via API (mahasiswa ditolak 403)
        $this->withToken($mhsToken)
            ->getJson('/api/v1/admin/pretest-questions')
            ->assertForbidden();

        $this->app['auth']->forgetGuards();

        $preStore = $this->withToken($adminToken)
            ->postJson('/api/v1/admin/pretest-questions', [
                'module_target' => 1,
                'question_text' => 'Soal API Pre-Test Modul 1?',
                'option_a' => 'Opsi A',
                'option_b' => 'Opsi B',
                'option_c' => 'Opsi C',
                'option_d' => 'Opsi D',
                'correct_answer' => 'A',
            ])
            ->assertCreated();
        $preId = $preStore->json('data.question.id');

        $this->withToken($adminToken)
            ->deleteJson("/api/v1/admin/pretest-questions/{$preId}")
            ->assertOk();

        $postStore = $this->withToken($adminToken)
            ->postJson('/api/v1/admin/posttest-questions', [
                'question_text' => 'Soal API Post-Test Umum?',
                'option_a' => 'Benar A',
                'option_b' => 'Salah B',
                'option_c' => 'Salah C',
                'option_d' => 'Salah D',
                'correct_answer' => 'A',
            ])
            ->assertCreated();
        $postId = $postStore->json('data.question.id');

        // 4. Lewati jeda 3 hari -> mahasiswa ambil & kirim Post-Test API tanpa pencabutan token
        $this->app['auth']->forgetGuards();
        $this->travel(3)->days();
        $this->travel(2)->minutes();

        $getPost = $this->withToken($mhsToken)
            ->getJson('/api/v1/posttest')
            ->assertOk();

        $answers = [];
        foreach ($getPost->json('data.questions') as $q) {
            $answers[$q['id']] = TestQuestion::findOrFail($q['id'])->correct_answer;
        }

        $this->withToken($mhsToken)
            ->postJson('/api/v1/posttest', ['answers' => $answers])
            ->assertOk()
            ->assertJsonPath('data.posttest_score', 100);

        // Token masih aktif setelah submit Post-Test
        $this->withToken($mhsToken)
            ->getJson('/api/v1/auth/me')
            ->assertOk();

        $this->app['auth']->forgetGuards();
        $this->withToken($adminToken)
            ->deleteJson("/api/v1/admin/posttest-questions/{$postId}")
            ->assertOk();
    }
}
