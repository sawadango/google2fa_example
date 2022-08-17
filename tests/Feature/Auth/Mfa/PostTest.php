<?php

namespace Tests\Feature\Auth\Mfa;

use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\User;
use Illuminate\Testing\TestResponse;
use Tests\TestCase;

class PostTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @testdoc ログイン時200
     *
     * @return void
     */
    public function testOk()
    {
        $this->markTestSkipped('モック化したら外す');

        $user = $this->createUser();
        $user->wasRecentlyCreated = false;
        $this->actingAs($user);

        $secretKey = 'ZTDAND2IEPZWDCF7';
        $otp = '196452';

        $response = $this->postMfa([
            'secret_key' => $secretKey,
            'otp' => $otp
        ]);

        $response->assertStatus(200);

        $this->assertDatabaseHas('users', [
            'google2fa_secret' => $secretKey,
        ]);
    }

    /**
     * @param User $user
     * @param string $password
     *
     * @return TestResponse
     */
    private function postMfa(array $conditions): TestResponse
    {
        return $this->post(
            '/api/mfa',
            [
                'secret_key'  => $conditions['secret_key'],
                'otp' => $conditions['otp'],
            ]
        );
    }

    /**
     * @return User
     */
    private function createUser(): User
    {
        return User::factory()->create([
            'email' => 'test@example.com',
            'password' => bcrypt('password'),
        ]);
    }
}
