<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Faq extends Model
{
    use HasFactory;
    use Traits\HasUuidPrimaryKey;
    use Traits\Filterable;
    use Traits\Loggable;

    protected $fillable = [
        'title',
        'content',
        'order',
    ];

    protected $filterable = [
        'title',
    ];

    public function scopeSort($query)
    {
        $query->orderBy('order', 'asc');
    }
}
