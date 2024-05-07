<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Video extends Model
{
    use HasFactory;
    use Traits\HasUuidPrimaryKey;
    use Traits\Loggable;

    protected $fillable = [
        'title',
        'youtube_id',
        'description',
        'is_homepage'
    ];

    public function youtubeUrl()
    {
        return 'https://www.youtube.com/watch?v=' . $this->youtube_id;
    }
}
