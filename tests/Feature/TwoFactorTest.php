<?php

namespace Tests\Feature;

use App\Models\Role;
use App\Models\User;
use App\Services\TotpService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TwoFactorTest extends TestCase
{
    use RefreshDatabase;

    private Role $roleUser;
    private TotpService $totp;

    protected function setUp(): void
    {
        parent::setUp();
        $this->roleUser = Role::create(['name' => 'user', 'description' => 'User']);
        $this->totp = new TotpService();
    }

    private function createUser(array $attributes = []): User
    {
        return User::create(array_merge([
            'name' => '2FA Test User',
            'email' => uniqid() . '@example.com',
            'password' => bcrypt('password123'),
            'role_id' => $this->roleUser->id,
            'is_active' => true,
        ], $attributes));
    }

    public function test_user_can_access_two_factor_setup_page(): void
    {
        $user = $this->createUser();

        $response = $this->actingAs($user)->get('/two-factor-setup');

        $response->assertStatus(200);
        $response->assertSee('2FA');
        $this->assertNotNull($user->fresh()->two_factor_secret);
    }

    public function test_user_can_confirm_two_factor_with_valid_totp_code(): void
    {
        $user = $this->createUser();
        $this->actingAs($user)->get('/two-factor-setup');
        $secret = $user->fresh()->two_factor_secret;

        $validCode = $this->totp->getCode($secret);

        $response = $this->actingAs($user)->post('/two-factor/confirm', [
            'code' => $validCode,
        ]);

        $response->assertRedirect('/profile');
        $response->assertSessionHas('success');
        $this->assertTrue($user->fresh()->hasTwoFactorEnabled());
    }

    public function test_two_factor_confirmation_fails_with_invalid_code(): void
    {
        $user = $this->createUser();
        $this->actingAs($user)->get('/two-factor-setup');

        $response = $this->actingAs($user)->post('/two-factor/confirm', [
            'code' => '000000',
        ]);

        $response->assertSessionHasErrors('code');
        $this->assertFalse($user->fresh()->hasTwoFactorEnabled());
    }

    public function test_user_with_two_factor_is_redirected_to_challenge_on_login(): void
    {
        $secret = $this->totp->generateSecret();
        $user = $this->createUser([
            'email' => '2fa-enabled@example.com',
            'two_factor_secret' => $secret,
            'two_factor_confirmed_at' => now(),
            'two_factor_recovery_codes' => ['TEST-1234', 'TEST-5678'],
        ]);

        $response = $this->post('/login', [
            'email' => '2fa-enabled@example.com',
            'password' => 'password123',
        ]);

        $response->assertRedirect('/two-factor-challenge');
        $this->assertGuest();
        $this->assertEquals($user->id, session('2fa:user_id'));
    }

    public function test_user_can_complete_login_with_valid_totp_code(): void
    {
        $secret = $this->totp->generateSecret();
        $user = $this->createUser([
            'email' => '2fa-login@example.com',
            'two_factor_secret' => $secret,
            'two_factor_confirmed_at' => now(),
        ]);

        $code = $this->totp->getCode($secret);

        $response = $this->withSession(['2fa:user_id' => $user->id])
            ->post('/two-factor-challenge', [
                'code' => $code,
            ]);

        $response->assertRedirect('/');
        $this->assertAuthenticatedAs($user);
        $this->assertNull(session('2fa:user_id'));
    }

    public function test_user_cannot_complete_login_with_invalid_code(): void
    {
        $secret = $this->totp->generateSecret();
        $user = $this->createUser([
            'email' => '2fa-fail@example.com',
            'two_factor_secret' => $secret,
            'two_factor_confirmed_at' => now(),
        ]);

        $response = $this->withSession(['2fa:user_id' => $user->id])
            ->post('/two-factor-challenge', [
                'code' => '999999',
            ]);

        $response->assertSessionHasErrors('code');
        $this->assertGuest();
    }

    public function test_user_can_complete_login_with_recovery_code(): void
    {
        $secret = $this->totp->generateSecret();
        $user = $this->createUser([
            'email' => '2fa-recovery@example.com',
            'two_factor_secret' => $secret,
            'two_factor_confirmed_at' => now(),
            'two_factor_recovery_codes' => ['RC-1111', 'RC-2222'],
        ]);

        $response = $this->withSession(['2fa:user_id' => $user->id])
            ->post('/two-factor-challenge', [
                'recovery_code' => 'RC-1111',
            ]);

        $response->assertRedirect('/');
        $this->assertAuthenticatedAs($user);

        // Verify recovery code was consumed
        $freshUser = $user->fresh();
        $this->assertNotContains('RC-1111', $freshUser->two_factor_recovery_codes);
        $this->assertContains('RC-2222', $freshUser->two_factor_recovery_codes);
    }

    public function test_user_can_disable_two_factor_with_correct_password(): void
    {
        $user = $this->createUser([
            'two_factor_secret' => $this->totp->generateSecret(),
            'two_factor_confirmed_at' => now(),
            'two_factor_recovery_codes' => ['RC-1111'],
        ]);

        $response = $this->actingAs($user)->post('/two-factor/disable', [
            'current_password' => 'password123',
        ]);

        $response->assertRedirect('/profile');
        $response->assertSessionHas('success');
        $this->assertFalse($user->fresh()->hasTwoFactorEnabled());
    }

    public function test_user_cannot_disable_two_factor_with_wrong_password(): void
    {
        $user = $this->createUser([
            'two_factor_secret' => $this->totp->generateSecret(),
            'two_factor_confirmed_at' => now(),
        ]);

        $response = $this->actingAs($user)->post('/two-factor/disable', [
            'current_password' => 'wrong-password',
        ]);

        $response->assertSessionHasErrors('current_password');
        $this->assertTrue($user->fresh()->hasTwoFactorEnabled());
    }
}

