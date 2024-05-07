<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class City extends Model
{
    use HasFactory;
    use Traits\HasUuidPrimaryKey;
    use Traits\Filterable;
    use Traits\Loggable;

    protected $fillable = [
        'name',
        'type',
        'province_id',
    ];

    protected $filterable = [
        'name',
        'province_id',
    ];

    public const TYPE_KOTA = 'Kota';
    public const TYPE_KABUPATEN = 'Kabupaten';

    public static function typeList()
    {
        return [
            static::TYPE_KOTA,
            static::TYPE_KABUPATEN,
        ];
    }

    public function province()
    {
        return $this->belongsTo(Province::class);
    }
}
