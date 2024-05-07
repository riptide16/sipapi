<?php

namespace Database\Factories;

use App\Models\AccreditationContent;
use App\Models\Evaluation;
use App\Models\EvaluationContent;
use App\Models\InstrumentAspect;
use App\Models\InstrumentAspectPoint;
use Illuminate\Database\Eloquent\Factories\Factory;

class EvaluationContentFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = EvaluationContent::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $aspect = InstrumentAspect::factory()->choice()->create();
        $points = InstrumentAspectPoint::factory()->count(5)->create([
            'instrument_aspect_id' => $aspect->id,
        ]);
        $contentPoint = $this->faker->randomElement($points);
        $accContent = AccreditationContent::factory()->choice()->create([
            'aspectable_id' => $aspect->id,
            'instrument_aspect_point_id' => $contentPoint->id,
            'statement' => $contentPoint->statement,
            'value' => $contentPoint->value,
        ]);
        $chosenPoint = $this->faker->randomElement($points);
        return [
            'evaluation_id' => Evaluation::factory()->create()->id,
            'accreditation_content_id' => $accContent->id,
            'instrument_aspect_point_id' => $chosenPoint->id,
            'statement' => $chosenPoint->statement,
            'value' => $chosenPoint->value,
        ];
    }
}
