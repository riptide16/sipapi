<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Region extends Model
{
    use HasFactory;
    use SoftDeletes;
    use Traits\HasUuidPrimaryKey;
    use Traits\Filterable;
    use Traits\Loggable;

    protected $fillable = [
        'name',
    ];

    protected $filterable = [
        'name',
    ];

    public function provinces()
    {
        return $this->belongsToMany(Province::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
