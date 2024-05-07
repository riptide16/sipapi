<?php

namespace Database\Factories;

use App\Models\Accreditation;
use App\Models\EvaluationAssignment;
use Illuminate\Database\Eloquent\Factories\Factory;

class EvaluationAssignmentFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = EvaluationAssignment::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $accreditation = Accreditation::factory()->create();

        return [
            'accreditation_id' => $accreditation->id,
            'scheduled_date' => $this->faker->dateTime(),
            'method' => $this->faker->randomElement($this->model::methodList()),
        ];
    }
}
