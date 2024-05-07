<?php

namespace Database\Factories;

use App\Models\FileDownload;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Database\Eloquent\Factories\Factory;

class FileDownloadFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = FileDownload::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $today = today();
        $image = UploadedFile::fake()->image('image.jpg');
        $imagePath = Storage::fake('public')->putFile("files/{$today->format('Y')}/{$today->format('m')}", $image);
        return [
            'filename' => $this->faker->name(),
            'attachment' => $imagePath,
            'is_published' => false,
        ];
    }

    public function published()
    {
        return $this->state(function (array $attributes) {
            return [
                'is_published' => true,
            ];
        });
    }
}
