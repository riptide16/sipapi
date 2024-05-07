<?php

namespace Database\Factories;

use App\Models\Accreditation;
use App\Models\User;
use App\Models\Institution;
use Illuminate\Database\Eloquent\Factories\Factory;

class AccreditationFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Accreditation::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $user = User::factory()->assessee()->active()->create();
        return [
            'code' => $this->faker->unique()->randomNumber(8),
            'user_id' => $user->id,
            'institution_id' => Institution::factory()
                                           ->filled()
                                           ->valid()
                                           ->create(['user_id' => $user->id])
                                           ->id,
            'status' => Accreditation::STATUS_SUBMITTED,
        ];
    }

    public function reviewed()
    {
        return $this->state(function (array $attributes) {
            return [
                'status' => $this->model::STATUS_REVIEWED,
            ];
        });
    }

    public function evaluated()
    {
        return $this->state(function (array $attributes) {
            return [
                'status' => $this->model::STATUS_EVALUATED,
            ];
        });
    }
}
