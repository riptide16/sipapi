<?php

namespace Database\Factories;

use App\Models\News;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class NewsFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = News::class;

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
            'title' => $this->faker->text(20),
            'body' => $this->faker->sentence(),
            'image' => $imagePath,
            'published_date' => $this->faker->dateTime(),
            'author_id' => (new UserFactory())->superAdmin()->create()->id,
        ];
    }

    public function unpublished()
    {
        return $this->state(function (array $attributes) {
            return [
                'published_date' => $this->faker->dateTimeBetween('tomorrow', 'next week'),
            ];
        });
    }
}
