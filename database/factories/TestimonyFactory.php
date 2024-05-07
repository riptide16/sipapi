<?php

namespace Database\Factories;

use App\Models\Testimony;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Database\Eloquent\Factories\Factory;

class TestimonyFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Testimony::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $today = today();
        $image = UploadedFile::fake()->image('image.jpg');
        $imagePath = Storage::fake('public')->putFile("testimonies/{$today->format('Y')}/{$today->format('m')}", $image);
        return [
            'name' => $this->faker->name(),
            'content' => $this->faker->sentence(),
            'photo' => $imagePath,
        ];
    }
}
