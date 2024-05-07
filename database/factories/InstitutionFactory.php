<?php

namespace Database\Factories;

use App\Models\Institution;
use App\Models\User;
use App\Models\Region;
use App\Models\Province;
use App\Models\City;
use App\Models\Subdistrict;
use App\Models\Village;
use Illuminate\Database\Eloquent\Factories\Factory;

class InstitutionFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Institution::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'user_id' => User::factory()->assessee()->create()->id,
        ];
    }

    public function filled()
    {
        return $this->state(function (array $attributes) {
            return [
                'category' => $this->faker->randomElement($this->model::categoryList()),
                'region_id' => Region::factory()->create()->id,
                'library_name' => $this->faker->name,
                'npp' => (string) $this->faker->randomNumber(5),
                'agency_name' => $this->faker->name,
                'typology' => $this->faker->randomElement($this->model::typologyList()),
                'address' => $this->faker->address,
                'province_id' => Province::factory()->create()->id,
                'city_id' => City::factory()->create()->id,
                'subdistrict_id' => Subdistrict::factory()->create()->id,
                'village_id' => Village::factory()->create()->id,
                'institution_head_name' => $this->faker->name,
                'email' => $this->faker->email,
                'telephone_number' => $this->faker->phoneNumber,
                'mobile_number' => $this->faker->phoneNumber,
                'library_head_name' => $this->faker->name,
                'library_worker_name' => $this->faker->name,
                'title_count' => $this->faker->randomNumber(3),
            ];
        });
    }

    public function valid()
    {
        return $this->state(function (array $attributes) {
            return [
                'validated_at' => now(),
                'status' => $this->model::STATUS_VALID,
            ];
        });
    }
}
