<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Instrument extends Model
{
    use Traits\HasUuidPrimaryKey;
    use Traits\HasCategory;
    use Traits\Loggable;

    protected $fillable = [
        'category',
    ];

    public function aspects()
    {
        return $this->hasMany(InstrumentAspect::class);
    }

    public function components()
    {
        return $this->hasMany(InstrumentComponent::class, 'category', 'category')->where('type', 'main');
    }
}
