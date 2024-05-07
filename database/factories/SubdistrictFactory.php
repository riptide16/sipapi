<?php

namespace Database\Factories;

use App\Models\City;
use App\Models\Subdistrict;
use Illuminate\Database\Eloquent\Factories\Factory;

class SubdistrictFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Subdistrict::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'name' => $this->faker->city,
            'city_id' => City::factory()->create()->id,
        ];
    }
}
