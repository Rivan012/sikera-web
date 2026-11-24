<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\TestWith;
use Tests\TestCase;

class ProfileTest extends TestCase
{
    use RefreshDatabase;

    /** @return array<string, mixed> */
    private function studentProfile(User $user): array
    {
        return [
            'name' => 'Dewi Anggraini', 'initials' => 'DA', 'email' => $user->email,
            'nim' => 'a123', 'usia' => 20, 'gender' => 'P', 'agama' => 'Islam',
            'prodi' => 'Farmasi', 'fakultas' => 'MIPA', 'pendidikan_terakhir' => 'SMA',
            'menarche_age' => 12, 'avatar' => 'https://example.test/foto.jpg',
        ];
    }

    public function test_guest_cannot_read_or_update_a_profile(): void
    {
        $this->get('/profil')->assertRedirectToRoute('login');
        $this->put('/profil')->assertRedirectToRoute('login');
        $this->getJson('/api/v1/auth/me')->assertUnauthorized();
        $this->putJson('/api/v1/auth/me')->assertUnauthorized();
    }

    public function test_first_login_form_saves_all_biodata_for_the_profile_editor(): void
    {
        $user = User::factory()->create(['role' => 'mahasiswa', 'pendidikan_terakhir' => 'D2']);
        $data = $this->studentProfile($user);
        unset($data['avatar'], $data['email']);

        $form = $this->actingAs($user)->get('/biodata')->assertOk()->assertSee('value="D2"', false);
        foreach (array_keys($data) as $field) {
            $form->assertSee('name="'.$field.'"', false);
        }

        $this->post('/biodata', $data)->assertRedirectToRoute('mahasiswa.dashboard');
        $this->get('/profil')->assertOk()->assertSee('value="A123"', false)->assertSee('value="MIPA"', false);
        $this->assertDatabaseHas('users', [...$data, 'id' => $user->id, 'nim' => 'A123', 'is_biodata_filled' => true]);
    }

    public function test_mobile_biodata_saves_new_fields_and_accepts_older_clients(): void
    {
        $user = User::factory()->create(['role' => 'mahasiswa']);
        $this->withToken($user->createToken('test')->plainTextToken);
        $core = ['usia' => 19, 'gender' => 'P', 'agama' => 'Islam', 'prodi' => 'Farmasi', 'pendidikan_terakhir' => 'SMA'];
        $this->putJson('/api/v1/auth/biodata', [
            ...$core, 'name' => 'Dewi', 'nim' => 'a123', 'menarche_age' => 12, 'fakultas' => 'MIPA',
            'initials' => 'DA', 'role' => 'admin', 'email' => 'other@example.test',
        ])->assertOk()->assertJsonPath('data.user.is_biodata_filled', true);

        $this->putJson('/api/v1/auth/biodata', [...$core, 'fakultas' => null, 'usia' => 20])
            ->assertOk()->assertJsonPath('data.user.usia', 20);
        $this->getJson('/api/v1/auth/me')->assertOk()->assertJsonPath('data.user.nim', 'A123')
            ->assertJsonPath('data.user.menarche_age', 12)->assertJsonPath('data.user.name', 'Dewi')
            ->assertJsonPath('data.user.fakultas', 'MIPA')->assertJsonPath('data.user.initials', 'DA')
            ->assertJsonPath('data.user.email', $user->email)->assertJsonPath('data.user.role', 'mahasiswa');
    }

    public function test_invalid_biodata_does_not_mark_onboarding_complete(): void
    {
        $user = User::factory()->create(['role' => 'mahasiswa']);
        User::factory()->create(['nim' => 'A123']);
        $this->withToken($user->createToken('test')->plainTextToken)
            ->putJson('/api/v1/auth/biodata', [...$this->studentProfile($user), 'menarche_age' => 30])
            ->assertUnprocessable()->assertJsonPath('success', false)->assertJsonValidationErrors(['nim', 'menarche_age']);
        $this->assertDatabaseHas('users', ['id' => $user->id, 'is_biodata_filled' => false, 'nim' => null]);
    }

    public function test_student_can_edit_completed_profile_from_web_and_read_changes_via_api(): void
    {
        $user = User::factory()->create(['role' => 'mahasiswa', 'is_biodata_filled' => true, 'pretest_completed' => true]);
        $profile = $this->studentProfile($user);

        $this->actingAs($user)->get('/profil')->assertOk()->assertSee($user->email)->assertSee('Simpan perubahan');
        $this->put('/profil', $profile)->assertRedirectToRoute('profile.edit')->assertSessionHas('success');
        $this->get('/profil')->assertOk()->assertSee('Dewi Anggraini')->assertSee('A123');
        $this->getJson('/api/v1/auth/me')->assertOk()->assertJsonPath('data.user.fakultas', 'MIPA');
        $this->assertDatabaseHas('users', [...$profile, 'nim' => 'A123', 'id' => $user->id, 'pretest_completed' => true]);
    }

    public function test_api_updates_only_the_token_owners_profile_and_completes_biodata(): void
    {
        $user = User::factory()->create(['role' => 'mahasiswa', 'nim' => 'A123']);
        $other = User::factory()->create();
        $data = [...$this->studentProfile($user), 'email' => 'dewi@example.test', 'current_password' => 'password'];
        $token = $user->createToken('test')->plainTextToken;

        $this->withToken($token)->putJson('/api/v1/auth/me', [
            ...$data, 'id' => $other->id, 'role' => 'admin', 'points' => 999,
            'is_biodata_filled' => false, 'pretest_completed' => true,
        ])->assertOk()->assertJsonPath('data.user.id', $user->id)
            ->assertJsonPath('data.user.is_biodata_filled', true)
            ->assertJsonPath('data.user.email', 'dewi@example.test')->assertJsonMissingPath('data.user.password');
        $this->assertDatabaseHas('users', [
            'id' => $user->id, 'email' => 'dewi@example.test', 'email_verified_at' => null,
            'role' => 'mahasiswa', 'points' => 0, 'pretest_completed' => false,
        ]);
        $this->assertDatabaseHas('users', ['id' => $other->id, 'name' => $other->name, 'email' => $other->email]);
    }

    public function test_invalid_web_submission_preserves_input_and_does_not_save_changes(): void
    {
        $user = User::factory()->create(['role' => 'mahasiswa']);
        $other = User::factory()->create(['nim' => 'ABC123']);

        $this->actingAs($user)->from('/profil')->put('/profil', [
            'name' => 'Dewi', 'email' => $other->email, 'nim' => 'abc123',
            'usia' => 41, 'avatar' => 'http://example.test/photo.jpg',
        ])->assertRedirect('/profil')->assertSessionHasInput('name', 'Dewi')
            ->assertSessionHasErrors(['email', 'nim', 'usia', 'gender', 'agama', 'prodi', 'pendidikan_terakhir', 'avatar', 'current_password']);
        $this->assertDatabaseHas('users', ['id' => $user->id, 'name' => $user->name, 'is_biodata_filled' => false]);
    }

    #[TestWith(['admin'])]
    #[TestWith(['dosen_pa'])]
    public function test_staff_can_edit_identity_without_student_biodata(string $role): void
    {
        $user = User::factory()->create(['role' => $role, 'fakultas' => 'MIPA', 'avatar' => 'https://example.test/avatar.jpg']);

        $this->withToken($user->createToken('test')->plainTextToken)
            ->putJson('/api/v1/auth/me', ['name' => 'Rina', 'email' => $user->email, 'avatar' => null])
            ->assertOk()->assertJsonPath('data.user.name', 'Rina');
        $this->assertDatabaseHas('users', [
            'id' => $user->id, 'name' => 'Rina', 'is_biodata_filled' => false, 'avatar' => null, 'fakultas' => 'MIPA',
        ]);
    }

    public function test_email_change_requires_correct_password(): void
    {
        $user = User::factory()->create(['role' => 'admin']);

        $this->withToken($user->createToken('test')->plainTextToken)->putJson('/api/v1/auth/me', [
            'name' => $user->name, 'email' => 'new@example.test', 'current_password' => 'incorrect',
        ])->assertUnprocessable()->assertJsonValidationErrors('current_password');
        $this->assertDatabaseHas('users', ['id' => $user->id, 'email' => $user->email]);
    }

    public function test_google_identity_is_preserved_while_biodata_can_be_edited(): void
    {
        $user = User::factory()->create(['google_id' => 'linked', 'role' => 'mahasiswa', 'avatar' => 'https://example.test/google.jpg']);
        $data = $this->studentProfile($user);
        $this->withToken($user->createToken('test')->plainTextToken)->putJson('/api/v1/auth/me', [
            ...$data, 'email' => 'other@example.test',
        ])->assertUnprocessable()->assertJsonValidationErrors(['email', 'avatar']);

        unset($data['avatar']);
        $this->putJson('/api/v1/auth/me', $data)->assertOk()->assertJsonPath('data.user.avatar', $user->avatar);
        $this->assertDatabaseHas('users', ['id' => $user->id, 'email' => $user->email, 'prodi' => 'Farmasi']);
    }
}
