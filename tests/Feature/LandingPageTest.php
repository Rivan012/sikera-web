<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

class LandingPageTest extends TestCase
{
    use RefreshDatabase;

    public function test_landing_page_renders_database_curriculum_posters_and_honest_contact_states(): void
    {
        $this->seed();

        $response = $this->get(route('home'))->assertOk();

        $response->assertInertia(fn (Assert $page) => $page
            ->component('Welcome')
            ->where('modules.0.title', 'Anatomi, Fisiologi, & Higienitas Reproduksi')
            ->where('modules.0.topics.0.title', 'Pengenalan Anatomi Organ Reproduksi Pria & Wanita')
            ->where('posters.0.title', '4 Panduan Utama Higienitas Organ Kewanitaan')
            ->where('cases.0.title', 'Dilema Relasi Kos-Kosan & Tekanan Tinggal Bersama')
            ->where('dataLoadError', false)
        );

        $props = $response->viewData('page')['props'];
        $this->assertStringNotContainsString('*boundaries*', $props['cases'][0]['narrative']);

        $telHrefs = array_filter(array_column($props['hotlines'], 'tel_href'));
        $this->assertContains('tel:073621170', $telHrefs);
        $this->assertContains('tel:073652004', $telHrefs);
        $this->assertNotContains('tel:081278901234', $telHrefs);
    }

    public function test_landing_page_renders_explicit_empty_states_when_records_do_not_exist(): void
    {
        $this->get(route('home'))
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->component('Welcome')
                ->has('modules', 0)
                ->has('posters', 0)
                ->has('cases', 0)
                ->has('hotlines', 0)
                ->where('dataLoadError', false)
            );
    }

    public function test_authenticated_user_sees_workspace_actions_on_landing_page(): void
    {
        $user = User::create([
            'name' => 'Mahasiswa Uji',
            'initials' => 'MU',
            'email' => 'uji.landing@unib.ac.id',
            'role' => 'mahasiswa',
            'usia' => 19,
            'agama' => 'Islam',
            'pendidikan_terakhir' => 'SMA/SMK/MA/Sederajat',
            'fakultas' => 'Fakultas Teknik (FT)',
            'prodi' => 'Informatika',
            'gender' => 'P',
            'is_biodata_filled' => true,
            'pretest_completed' => false,
            'posttest_completed' => false,
            'password' => Hash::make('password'),
        ]);

        $this->actingAs($user)
            ->get(route('home'))
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->component('Welcome')
                ->where('auth.user.id', $user->id)
                ->where('auth.user.name', 'Mahasiswa Uji')
            );
    }
}
