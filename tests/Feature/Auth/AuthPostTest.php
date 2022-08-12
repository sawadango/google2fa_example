<?php

namespace Tests\Feature\Auth;

use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\User;
use Illuminate\Testing\TestResponse;
use Tests\TestCase;

class AuthPostTest extends TestCase
{
    use RefreshDatabase;

    const TRUE_PASSWORD = 'true';

    const WRONG_PASSWORD = 'wrong';

    /**
     * @testdoc 正しいパスワードで200
     *
     * @return void
     */
    public function testOk()
    {
        $user = $this->createUser();

        $response = $this->postAuthenticate($user, self::TRUE_PASSWORD);

        $response->assertStatus(200);
        dd($response);
    }


    /**
     * @testdoc 誤ったパスワードで401
     */
    public function test401()
    {
        $user = $this->createUser();

        $response = $this->postAuthenticate($user, self::WRONG_PASSWORD);

        $response->assertStatus(401);
    }

    /**
     * @param User $user
     * @param string $password
     *
     * @return TestResponse
     */
    private function postAuthenticate(User $user, string $password): TestResponse
    {
        return $this->post(
            '/api/authenticate',
            [
                'email'  => $user->email,
                'password' => $password,
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
            'password' => bcrypt(self::TRUE_PASSWORD),
        ]);
    }
}
