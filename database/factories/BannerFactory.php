<?php

namespace Database\Factories;

use App\Models\Banner;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class BannerFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Banner::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $today = today();
        $image = UploadedFile::fake()->image('image.jpg');
        $imagePath = Storage::fake('public')->putFile("banners/{$today->format('Y')}/{$today->format('m')}", $image);

        return [
            'name' => $this->faker->text(10),
            'order' => $this->faker->numberBetween(1, 20),
            'is_active' => $this->faker->boolean(),
            'url' => $this->faker->url(),
            'image' => $imagePath,
        ];
    }

    public function active()
    {
        return $this->state(function (array $attributes) {
            return [
                'is_active' => true,
            ];
        });
    }

    public function inactive()
    {
        return $this->state(function (array $attributes) {
            return [
                'is_active' => false,
            ];
        });
    }
}
