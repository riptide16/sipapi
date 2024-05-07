<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FileDownload extends Model
{
    use HasFactory;
    use Traits\HasUuidPrimaryKey;
    use Traits\Filterable;
    use Traits\Loggable;

    protected $fillable = [
        'filename',
        'attachment',
        'is_published',
    ];

    protected $filterable = [
        'filename',
    ];

    protected $casts = [
        'is_published' => 'boolean',
        'is_preset' => 'boolean',
    ];

    public function scopePublished($query)
    {
        $query->where('is_published', true);
    }
}
