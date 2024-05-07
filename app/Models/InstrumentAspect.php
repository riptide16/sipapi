<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class InstrumentAspect extends Model
{
    use HasFactory;
    use SoftDeletes;
    use Traits\HasUuidPrimaryKey;
    use Traits\Loggable;

    const TYPE_CHOICE = 'choice';
    const TYPE_PROOF = 'proof';
    const TYPE_MULTI_ASPECT = 'multi_aspect';

    protected $fillable = [
        'aspect',
        'type',
        'order',
        'instrument_id',
        'instrument_component_id',
        'parent_id',
    ];

    public static function typeList()
    {
        return [
            static::TYPE_CHOICE,
            static::TYPE_PROOF,
            static::TYPE_MULTI_ASPECT,
        ];
    }

    public function isChoice()
    {
        return $this->type === static::TYPE_CHOICE;
    }

    public function isMultiAspect()
    {
        return $this->type === static::TYPE_MULTI_ASPECT;
    }

    public function points()
    {
        return $this->hasMany(InstrumentAspectPoint::class)->orderBy('order', 'asc');
    }

    public function children()
    {
        return $this->hasMany(self::class, 'parent_id')->orderBy('order', 'asc');
    }

    public function parent()
    {
        return $this->belongsTo(self::class, 'parent_id');
    }

    public function instrument()
    {
        return $this->belongsTo(Instrument::class);
    }

    public function instrumentComponent()
    {
        return $this->belongsTo(InstrumentComponent::class);
    }

    public function accreditationContents()
    {
        return $this->morphMany(AccreditationContent::class, 'aspectable');
    }

    public function accreditationSimulationContents()
    {
        return $this->morphMany(AccreditationSimulationContent::class, 'aspectable');
    }

    public function scopeSort($query)
    {
        return $query->orderBy('order', 'asc');
    }
}
