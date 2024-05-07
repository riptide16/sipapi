<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Village extends Model
{
    use HasFactory;
    use Traits\HasUuidPrimaryKey;
    use Traits\Filterable;
    use Traits\Loggable;

    protected $fillable = [
        'name',
        'postal_code',
        'subdistrict_id',
    ];

    protected $filterable = [
        'name',
        'postal_code',
        'subdistrict_id',
    ];

    public function subdistrict()
    {
        return $this->belongsTo(Subdistrict::class);
    }
}
