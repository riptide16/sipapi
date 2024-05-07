<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PublicMenu extends Model
{
    use HasFactory;
    use Traits\HasUuidPrimaryKey;
    use Traits\Filterable;
    use Traits\Loggable;

    protected $fillable = [
        'name',
        'url',
        'parent_id',
        'order',
        'is_default',
        'page_id',
    ];

    protected $filterable = [
        'name',
        'url',
        'parent_id',
        'order',
    ];

    protected $casts = [
        'is_default' => 'bool',
    ];

    public function parent()
    {
        return $this->belongsTo(static::class, 'parent_id');
    }

    public function children()
    {
        return $this->hasMany(static::class, 'parent_id');
    }

    public function page()
    {
        return $this->belongsTo(Page::class);
    }

    public function scopeAncestors($query) {
        return $query->whereNull('parent_id');
    }

    public function scopeSort($query)
    {
        $query->orderBy('order', 'asc');
    }

    public function loadAllChildren()
    {
        $this->load(['children' => function ($query) {
            $query->sort();
        }]);
        foreach ($this->children as $children) {
            $children->loadAllChildren();
        }
    }

    public function syncUrl()
    {
        $url = '';
        if ($this->parent_id) {
            $url .= $this->parent->url;
        }
        if ($this->page_id) {
            if ($this->parent_id) {
                $url .= '/';
            }
            $url .= $this->page->slug;
        }

        $this->url = $url;
        $this->saveQuietly();

        foreach ($this->children as $children) {
            $children->syncUrl();
        }

        return $this;
    }
}
