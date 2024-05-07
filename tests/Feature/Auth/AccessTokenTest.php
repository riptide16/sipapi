<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Tests\TestCase;

class AccessTokenTest extends TestCase
{
    public function test_successful_request_token()
    {
        $user = User::factory()->create();
        $data = [
            'grant_type' => 'password',
            'client_id' => $this->client->id,
            'client_secret' => $this->client->secret,
            'username' => $user->email,
            'password' => 'password',
        ];
        $response = $this->postJson(route('auth.token'), $data);

        $response->assertStatus(200)
                 ->assertJson(['success' => true])
                 ->assertJson(['data' => ['token_type' => 'Bearer']])
                 ->assertJsonStructure([
                     'data' => [
                         'token_type',
                         'expires_in',
                         'access_token',
                         'refresh_token',
                     ],
                 ]);
    }

    public function test_failed_request_token_inactive_user()
    {
        $user = User::factory()->inactive()->create();
        $data = [
            'grant_type' => 'password',
            'client_id' => $this->client->id,
            'client_secret' => $this->client->secret,
            'username' => $user->email,
            'password' => 'password',
        ];
        $response = $this->postJson(route('auth.token'), $data);

        $response->assertStatus(400)
                 ->assertJson(['success' => false])
                 ->assertJson(['code' => 'ERR4100']);
    }

    public function test_failed_request_token_unverified_user()
    {
        $user = User::factory()->inactive()->create();
        $data = [
            'grant_type' => 'password',
            'client_id' => $this->client->id,
            'client_secret' => $this->client->secret,
            'username' => $user->email,
            'password' => 'password',
        ];
        $response = $this->postJson(route('auth.token'), $data);

        $response->assertStatus(400)
                 ->assertJson(['success' => false])
                 ->assertJson(['code' => 'ERR4100']);
    }

    public function test_failed_request_token_wrong_credentials()
    {
        $user = User::factory()->create();
        $data = [
            'grant_type' => 'password',
            'client_id' => $this->client->id,
            'client_secret' => $this->client->secret,
            'username' => $user->email,
            'password' => 'passwordsalah',
        ];
        $response = $this->postJson(route('auth.token'), $data);

        $response->assertStatus(400)
                 ->assertJson(['success' => false])
                 ->assertJson(['code' => 'ERR4100']);
    }
}
