<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Province extends Model
{
    use HasFactory;
    use Traits\HasUuidPrimaryKey;
    use Traits\Filterable;
    use Traits\Loggable;

    protected $fillable = [
        'name',
    ];

    protected $filterable = [
        'name',
        'regions:id',
    ];

    public function regions()
    {
        return $this->belongsToMany(Region::class);
    }
}
