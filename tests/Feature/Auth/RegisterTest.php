<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use App\Notifications\VerifyEmail;
use Illuminate\Support\Facades\Notification;
use Illuminate\Auth\Events\Registered;
use Tests\TestCase;

class RegisterTest extends TestCase
{
    public function test_register()
    {
        Notification::fake();

        $data = [
            'email' => 'tes@email.com',
            'username' => 'username',
            'name' => 'Name',
            'password' => 'pass1234',
            'password_confirmation' => 'pass1234',
            'institution_name' => 'Lembaga 1',
        ];
        $response = $this->postJson(route('auth.register', $data));

        $response->assertStatus(201);
        $this->assertDatabaseHas('users', [
            'email' => $data['email'],
            'username' => $data['username'],
            'email_verified_at' => null,
            'status' => User::STATUS_INACTIVE,
        ]);

        $user = User::find($response['data']['id']);
        Notification::assertSentTo([$user], VerifyEmail::class);
    }
}
