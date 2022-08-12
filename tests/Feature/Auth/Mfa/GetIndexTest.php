<?php

namespace Tests\Feature\Auth\Mfa;

use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\User;
use Illuminate\Testing\TestResponse;
use Tests\TestCase;

class GetIndexTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @testdoc ログイン時200
     *
     * @return void
     */
    public function testOk()
    {
        $user = $this->createUser();
        $user->wasRecentlyCreated = false;
        $this->actingAs($user);

        $response = $this->getIndex();
        // dd($response);

        $response->assertStatus(200);
    }

    /**
     * @param User $user
     * @param string $password
     *
     * @return TestResponse
     */
    private function getIndex(): TestResponse
    {
        return $this->get(
            '/api/mfa'
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
