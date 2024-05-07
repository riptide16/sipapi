<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GalleryAlbum extends Model
{
    use HasFactory;
    use Traits\HasUuidPrimaryKey;

    protected $fillable = [
        'name',
        'slug'
    ];

    public function galleries()
    {
        return $this->hasMany(Gallery::class, 'album_id');
    }
}
