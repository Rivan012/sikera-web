<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Socialite\Contracts\Provider;
use Laravel\Socialite\Facades\Socialite;
use Laravel\Socialite\Two\GoogleProvider;
use Laravel\Socialite\Two\InvalidStateException;
use Laravel\Socialite\Two\User as SocialiteUser;
use Mockery;
use PHPUnit\Framework\Attributes\TestWith;
use Tests\TestCase;

class GoogleLoginTest extends TestCase
{
    use RefreshDatabase;

    private function mockMobileIdentity(array $claims = []): string
    {
        $token = 'header.payload.'.str_repeat('x', 100);
        $provider = Mockery::mock(GoogleProvider::class);
        $provider->shouldReceive('userFromToken')->with($token)->andReturn(SocialiteUser::fake($claims + [
            'id' => 'google-student',
            'email' => 'student@campus.test',
            'hd' => 'campus.test',
            'email_verified' => true,
            'exp' => time() + 3600,
        ]));
        Socialite::shouldReceive('driver')->with('google')->andReturn($provider);

        return $token;
    }

    public function test_mobile_google_login_uses_verified_identity_and_returns_a_working_session(): void
    {
        $token = $this->mockMobileIdentity();

        $response = $this->postJson('/api/v1/auth/login', ['id_token' => $token, 'email' => 'ignored@example.test'])
            ->assertOk()->assertJsonPath('data.user.email', 'student@campus.test')
            ->assertJsonPath('data.token_type', 'Bearer');
        $id = $response->json('data.user.id');
        $this->withToken($response->json('data.token'))->getJson('/api/v1/auth/me')
            ->assertOk()->assertJsonPath('data.user.id', $id);
        $this->postJson('/api/v1/auth/login', ['id_token' => $token])
            ->assertOk()->assertJsonPath('data.user.id', $id);
        $this->assertDatabaseCount('users', 1);
    }

    public function test_google_links_an_existing_campus_account_and_preserves_password_login(): void
    {
        $user = User::factory()->create(['email' => 'student@campus.test']);

        $this->postJson('/api/v1/auth/login', ['id_token' => $this->mockMobileIdentity()])
            ->assertOk()->assertJsonPath('data.user.id', $user->id);
        $this->postJson('/api/v1/auth/login', ['email' => $user->email, 'password' => 'password'])
            ->assertOk()->assertJsonPath('data.user.id', $user->id);
        $this->assertSame('google-student', $user->fresh()->google_id);
    }

    #[TestWith([['email_verified' => false], null])]
    #[TestWith([['hd' => null], null])]
    #[TestWith([['exp' => 0], null])]
    #[TestWith([[], 'other-google-id'])]
    public function test_google_login_rejects_invalid_claims_or_conflicting_accounts(array $claims, ?string $linkedId): void
    {
        $user = User::factory()->create(['email' => 'student@campus.test', 'google_id' => $linkedId]);

        $this->postJson('/api/v1/auth/login', ['id_token' => $this->mockMobileIdentity($claims)])
            ->assertUnauthorized();
        $this->assertSame($linkedId, $user->fresh()->google_id);
        $this->assertDatabaseEmpty('personal_access_tokens');
    }

    public function test_mobile_login_rejects_invalid_google_credentials(): void
    {
        $this->postJson('/api/v1/auth/login', ['id_token' => 'access-token'])->assertUnprocessable();
        $token = 'header.payload.'.str_repeat('x', 100);
        $provider = Mockery::mock(GoogleProvider::class);
        $provider->shouldReceive('userFromToken')->once()->with($token)->andThrow(new \Exception('Invalid signature'));
        Socialite::shouldReceive('driver')->with('google')->andReturn($provider);

        $this->postJson('/api/v1/auth/login', ['id_token' => $token])->assertUnauthorized();
        $this->assertDatabaseEmpty('users');
        $this->assertDatabaseEmpty('personal_access_tokens');
    }

    private function makeJwtWithAudience(string $audience): string
    {
        $header = rtrim(strtr(base64_encode(json_encode(['alg' => 'RS256', 'typ' => 'JWT'])), '+/', '-_'), '=');
        $payload = rtrim(strtr(base64_encode(json_encode(['aud' => $audience, 'sub' => 'google-mobile-user'])), '+/', '-_'), '=');

        return $header.'.'.$payload.'.'.str_repeat('x', 80);
    }

    public function test_mobile_google_login_accepts_android_pkce_client_id_audience(): void
    {
        $androidClientId = '755858406803-android.apps.googleusercontent.com';
        config([
            'services.google.client_id' => '755858406803-web.apps.googleusercontent.com',
            'services.google.android_client_id' => $androidClientId,
            'services.google.allowed_client_ids' => ['755858406803-web.apps.googleusercontent.com'],
        ]);

        $token = $this->makeJwtWithAudience($androidClientId);
        $provider = Mockery::mock(GoogleProvider::class);
        $provider->shouldReceive('userFromToken')->once()->with($token)->andReturn(SocialiteUser::fake([
            'id' => 'google-android-student',
            'name' => 'Aisyah Android',
            'email' => 'aisyah.android@gmail.com',
            'avatar' => 'https://lh3.googleusercontent.com/avatar.png',
            'email_verified' => true,
            'exp' => time() + 3600,
        ]));

        Socialite::shouldReceive('buildProvider')
            ->once()
            ->with(GoogleProvider::class, Mockery::on(fn (array $cfg) => $cfg['client_id'] === $androidClientId))
            ->andReturn($provider);

        $response = $this->postJson('/api/v1/auth/login', ['id_token' => $token])
            ->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonPath('data.token_type', 'Bearer')
            ->assertJsonPath('data.user.email', 'aisyah.android@gmail.com');

        $this->withToken($response->json('data.token'))
            ->getJson('/api/v1/auth/me')
            ->assertOk()
            ->assertJsonPath('data.user.email', 'aisyah.android@gmail.com');
    }

    public function test_mobile_google_login_rejects_unlisted_client_id_audience(): void
    {
        config([
            'services.google.client_id' => '755858406803-web.apps.googleusercontent.com',
            'services.google.android_client_id' => '755858406803-android.apps.googleusercontent.com',
            'services.google.allowed_client_ids' => [],
        ]);

        $token = $this->makeJwtWithAudience('999999999999-untrusted.apps.googleusercontent.com');
        $provider = Mockery::mock(GoogleProvider::class);
        $provider->shouldReceive('userFromToken')->once()->with($token)->andThrow(new \Exception('Invalid ID token audience.'));
        Socialite::shouldReceive('driver')->once()->with('google')->andReturn($provider);

        $this->postJson('/api/v1/auth/login', ['id_token' => $token])
            ->assertUnauthorized();
        $this->assertDatabaseEmpty('users');
        $this->assertDatabaseEmpty('personal_access_tokens');
    }

    public function test_google_redirect_uses_the_callback_origin(): void
    {
        config([
            'services.google.redirect' => 'http://localhost:8000/auth/google/callback',
        ]);

        $response = $this->get('http://127.0.0.1:8000/auth/google');

        $response->assertRedirect('http://localhost:8000/auth/google/start');
    }

    public function test_web_login_reaches_google_behind_an_https_proxy(): void
    {
        $callback = 'https://sikera.example/auth/google/callback';
        config([
            'services.google.redirect' => $callback,
            'services.google.client_id' => 'web-client',
            'services.google.client_secret' => 'test-secret',
        ]);

        $start = $this->get('http://sikera.example/auth/google')
            ->assertRedirect('https://sikera.example/auth/google/start');

        $path = parse_url($start->headers->get('Location'), PHP_URL_PATH);
        $authorization = $this->get('http://sikera.example'.$path)->assertRedirect();
        $destination = $authorization->headers->get('Location');
        $this->assertSame('accounts.google.com', parse_url($destination, PHP_URL_HOST));
        parse_str(parse_url($destination, PHP_URL_QUERY), $query);
        $this->assertSame($callback, $query['redirect_uri']);
        $this->assertNotEmpty($query['state']);
        $authorization->assertSessionHas('state', $query['state']);
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
