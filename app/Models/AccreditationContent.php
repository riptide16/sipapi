<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AccreditationContent extends Model
{
    use HasFactory;
    use Traits\HasUuidPrimaryKey;
    use Traits\Loggable;

    public const TYPE_CHOICE = 'choice';
    public const TYPE_PROOF = 'proof';
    public const TYPE_VIDEO = 'video';
    public const TYPE_GDRIVE = 'gdrive';

    protected $fillable = [
        'accreditation_id',
        'aspectable_type',
        'aspectable_id',
        'main_component_id',
        'instrument_aspect_point_id',
        'type',
        'aspect',
        'statement',
        'value',
        'file',
        'url',
    ];

    public static function typeList()
    {
        return [
            static::TYPE_CHOICE,
            static::TYPE_PROOF,
            static::TYPE_VIDEO,
	    static::TYPE_GDRIVE,
        ];
    }

    public function mainComponent()
    {
        return $this->belongsTo(InstrumentComponent::class, 'main_component_id');
    }

    public function instrumentAspectPoint()
    {
        return $this->belongsTo(InstrumentAspectPoint::class);
    }

    public function aspectable()
    {
        return $this->morphTo();
    }

    public function evaluationContent()
    {
        return $this->hasOne(EvaluationContent::class);
    }

    public function accreditation()
    {
        return $this->belongsTo(Accreditation::class);
    }

    public function filename()
    {
        if ($this->type == 'proof') {
            $filename = "{$this->accreditation->code} {$this->accreditation->institution->agency_name} {$this->aspect}";
            $explode = explode('.', $this->file);
            $extension = $explode[count($explode) - 1];
            return substr(preg_replace('/[^ \w]/', '-', $filename), 0, 100) . '.' . $extension;
        } else {
            return null;
        }
    }
}
