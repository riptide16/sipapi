<?php

namespace Database\Factories;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class UserFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = User::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'name' => $this->faker->name(),
            'username' => $this->faker->unique()->userName(),
            'email' => $this->faker->unique()->safeEmail(),
            'email_verified_at' => now(),
            'password' => 'password',
            'remember_token' => Str::random(10),
            'status' => User::STATUS_ACTIVE,
            'activated_at' => now(),
        ];
    }

    public function inactive()
    {
        return $this->state(function (array $attributes) {
            return [
                'email_verified_at' => null,
                'status' => User::STATUS_INACTIVE,
                'activated_at' => null,
            ];
        });
    }

    public function active()
    {
        return $this->state(function (array $attributes) {
            return [
                'status' => User::STATUS_ACTIVE,
                'activated_at' => now(),
            ];
        });
    }

    public function superAdmin()
    {
        $role = Role::where('name', Role::SUPER_ADMIN)->first();
        return $this->state(function (array $attributes) use ($role) {
            return [
                'role_id' => $role->id,
            ];
        });
    }

    public function admin()
    {
        $role = Role::where('name', Role::ADMIN)->first();
        return $this->state(function (array $attributes) use ($role) {
            return [
                'role_id' => $role->id,
            ];
        });
    }

    public function assessee()
    {
        $role = Role::where('name', Role::ASSESSEE)->first();
        return $this->state(function (array $attributes) use ($role) {
            return [
                'role_id' => $role->id,
            ];
        });
    }

    public function assessor()
    {
        $role = Role::where('name', Role::ASSESSOR)->first();
        return $this->state(function (array $attributes) use ($role) {
            return [
                'role_id' => $role->id,
            ];
        });
    }
}
