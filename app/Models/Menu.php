<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Menu extends Model
{
    use HasFactory;
    use Traits\HasUuidPrimaryKey;

    public function permissions()
    {
        return $this->hasMany(Permission::class);
    }

    public function scopeSort($query)
    {
        return $query->orderBy('order', 'asc');
    }
}
