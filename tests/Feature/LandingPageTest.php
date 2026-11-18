<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class LandingPageTest extends TestCase
{
    use RefreshDatabase;

    public function test_landing_page_renders_database_curriculum_posters_and_honest_contact_states(): void
    {
        $this->seed();

        $response = $this->get(route('home'));

        $response->assertSee('Anatomi, Fisiologi, & Higienitas Reproduksi');
        $response->assertSee('Pengenalan Anatomi Organ Reproduksi Pria & Wanita');
        $response->assertSee('4 Panduan Utama Higienitas Organ Kewanitaan');
        $response->assertSee('Dilema Relasi Kos-Kosan & Tekanan Tinggal Bersama');
        $response->assertDontSee('*boundaries*');
        $response->assertSee('tel:073621170', false);
        $response->assertSee('tel:073652004', false);
        $response->assertDontSee('tel:081278901234', false);
        $response->assertSee('[KONTAK TELEPON BELUM DIVERIFIKASI]');
    }

    public function test_landing_page_renders_explicit_empty_states_when_records_do_not_exist(): void
    {
        $response = $this->get(route('home'));

        $response->assertSee('Belum ada modul pembelajaran yang dipublikasikan');
        $response->assertSee('Belum ada studi kasus yang dipublikasikan');
        $response->assertSee('Belum ada poster Kaspro yang diunggah');
        $response->assertSee('Daftar kontak darurat belum tercatat di basis data.');
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

        $response = $this->actingAs($user)->get(route('home'));

        $response->assertSee('Buka Ruang Kerja SIKERA');
        $response->assertDontSee('Isi Biodata Responden Baru');
    }
}
