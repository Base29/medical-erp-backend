<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LogoutTest extends TestCase
{
    use RefreshDatabase;
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function test_user_logged_out_with_valid_token()
    {
        $user = User::factory()->create();

        $response = $this->post(route('login'), [
            'email' => $user->email,
            'password' => 'password',
        ]);

        $token = $response->decodeResponseJson()['user']['token'];

        $resp = $this->withHeader('Authorization', 'Bearer ' . $token)->post(route('logout'));

        $resp->assertStatus(200);

    }
}