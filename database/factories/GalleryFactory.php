<?php

namespace Database\Factories;

use App\Models\Gallery;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class GalleryFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Gallery::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $today = today();
        $image = UploadedFile::fake()->image('image.jpg');
        $imagePath = Storage::fake('public')->putFile("galleries/{$today->format('Y')}/{$today->format('m')}", $image);

        return [
            'title' => $this->faker->text(10),
            'caption' => $this->faker->sentence(),
            'published_date' => $this->faker->dateTime(),
            'image' => $imagePath,
            'album_id' => (new GalleryAlbumFactory())->create()->id,
        ];
    }
}
