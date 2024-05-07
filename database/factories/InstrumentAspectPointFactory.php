<?php

namespace Database\Factories;

use App\Models\InstrumentAspect;
use App\Models\InstrumentAspectPoint;
use Illuminate\Database\Eloquent\Factories\Factory;

class InstrumentAspectPointFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = InstrumentAspectPoint::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $aspect = InstrumentAspect::factory()->create();

        return [
            'statement' => $this->faker->sentence(),
            'order' => $this->faker->numberBetween(1, 20),
            'instrument_aspect_id' => $aspect->id,
            'value' => $aspect->isChoice() ? $this->faker->numberBetween(1, 5) : null,
        ];
    }
}
