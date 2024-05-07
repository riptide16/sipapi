<?php

namespace Database\Factories;

use App\Models\User;
use App\Models\UserVerificationToken;
use Illuminate\Database\Eloquent\Factories\Factory;

class UserVerificationTokenFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = UserVerificationToken::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'token' => UserVerificationToken::generateToken(),
            'user_id' => User::factory()->create()->id,
            'expires_at' => now()->addHours(1),
        ];
    }
}
