<?php

namespace Database\Seeders;

use App\Models\Province;
use App\Models\City;
use App\Models\Subdistrict;
use App\Models\Village;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Cache;

class IndonesiaRegionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Cache::flush();
        $handle = fopen(dirname(__FILE__) . '/files/regions.csv', 'r');
        if (!$handle) {
            throw new \Exception('CSV File doesn\'t exist!');
        }

        $row = 1;
        while (($values = fgetcsv($handle, 200, ',')) !== false) {
            // Skip headers
            if ($row++ === 1) {
                continue;
            }

            // Index list:
            // 0 => No; 1 => Province; 2 => City Type; 3 => City
            // 4 => Subdistrict; 5 => Village; 6 => Postal Code
            $provinceId = $this->saveProvince($values[1]);
            $cityId = $this->saveCity($values[3], $values[2], $provinceId);
            $subdistrictId = $this->saveSubdistrict($values[4], $cityId);
            $villageId = $this->saveVillage($values[5], $values[6], $subdistrictId);
        }

        fclose($handle);
        Cache::flush();
    }

    private function saveProvince($name)
    {
        return Cache::remember("province_{$name}", 3600, function () use ($name) {
            $province = Province::firstOrCreate([
                'name' => $name,
            ]);
            return $province->id;
        });
    }

    private function saveCity($name, $type, $provinceId)
    {
        return Cache::remember(
            "city_{$name}_{$type}_{$provinceId}",
            3600,
            function () use ($name, $type, $provinceId) {
                $city = City::firstOrCreate([
                    'name' => $name,
                    'type' => $type,
                    'province_id' => $provinceId,
                ]);
                return $city->id;
            }
        );
    }

    private function saveSubdistrict($name, $cityId)
    {
        return Cache::remember(
            "subdistrict_{$name}_{$cityId}",
            3600,
            function () use ($name, $cityId) {
                $subdistrict = Subdistrict::firstOrCreate([
                    'name' => $name,
                    'city_id' => $cityId,
                ]);
                return $subdistrict->id;
            }
        );
    }

    private function saveVillage($name, $postal, $subdistrictId)
    {
        $village = Village::firstOrCreate([
            'name' => $name,
            'subdistrict_id' => $subdistrictId,
            'postal_code' => $postal,
        ]);
        return $village->id;
    }
}
