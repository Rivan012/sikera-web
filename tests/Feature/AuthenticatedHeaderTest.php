<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthenticatedHeaderTest extends TestCase
{
    use RefreshDatabase;

    public function test_authenticated_header_displays_avatar_instead_of_initials(): void
    {
        $user = User::factory()->create([
            'name' => 'Dewi Anggraini',
            'initials' => 'DA',
            'avatar' => 'https://example.test/avatar.jpg',
        ]);

        $this->actingAs($user);

        $this->view('layouts.partials.header')
            ->assertSee($user->avatar)
            ->assertSee('alt="'.$user->name.'"', false)
            ->assertDontSee('>DA</span>', false);
    }

    public function test_authenticated_header_falls_back_to_initials_without_avatar(): void
    {
        $user = User::factory()->create([
            'name' => 'Dewi Anggraini',
            'initials' => 'DA',
            'avatar' => null,
        ]);

        $this->actingAs($user);

        $this->view('layouts.partials.header')
            ->assertSee('>DA</span>', false)
            ->assertDontSee('<img', false);
    }
}
