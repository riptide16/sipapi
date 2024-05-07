<?php

namespace Database\Factories;

use App\Models\InstrumentComponent;
use Illuminate\Database\Eloquent\Factories\Factory;

class InstrumentComponentFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = InstrumentComponent::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'name' => $this->faker->name(),
            'category' => $this->faker->randomElement($this->model::categoryList()),
            'weight' => $this->faker->numberBetween(1, 30),
            'type' => $this->model::TYPE_MAIN,
            'order' => $this->faker->numberBetween(1, 20),
        ];
    }

    public function sub1()
    {
        $parent = $this->create();
        return $this->state(function (array $attributes) use ($parent) {
            return [
                'weight' => null,
                'type' => $this->model::TYPE_SUB_1,
                'parent_id' => $parent->id,
            ];
        });
    }

    public function sub2()
    {
        $parent = $this->sub1()->create();
        return $this->state(function (array $attributes) use ($parent) {
            return [
                'weight' => null,
                'type' => $this->model::TYPE_SUB_2,
                'parent_id' => $parent->id,
            ];
        });
    }
}
