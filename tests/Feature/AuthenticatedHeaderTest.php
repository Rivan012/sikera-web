<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

class AuthenticatedHeaderTest extends TestCase
{
    use RefreshDatabase;

    public function test_authenticated_header_shares_avatar_for_react_shell(): void
    {
        $user = User::factory()->create([
            'name' => 'Dewi Anggraini',
            'initials' => 'DA',
            'avatar' => 'https://example.test/avatar.jpg',
        ]);

        $this->actingAs($user)
            ->get('/profil')
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->component('Profile/Edit')
                ->where('auth.user.name', 'Dewi Anggraini')
                ->where('auth.user.avatar', 'https://example.test/avatar.jpg')
            );
    }

    public function test_authenticated_header_falls_back_to_initials_without_avatar(): void
    {
        $user = User::factory()->create([
            'name' => 'Dewi Anggraini',
            'initials' => 'DA',
            'avatar' => null,
        ]);

        $this->actingAs($user)
            ->get('/profil')
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->component('Profile/Edit')
                ->where('auth.user.initials', 'DA')
                ->where('auth.user.avatar', null)
            );
    }
}
