<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Socialite\Contracts\Provider;
use Laravel\Socialite\Facades\Socialite;
use Laravel\Socialite\Two\InvalidStateException;
use Laravel\Socialite\Two\User as SocialiteUser;
use Mockery;
use Tests\TestCase;

class GoogleLoginTest extends TestCase
{
    use RefreshDatabase;

    public function test_google_redirect_uses_the_callback_origin(): void
    {
        config([
            'services.google.redirect' => 'http://localhost:8000/auth/google/callback',
        ]);

        $response = $this->get('http://127.0.0.1:8000/auth/google');

        $response->assertRedirect('http://localhost:8000/auth/google');
    }

    public function test_google_callback_creates_user_with_long_avatar_url(): void
    {
        $avatarUrl = 'https://googleusercontent.com/'.str_repeat('a', 500);

        Socialite::fake('google', SocialiteUser::fake([
            'id' => 'google-user-123',
            'name' => 'Google User',
            'email' => 'google-user@example.test',
            'avatar' => $avatarUrl,
        ]));

        $response = $this->get(route('google.callback'));

        $response->assertRedirect('/dashboard');
        $this->assertAuthenticated();
        $this->assertDatabaseHas('users', [
            'email' => 'google-user@example.test',
            'google_id' => 'google-user-123',
            'avatar' => $avatarUrl,
        ]);
    }

    public function test_invalid_google_state_returns_to_login_with_retry_message(): void
    {
        $provider = Mockery::mock(Provider::class);
        $provider->shouldReceive('user')
            ->once()
            ->andThrow(new InvalidStateException);

        Socialite::shouldReceive('driver')
            ->once()
            ->with('google')
            ->andReturn($provider);

        $response = $this->get(route('google.callback'));

        $response->assertRedirect(route('login'))
            ->assertSessionHas(
                'warning',
                'Sesi login Google tidak valid atau kedaluwarsa. Silakan mulai ulang login.'
            );
    }
}
