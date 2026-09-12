<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AdminUserCrudTest extends TestCase
{
    use DatabaseTransactions;

    protected User $admin;

    protected User $mahasiswa;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::where('role', 'admin')->first() ?? User::create([
            'name' => 'Admin Tester',
            'email' => 'admin_test@sikera.id',
            'role' => 'admin',
            'password' => Hash::make('password'),
            'is_biodata_filled' => true,
        ]);

        $this->mahasiswa = User::where('role', 'mahasiswa')->first() ?? User::create([
            'name' => 'Mahasiswa Tester',
            'email' => 'mhs_test@sikera.id',
            'role' => 'mahasiswa',
            'password' => Hash::make('password'),
            'is_biodata_filled' => true,
        ]);
    }

    public function test_superadmin_dapat_melihat_halaman_daftar_pengguna_dengan_tombol_aksi(): void
    {
        $response = $this->actingAs($this->admin)->get(route('admin.users.index'));

        $response->assertStatus(200);
        $response->assertSee('Tambah Pengguna Baru');
        $response->assertSee('Aksi');
        $response->assertSee($this->admin->name);
    }

    public function test_superadmin_dapat_menambahkan_pengguna_baru(): void
    {
        $userData = [
            'name' => 'Budi Santoso',
            'initials' => 'BS',
            'email' => 'budi.santoso@unib.ac.id',
            'nim' => 'G1A026099',
            'role' => 'mahasiswa',
            'usia' => 20,
            'gender' => 'L',
            'agama' => 'Islam',
            'prodi' => 'Informatika',
            'fakultas' => 'Fakultas Teknik',
            'pendidikan_terakhir' => 'SMA/SMK Sederajat',
            'points' => 100,
            'is_biodata_filled' => '1',
            'pretest_completed' => '1',
            'posttest_completed' => '0',
            'password' => 'secret123',
            'password_confirmation' => 'secret123',
        ];

        $response = $this->actingAs($this->admin)->post(route('admin.users.store'), $userData);

        $response->assertRedirect(route('admin.users.index'));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('users', [
            'name' => 'Budi Santoso',
            'email' => 'budi.santoso@unib.ac.id',
            'nim' => 'G1A026099',
            'role' => 'mahasiswa',
            'prodi' => 'Informatika',
            'points' => 100,
            'is_biodata_filled' => true,
            'pretest_completed' => true,
            'posttest_completed' => false,
        ]);

        $newUser = User::where('email', 'budi.santoso@unib.ac.id')->first();
        $this->assertNotNull($newUser);
        $this->assertTrue(Hash::check('secret123', $newUser->password));
    }

    public function test_validasi_gagal_jika_email_sudah_digunakan(): void
    {
        $response = $this->actingAs($this->admin)->post(route('admin.users.store'), [
            'name' => 'Duplikat Email',
            'email' => $this->mahasiswa->email,
            'role' => 'mahasiswa',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $response->assertSessionHasErrors('email');
    }

    public function test_superadmin_dapat_memperbarui_data_pengguna(): void
    {
        $targetUser = User::create([
            'name' => 'Nama Sebelum Edit',
            'email' => 'editme@unib.ac.id',
            'role' => 'mahasiswa',
            'password' => Hash::make('oldpassword'),
            'prodi' => 'Kedokteran',
            'is_biodata_filled' => false,
        ]);

        $updateData = [
            'name' => 'Nama Setelah Edit',
            'initials' => 'NSE',
            'email' => 'editme_updated@unib.ac.id',
            'nim' => 'G1A999999',
            'role' => 'dosen_pa',
            'usia' => 35,
            'gender' => 'P',
            'agama' => 'Islam',
            'prodi' => 'Ilmu Kesehatan',
            'fakultas' => 'Fakultas Kedokteran',
            'pendidikan_terakhir' => 'Magister (S2)',
            'points' => 250,
            'is_biodata_filled' => '1',
            'pretest_completed' => '1',
            'posttest_completed' => '1',
        ];

        $response = $this->actingAs($this->admin)->put(route('admin.users.update', $targetUser->id), $updateData);

        $response->assertRedirect(route('admin.users.index'));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('users', [
            'id' => $targetUser->id,
            'name' => 'Nama Setelah Edit',
            'email' => 'editme_updated@unib.ac.id',
            'nim' => 'G1A999999',
            'role' => 'dosen_pa',
            'prodi' => 'Ilmu Kesehatan',
            'points' => 250,
            'is_biodata_filled' => true,
        ]);

        // Pastikan password lama tidak berubah jika dikosongkan
        $targetUser->refresh();
        $this->assertTrue(Hash::check('oldpassword', $targetUser->password));
    }

    public function test_superadmin_dapat_mengubah_kata_sandi_pengguna(): void
    {
        $targetUser = User::create([
            'name' => 'Ganti Sandi User',
            'email' => 'gantisandi@unib.ac.id',
            'role' => 'mahasiswa',
            'password' => Hash::make('oldpassword123'),
        ]);

        $response = $this->actingAs($this->admin)->put(route('admin.users.update', $targetUser->id), [
            'name' => 'Ganti Sandi User',
            'email' => 'gantisandi@unib.ac.id',
            'role' => 'mahasiswa',
            'password' => 'newpassword456',
            'password_confirmation' => 'newpassword456',
        ]);

        $response->assertRedirect(route('admin.users.index'));
        $targetUser->refresh();

        $this->assertTrue(Hash::check('newpassword456', $targetUser->password));
        $this->assertFalse(Hash::check('oldpassword123', $targetUser->password));
    }

    public function test_superadmin_dapat_menghapus_pengguna_lain(): void
    {
        $userToDelete = User::create([
            'name' => 'User Mau Dihapus',
            'email' => 'hapus@unib.ac.id',
            'role' => 'mahasiswa',
            'password' => Hash::make('password'),
        ]);

        $response = $this->actingAs($this->admin)->delete(route('admin.users.destroy', $userToDelete->id));

        $response->assertRedirect(route('admin.users.index'));
        $response->assertSessionHas('success');

        $this->assertDatabaseMissing('users', [
            'id' => $userToDelete->id,
        ]);
    }

    public function test_superadmin_tidak_dapat_menghapus_akun_sendiri(): void
    {
        $response = $this->actingAs($this->admin)->delete(route('admin.users.destroy', $this->admin->id));

        $response->assertRedirect(route('admin.users.index'));
        $response->assertSessionHasErrors('error');

        $this->assertDatabaseHas('users', [
            'id' => $this->admin->id,
        ]);
    }

    public function test_mahasiswa_tidak_dapat_mengakses_dan_melakukan_crud_pengguna(): void
    {
        $this->actingAs($this->mahasiswa);

        // GET index ditolak 403
        $this->get(route('admin.users.index'))->assertStatus(403);

        // POST store ditolak 403
        $this->post(route('admin.users.store'), [
            'name' => 'Hacker User',
            'email' => 'hacker@unib.ac.id',
            'role' => 'admin',
            'password' => 'secret123',
            'password_confirmation' => 'secret123',
        ])->assertStatus(403);

        // PUT update ditolak 403
        $this->put(route('admin.users.update', $this->admin->id), [
            'name' => 'Tampered Admin',
            'email' => 'tampered@admin.com',
            'role' => 'admin',
        ])->assertStatus(403);

        // DELETE destroy ditolak 403
        $this->delete(route('admin.users.destroy', $this->admin->id))->assertStatus(403);
    }
}
