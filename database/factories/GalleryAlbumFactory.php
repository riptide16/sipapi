<?php

namespace Database\Factories;

use App\Models\GalleryAlbum;
use Illuminate\Database\Eloquent\Factories\Factory;

class GalleryAlbumFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = GalleryAlbum::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'name' => $this->faker->unique()->text(10),
        ];
    }
}
