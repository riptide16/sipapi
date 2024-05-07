<?php

namespace Database\Factories;

use App\Models\Notification;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class NotificationFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Notification::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $user = User::factory()->create();

        return [
            'type' => \Str::random(12),
            'notifiable_type' => 'App\Models\User',
            'notifiable_id' => $user->id,
            'data' => [
                'title' => $this->faker->sentence(),
                'body' => $this->faker->sentence(),
                'target_url' => $this->faker->url(),
            ],
        ];
    }

    public function read()
    {
        return $this->state(function (array $attributes) {
            return [
                'read_at' => now(),
            ];
        });
    }
}
