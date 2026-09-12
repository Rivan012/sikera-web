<?php

namespace Tests\Feature;

use App\Models\User;
use App\Services\MenstrualCycleCalculatorService;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class PeriodLogWithWaLIDDTest extends TestCase
{
    use DatabaseTransactions;

    protected User $femaleUser;

    protected User $maleUser;

    protected MenstrualCycleCalculatorService $service;

    protected function setUp(): void
    {
        parent::setUp();

        $this->service = new MenstrualCycleCalculatorService;

        $this->femaleUser = User::where('email', 'female_walidd_test@sikera.id')->first() ?? User::create([
            'name' => 'Fatimah Az-Zahra',
            'email' => 'female_walidd_test@sikera.id',
            'role' => 'mahasiswa',
            'gender' => 'P',
            'usia' => 20,
            'password' => Hash::make('password'),
            'is_biodata_filled' => true,
        ]);

        $this->maleUser = User::where('email', 'male_walidd_test@sikera.id')->first() ?? User::create([
            'name' => 'Budi Santoso',
            'email' => 'male_walidd_test@sikera.id',
            'role' => 'mahasiswa',
            'gender' => 'L',
            'usia' => 21,
            'password' => Hash::make('password'),
            'is_biodata_filled' => true,
        ]);
    }

    public function test_walidd_score_calculation_across_categories(): void
    {
        // 1. Skor 0: Tidak Dismenore
        $res0 = $this->service->calculateWaLIDD(
            workingAbility: 0,
            locations: [],
            nrsScore: 0,
            painDays: 0
        );
        $this->assertEquals(0, $res0['total_score']);
        $this->assertEquals('Tidak Dismenore', $res0['category']);
        $this->assertEquals(0, $res0['working_ability_score']);
        $this->assertEquals(0, $res0['location_score']);
        $this->assertEquals(0, $res0['intensity_score']);
        $this->assertEquals(0, $res0['pain_days_score']);

        // 2. Skor 1-4: Dismenore Ringan (W=1, L=1, I=1 (NRS 3), D=1 (1 hari)) => Total = 4
        $resRingan = $this->service->calculateWaLIDD(
            workingAbility: 1,
            locations: ['perut_bawah'],
            nrsScore: 3,
            painDays: 1
        );
        $this->assertEquals(4, $resRingan['total_score']);
        $this->assertEquals('Dismenore Ringan', $resRingan['category']);

        // 3. Skor 5-7: Dismenore Sedang (W=2, L=2, I=2 (NRS 6), D=1 (2 hari)) => Total = 7
        $resSedang = $this->service->calculateWaLIDD(
            workingAbility: 2,
            locations: ['perut_bawah', 'pinggang'],
            nrsScore: 6,
            painDays: 2
        );
        $this->assertEquals(7, $resSedang['total_score']);
        $this->assertEquals('Dismenore Sedang', $resSedang['category']);

        // 4. Skor 8-12: Dismenore Berat (W=3, L=3, I=3 (NRS 9), D=3 (5 hari)) => Total = 12
        $resBerat = $this->service->calculateWaLIDD(
            workingAbility: 3,
            locations: ['perut_bawah', 'pinggang', 'paha_dalam'],
            nrsScore: 9,
            painDays: 5
        );
        $this->assertEquals(12, $resBerat['total_score']);
        $this->assertEquals('Dismenore Berat', $resBerat['category']);
    }

    public function test_web_store_period_with_5_components(): void
    {
        $payload = [
            'menarche_age' => 12,
            'start_date' => '2026-09-01',
            'end_date' => '2026-09-06',
            'cycle_length' => 28,
            'is_regular' => 1,
            'flow_level' => 'sedang',
            'flow_color' => 'Merah Terang',
            'blood_consistency' => 'Sedang / Normal',
            'nrs_pain_score' => 4,
            'has_dysmenorrhea' => 1,
            'walidd_working_ability' => 1,
            'walidd_locations' => ['perut_bawah', 'pinggang'],
            'walidd_pain_days' => 2,
            'symptoms' => ['Kram Perut'],
            'notes' => 'Catatan tes haid 5 komponen.',
        ];

        $response = $this->actingAs($this->femaleUser)
            ->post(route('selfcare.period.store'), $payload);

        $response->assertRedirect(route('selfcare.index'));
        $response->assertSessionHas('success');

        // Pastikan record tersimpan di database
        $this->assertDatabaseHas('period_logs', [
            'user_id' => $this->femaleUser->id,
            'menarche_age' => 12,
            'cycle_length' => 28,
            'period_duration' => 6, // 1 to 6 Sept = 6 hari
            'is_regular' => 1,
            'flow_level' => 'sedang',
            'flow_color' => 'Merah Terang',
            'blood_consistency' => 'Sedang / Normal',
            'nrs_pain_score' => 4,
            'has_dysmenorrhea' => 1,
            'walidd_working_ability' => 1,
            'walidd_location_score' => 2,
            'walidd_intensity_score' => 2,
            'walidd_pain_days_score' => 1,
            'walidd_total_score' => 6, // W(1) + L(2) + I(2) + D(1) = 6
            'walidd_category' => 'Dismenore Sedang',
        ]);

        // Cek update profil menarche_age user
        $this->femaleUser->refresh();
        $this->assertEquals(12, $this->femaleUser->menarche_age);
    }

    public function test_api_store_period_with_5_components(): void
    {
        $payload = [
            'menarche_age' => 13,
            'start_date' => '2026-09-02',
            'end_date' => '2026-09-07',
            'cycle_length' => 30,
            'is_regular' => true,
            'flow_level' => 'deras',
            'flow_color' => 'Merah Terang',
            'blood_consistency' => 'Cair / Encer',
            'nrs_pain_score' => 2,
            'has_dysmenorrhea' => true,
            'walidd_working_ability' => 1,
            'walidd_locations' => ['perut_bawah'],
            'walidd_pain_days' => 1,
        ];

        $response = $this->actingAs($this->femaleUser, 'sanctum')
            ->postJson('/api/v1/selfcare/period', $payload);

        $response->assertStatus(201)
            ->assertJson([
                'success' => true,
                'data' => [
                    'period_log' => [
                        'menarche_age' => 13,
                        'cycle_length' => 30,
                        'is_regular' => true,
                        'flow_level' => 'deras',
                        'flow_color' => 'Merah Terang',
                        'blood_consistency' => 'Cair / Encer',
                        'walidd_total_score' => 4, // W(1) + L(1) + I(1) + D(1) = 4
                        'walidd_category' => 'Dismenore Ringan',
                    ],
                ],
            ]);
    }

    public function test_api_calculate_walidd_endpoint(): void
    {
        $payload = [
            'working_ability' => 2,
            'locations' => ['perut_bawah', 'pinggang'],
            'nrs_score' => 5,
            'pain_days' => 3,
        ];

        $response = $this->postJson('/api/v1/selfcare/period/walidd/calculate', $payload);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'data' => [
                    'working_ability_score' => 2,
                    'location_score' => 2,
                    'intensity_score' => 2, // NRS 5 => 2
                    'pain_days_score' => 2, // 3 hari => 2
                    'total_score' => 8,     // 2 + 2 + 2 + 2 = 8
                    'category' => 'Dismenore Berat',
                ],
            ]);
    }

    public function test_pbac_score_calculation_normal_and_menorrhagia(): void
    {
        // 1. Kasus Normal: 5 pembalut sedikit (5x1=5), 4 sedang (4x5=20), 1 basah penuh (1x20=20) = 45 poin (< 100)
        $resNormal = $this->service->calculatePBAC(
            padsLight: 5,
            padsMedium: 4,
            padsHeavy: 1,
            clotsSmall: 2,
            clotsLarge: 0
        );
        $this->assertEquals(47, $resNormal['total_score']);
        $this->assertEquals('normal', $resNormal['code']);
        $this->assertFalse($resNormal['is_hmb']);

        // 2. Kasus Menoragia / HMB: Skor >= 100 poin (5 basah penuh x 20 = 100, 2 gumpalan besar x 5 = 10 -> 110)
        $resHmb = $this->service->calculatePBAC(
            padsLight: 0,
            padsMedium: 0,
            padsHeavy: 5,
            clotsSmall: 0,
            clotsLarge: 2
        );
        $this->assertEquals(110, $resHmb['total_score']);
        $this->assertEquals('menoragia', $resHmb['code']);
        $this->assertTrue($resHmb['is_hmb']);
        $this->assertStringContainsString('Menoragia / HMB', $resHmb['category']);

        // 3. Kasus Hipomenore: Hanya 3 pembalut sedikit (3x1=3 pt)
        $resHipo = $this->service->calculatePBAC(
            padsLight: 3,
            padsMedium: 0,
            padsHeavy: 0,
            clotsSmall: 0,
            clotsLarge: 0
        );
        $this->assertEquals(3, $resHipo['total_score']);
        $this->assertEquals('hipomenore', $resHipo['code']);
    }

    public function test_web_store_period_with_pbac_data(): void
    {
        $payload = [
            'menarche_age' => 12,
            'start_date' => '2026-09-01',
            'end_date' => '2026-09-06',
            'cycle_length' => 28,
            'flow_level' => 'sangat_deras',
            'flow_color' => 'Merah Terang',
            'nrs_pain_score' => 2,
            'pbac_pads_light' => 2,
            'pbac_pads_medium' => 3,
            'pbac_pads_heavy' => 5, // 5 x 20 = 100
            'pbac_clots_small' => 1,
            'pbac_clots_large' => 1, // 1 x 5 = 5
        ];

        $response = $this->actingAs($this->femaleUser)
            ->post(route('selfcare.period.store'), $payload);

        $response->assertRedirect(route('selfcare.index'));

        $this->assertDatabaseHas('period_logs', [
            'user_id' => $this->femaleUser->id,
            'pbac_score' => 123, // 2 + 15 + 100 + 1 + 5 = 123
            'volume_category' => 'menoragia',
        ]);
    }

    public function test_api_calculate_pbac_endpoint(): void
    {
        $payload = [
            'pads_light' => 4,
            'pads_medium' => 2,
            'pads_heavy' => 5,
            'clots_small' => 1,
            'clots_large' => 2,
        ];

        $response = $this->postJson('/api/v1/selfcare/period/pbac/calculate', $payload);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'data' => [
                    'total_score' => 125, // 4 + 10 + 100 + 1 + 10 = 125
                    'is_hmb' => true,
                    'code' => 'menoragia',
                ],
            ]);
    }

    public function test_male_user_cannot_store_period(): void
    {
        $payload = [
            'start_date' => '2026-09-01',
            'cycle_length' => 28,
            'flow_level' => 'sedang',
            'flow_color' => 'Merah Terang',
            'is_regular' => 1,
            'nrs_pain_score' => 0,
        ];

        // Web redirect with warning
        $response = $this->actingAs($this->maleUser)
            ->post(route('selfcare.period.store'), $payload);
        $response->assertRedirect(route('selfcare.index'));
        $response->assertSessionHas('warning');

        // API forbidden 403
        $apiResponse = $this->actingAs($this->maleUser, 'sanctum')
            ->postJson('/api/v1/selfcare/period', $payload);
        $apiResponse->assertStatus(403);
    }
}
