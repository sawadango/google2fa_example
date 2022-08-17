<?php

namespace Tests\Feature\Auth;

use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Testing\TestResponse;
use Tests\TestCase;

class AuthPostTest extends TestCase
{
    use RefreshDatabase;

    const TRUE_PASSWORD = 'true';

    const WRONG_PASSWORD = 'wrong';

    // /**
    //  * @testdoc 正しいパスワードで200
    //  *
    //  * @return void
    //  */
    // public function testOk()
    // {
    //     $user = $this->createUser();


    //     $payload = [
    //         'email' => $user->email,
    //         'password' => self::TRUE_PASSWORD,
    //     ];
    //     $response = $this->postAuthenticate($payload);

    //     $response->assertStatus(200);
    // }


    // /**
    //  * @testdoc 誤ったパスワードで401
    //  */
    // public function test401WrongPassword()
    // {
    //     $user = $this->createUser();

    //     $payload = [
    //         'email' => $user->email,
    //         'password' => self::WRONG_PASSWORD,
    //     ];
    //     $response = $this->postAuthenticate($payload);

    //     $response->assertStatus(401);
    // }

    /**
     * @testdoc 正しいパスワードだが2要素認証のOTPが間違っているとき401
     * 
     *
     * @return void
     */
    public function test401WrongOtp()
    {
        $user = $this->createUser();

        $secretKey = 'ZTDAND2IEPZWDCF7';
        $user->google2fa_secret = $secretKey;
        $user->google2fa_timestamp = Carbon::now()->timestamp / 30 - 2;
        $user->save();

        $payload = [
            'email' => $user->email,
            'password' => self::TRUE_PASSWORD,
            'secret_key' => $secretKey,
            'otp' => '951461',
        ];

        $response = $this->postAuthenticate($payload);

        $response->assertStatus(401);
    }

    /**
     * @param array $payload
     * 
     * @return TestResponse
     */
    private function postAuthenticate(array $payload): TestResponse
    {
        return $this->post(
            '/api/token',
            [
                'email'  => $payload['email'],
                'password' => $payload['password'],
                'secret_key' => $payload['secret_key'],
                'otp' => $payload['otp'],
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
