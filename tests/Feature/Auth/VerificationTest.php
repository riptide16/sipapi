<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use App\Models\UserVerificationToken;
use Illuminate\Auth\Events\Verified;
use Illuminate\Support\Facades\Event;
use Tests\TestCase;

class VerificationTest extends TestCase
{
    public function test_verify()
    {
        $user = User::factory()->inactive()->create();
        $token = UserVerificationToken::factory()->create([
            'user_id' => $user->id,
        ]);
        Event::fake();

        $response = $this->postJson(route('auth.verification'), ['token' => $token->token]);

        $response->assertStatus(200);
        $this->assertNotNull($token->user->refresh()->email_verified_at);

        Event::assertDispatched(function (Verified $event) use ($token) {
            return $event->user->id === $token->user->id;
        });
    }
}
