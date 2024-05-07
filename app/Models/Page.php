<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Page extends Model
{
    use HasFactory;
    use Traits\HasUuidPrimaryKey;
    use Traits\Filterable;
    use Traits\Loggable;

    protected $fillable = [
        'name',
        'slug',
        'body',
        'is_published',
    ];

    protected $filterable = [
        'name',
        'slug',
        'is_published',
    ];

    protected $casts = [
        'is_published' => 'boolean',
    ];

    public function setSlugAttribute($value)
    {
        $this->attributes['slug'] = strtolower($value);
    }

    public function scopePublished($query)
    {
        return $query->where('is_published', true);
    }

    public function publicMenus()
    {
        return $this->hasMany(PublicMenu::class, 'url', 'slug');
    }

    public function alternativeSlug()
    {
        $iteration = 1;
        $slug = $this->slug . '-' . $iteration;

        if (preg_match('/-(?P<i>\d+)$/', $this->slug, $result)) {
            $iteration = ++$result['i'];
            $slug = preg_replace('/-\d+$/', '-' . $iteration, $this->slug);
        }

        return $slug;
    }
}
