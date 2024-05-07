<?php

namespace Database\Factories;

use App\Models\Instrument;
use App\Models\InstrumentAspect;
use App\Models\InstrumentComponent;
use Illuminate\Database\Eloquent\Factories\Factory;

class InstrumentAspectFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = InstrumentAspect::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'aspect' => $this->faker->sentence(),
            'type' => $this->faker->randomElement($this->model::typeList()),
            'instrument_id' => Instrument::inRandomOrder()->first()->id,
            'instrument_component_id' => InstrumentComponent::factory()->create()->id,
        ];
    }

    public function choice()
    {
        return $this->state(function (array $attributes) {
            return [
                'type' => $this->model::TYPE_CHOICE,
            ];
        });
    }

    public function proof()
    {
        return $this->state(function (array $attributes) {
            return [
                'type' => $this->model::TYPE_PROOF,
            ];
        });
    }
}
