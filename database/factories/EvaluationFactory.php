<?php

namespace Database\Factories;

use App\Models\Accreditation;
use App\Models\Evaluation;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class EvaluationFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Evaluation::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $accreditation = Accreditation::factory()->create();
        $assessor = User::factory()->assessor()->create();

        return [
            'accreditation_id' => $accreditation->id,
            'institution_id' => $accreditation->institution_id,
            'assessor_id' => $assessor->id,
        ];
    }

    public function withDocument()
    {
        return $this->state(function (array $attributes) {
            $doc = UploadedFile::fake()->create('document.pdf', 10);
            $docPath = Storage::fake('local')->putFile("evaluations", $doc);

            return [
                'document_file' => $docPath,
            ];
        });
    }
}
