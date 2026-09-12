<?php

namespace Tests\Feature;

use App\Models\PeriodLog;
use App\Models\User;
use App\Services\MenstrualCycleCalculatorService;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class PeriodPredictionScientificTest extends TestCase
{
    use DatabaseTransactions;

    protected User $femaleUser;

    protected User $maleUser;

    protected MenstrualCycleCalculatorService $service;

    protected function setUp(): void
    {
        parent::setUp();

        $this->service = new MenstrualCycleCalculatorService;

        $this->femaleUser = User::where('email', 'female_test@sikera.id')->first() ?? User::create([
            'name' => 'Siti Khadijah',
            'email' => 'female_test@sikera.id',
            'role' => 'mahasiswa',
            'gender' => 'P',
            'usia' => 19,
            'password' => Hash::make('password'),
            'is_biodata_filled' => true,
        ]);

        $this->maleUser = User::where('email', 'male_test@sikera.id')->first() ?? User::create([
            'name' => 'Ahmad Fauzi',
            'email' => 'male_test@sikera.id',
            'role' => 'mahasiswa',
            'gender' => 'L',
            'usia' => 20,
            'password' => Hash::make('password'),
            'is_biodata_filled' => true,
        ]);
    }

    public function test_kalkulasi_ilmiah_siklus_standar_28_hari(): void
    {
        // Kasus: LMP 01 September 2026, siklus 28 hari, durasi 5 hari
        $lmp = '2026-09-01';
        $result = $this->service->calculate(
            lastPeriodDate: $lmp,
            cycleLength: 28,
            periodDuration: 5,
            targetDate: Carbon::parse('2026-09-12')
        );

        $this->assertTrue($result['success']);

        // 1. Prediksi Haid Berikutnya (LMP + 28 = 29 September 2026)
        $this->assertEquals('2026-09-29', $result['predictions']['next_period']['start_date']);
        $this->assertEquals('2026-10-03', $result['predictions']['next_period']['end_date']);

        // 2. Estimasi Hari Ovulasi (Haid Berikutnya - 14 hari = 15 September 2026)
        $this->assertEquals('2026-09-15', $result['predictions']['ovulation']['date']);

        // 3. Jendela Masa Subur Wilcox (Ovulasi - 5 hari s.d. Ovulasi + 1 hari = 10 s.d 16 September 2026)
        $this->assertEquals('2026-09-10', $result['predictions']['fertile_window']['start_date']);
        $this->assertEquals('2026-09-16', $result['predictions']['fertile_window']['end_date']);
        $this->assertEquals(6, $result['predictions']['fertile_window']['total_days']);

        // 4. Evaluasi Hari Ini (12 September = Hari ke-12 siklus, berada di Masa Subur)
        $this->assertEquals(12, $result['current_status']['cycle_day']);
        $this->assertEquals('fertile', $result['current_status']['phase_key']);
        $this->assertEquals('Tinggi', $result['current_status']['fertility_status']);
        $this->assertEquals('17 hari lagi', $result['current_status']['countdown_text']);
        $this->assertStringContainsString('Haid berikutnya diperkirakan tiba pada tanggal 29 September 2026 (sekitar 17 hari lagi)', $result['current_status']['summary_narrative']);
    }

    public function test_kalkulasi_ilmiah_moving_average_dan_ogino_knaus(): void
    {
        // Kasus dari dokumen: Siklus terpendek 26 hari, terpanjang 31 hari
        $history = [26, 31, 28, 29];
        $lmp = '2026-09-01';

        $result = $this->service->calculate(
            lastPeriodDate: $lmp,
            cycleHistory: $history,
            targetDate: Carbon::parse('2026-09-01')
        );

        // Moving Average: (26 + 31 + 28 + 29) / 4 = 28.5 => round 29
        $this->assertEquals(29, $result['cycle_metrics']['average_cycle_length']);
        $this->assertEquals(26, $result['cycle_metrics']['shortest_cycle']);
        $this->assertEquals(31, $result['cycle_metrics']['longest_cycle']);

        // Ogino-Knaus:
        // Awal = Terpendek (26) - 18 = Hari ke-8 siklus (09 September 2026)
        // Akhir = Terpanjang (31) - 11 = Hari ke-20 siklus (21 September 2026)
        $this->assertEquals(8, $result['predictions']['ogino_knaus']['cycle_day_start']);
        $this->assertEquals(20, $result['predictions']['ogino_knaus']['cycle_day_end']);
        $this->assertEquals('2026-09-09', $result['predictions']['ogino_knaus']['start_date']);
        $this->assertEquals('2026-09-21', $result['predictions']['ogino_knaus']['end_date']);
    }

    public function test_api_calculate_period_endpoint(): void
    {
        $payload = [
            'last_period_date' => '2026-09-01',
            'cycle_length' => 30,
            'period_duration' => 6,
            'target_date' => '2026-09-16',
        ];

        $response = $this->postJson('/api/v1/selfcare/period/calculate', $payload);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
            ])
            ->assertJsonStructure([
                'success',
                'message',
                'data' => [
                    'cycle_metrics',
                    'current_status',
                    'predictions' => [
                        'next_period',
                        'ovulation',
                        'fertile_window',
                        'ogino_knaus',
                    ],
                    'scientific_references',
                    'clinical_disclaimer',
                ],
            ]);

        // Pada siklus 30 hari:
        // Next period: 01/09 + 30 = 01/10/2026
        // Ovulasi: 01/10 - 14 = 17/09/2026
        // Fertile window: 17/09 - 5 = 12/09 s.d. 18/09
        $data = $response->json('data');
        $this->assertEquals('2026-10-01', $data['predictions']['next_period']['start_date']);
        $this->assertEquals('2026-09-17', $data['predictions']['ovulation']['date']);
        $this->assertEquals('2026-09-12', $data['predictions']['fertile_window']['start_date']);
        $this->assertEquals('2026-09-18', $data['predictions']['fertile_window']['end_date']);
        $this->assertEquals('15 hari lagi', $data['current_status']['countdown_text']);
    }

    public function test_api_period_prediction_pengguna_perempuan(): void
    {
        // 1. Buat log haid pengguna perempuan
        PeriodLog::create([
            'user_id' => $this->femaleUser->id,
            'start_date' => '2026-09-01',
            'end_date' => '2026-09-06',
            'cycle_length' => 28,
            'period_duration' => 5,
            'flow_level' => 'sedang',
            'flow_color' => 'Merah Terang',
            'nrs_pain_score' => 2,
        ]);

        $response = $this->actingAs($this->femaleUser)->getJson('/api/v1/selfcare/period/prediction');

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
            ]);

        $this->assertEquals('2026-09-29', $response->json('data.predictions.next_period.start_date'));
        $this->assertEquals('2026-09-15', $response->json('data.predictions.ovulation.date'));
        $this->assertNotNull($response->json('data.summary_narrative'));
        $this->assertNotNull($response->json('data.countdown_text'));
    }

    public function test_api_period_prediction_ditolak_untuk_laki_laki(): void
    {
        $response = $this->actingAs($this->maleUser)->getJson('/api/v1/selfcare/period/prediction');

        $response->assertStatus(403);
    }

    public function test_api_summary_menyertakan_prediksi_ilmiah(): void
    {
        PeriodLog::create([
            'user_id' => $this->femaleUser->id,
            'start_date' => '2026-09-01',
            'end_date' => '2026-09-05',
            'cycle_length' => 28,
            'period_duration' => 5,
            'flow_level' => 'sedang',
            'flow_color' => 'Merah Terang',
            'nrs_pain_score' => 1,
        ]);

        $response = $this->actingAs($this->femaleUser)->getJson('/api/v1/selfcare/summary');

        $response->assertStatus(200);
        $this->assertNotNull($response->json('data.period_tracker.predictions'));
        $this->assertEquals('2026-09-29', $response->json('data.period_tracker.predictions.next_period_date'));
        $this->assertEquals('2026-09-15', $response->json('data.period_tracker.predictions.ovulation_date'));
        $this->assertEquals('2026-09-10', $response->json('data.period_tracker.predictions.fertile_window_start'));
        $this->assertEquals('2026-09-16', $response->json('data.period_tracker.predictions.fertile_window_end'));
        $this->assertNotNull($response->json('data.period_tracker.predictions.summary_narrative'));
        $this->assertNotNull($response->json('data.period_tracker.predictions.countdown_text'));
    }

    public function test_web_selfcare_dapat_diakses_dengan_prediksi_ilmiah(): void
    {
        PeriodLog::create([
            'user_id' => $this->femaleUser->id,
            'start_date' => '2026-09-01',
            'cycle_length' => 28,
            'period_duration' => 5,
            'flow_level' => 'sedang',
            'flow_color' => 'Merah Terang',
            'nrs_pain_score' => 2,
        ]);

        $response = $this->actingAs($this->femaleUser)->get(route('selfcare.index'));

        $response->assertStatus(200);
        $response->assertSee('Perkiraan Siklus Haid');
        $response->assertSee('Jendela Masa Subur (6 Hari)');
        $response->assertSee('Simulasi &amp; Kalkulator Siklus Mandiri', false);
        $response->assertDontSee('Wilcox et al. (NEJM 1995)');
        $response->assertDontSee('ACOG &amp; WHO', false);
    }
}
