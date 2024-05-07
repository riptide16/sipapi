<?php

namespace Database\Factories;

use App\Models\Accreditation;
use App\Models\AccreditationContent;
use App\Models\InstrumentAspect;
use App\Models\InstrumentAspectPoint;
use App\Models\InstrumentComponent;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class AccreditationContentFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = AccreditationContent::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'accreditation_id' => Accreditation::factory()->create()->id,
            'type' => $this->faker->randomElement($this->model::typeList()),
            'aspect' => $this->faker->sentence(),
        ];
    }

    public function choice()
    {
        $aspect = InstrumentAspect::factory()->choice()->create();
        $points = InstrumentAspectPoint::factory()->count(5)->create([
            'instrument_aspect_id' => $aspect->id,
        ]);

        return $this->state(function (array $attributes) use ($aspect, $points) {
            $point = $this->faker->randomElement($points->toArray());
            return [
                'type' => $this->model::TYPE_CHOICE,
                'aspect' => $aspect->aspect,
                'aspectable_type' => InstrumentAspect::class,
                'aspectable_id' => $aspect->id,
                'instrument_aspect_point_id' => $point['id'],
                'main_component_id' => $aspect->instrumentComponent->ancestor()->id,
                'statement' => $point['statement'],
                'value' => $point['value'],
            ];
        });
    }

    public function proof()
    {
        $component = InstrumentComponent::factory()->create();

        return $this->state(function (array $attributes) use ($component) {
            $file = UploadedFile::fake()->create('file.pdf', 10);
            $filePath = Storage::fake('local')->putFile("accreditations", $file);

            return [
                'type' => $this->model::TYPE_PROOF,
                'aspect' => $component->name,
                'aspectable_type' => InstrumentComponent::class,
                'aspectable_id' => $component->id,
                'main_component_id' => $component->ancestor()->id,
                'file' => $filePath,
            ];
        });
    }
}
