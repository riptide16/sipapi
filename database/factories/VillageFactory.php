<?php

namespace Database\Factories;

use App\Models\Subdistrict;
use App\Models\Village;
use Illuminate\Database\Eloquent\Factories\Factory;

class VillageFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Village::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'name' => $this->faker->city,
            'postal_code' => (string) $this->faker->randomNumber(5),
            'subdistrict_id' => Subdistrict::factory()->create()->id,
        ];
    }
}
