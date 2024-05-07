<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class News extends Model
{
    use HasFactory;
    use Traits\HasUuidPrimaryKey;
    use Traits\Loggable;

    protected $fillable = [
        'title',
        'body',
        'image',
        'published_date',
    ];

    protected $casts = [
        'published_date' => 'datetime',
    ];

    public function author()
    {
        return $this->belongsTo(User::class, 'author_id');
    }

    public function scopePublished($query)
    {
        return $query->whereNotNull('published_date')
                     ->whereDate('published_date', '<=', now());
    }
}
