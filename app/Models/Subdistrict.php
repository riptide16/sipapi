<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Subdistrict extends Model
{
    use HasFactory;
    use Traits\HasUuidPrimaryKey;
    use Traits\Filterable;
    use Traits\Loggable;

    protected $fillable = [
        'name',
        'city_id',
    ];

    protected $filterable = [
        'name',
        'city_id',
    ];

    public function city()
    {
        return $this->belongsTo(City::class);
    }
}
