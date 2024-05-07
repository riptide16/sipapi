<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Hash;
use Tests\TestCase;

class ForgotPasswordTest extends TestCase
{
    public function test_forgot_password()
    {
        $user = User::factory()->create();
        $response = $this->postJson(route('auth.forgot_password', ['email' => $user->email]));

        $response->assertStatus(200)
                 ->assertJson(['success' => true]);
        $this->assertDatabaseHas('password_resets', [
            'email' => $user->email,
        ]);
    }

    public function test_reset_password()
    {
        $user = User::factory()->create();
        $token = app('auth.password.broker')->createToken($user);

        $data = [
            'email' => $user->email,
            'token' => $token,
            'password' => 'newpass123',
            'password_confirmation' => 'newpass123',
        ];
        $response = $this->postJson(route('auth.reset_password', $data));

        $response->assertStatus(200)
                 ->assertJson(['success' => true]);
        $this->assertTrue(Hash::check($data['password'], $user->refresh()->password));
    }
}
