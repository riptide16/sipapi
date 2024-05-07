<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Institution extends Model
{
    use HasFactory;
    use Traits\HasUuidPrimaryKey;
    use Traits\HasCategory;
    use Traits\Loggable;
    use Traits\Filterable;

    public const STATUS_INVALID = 'tidak_valid';
    public const STATUS_VALID = 'valid';
    public const STATUS_AWAITING_VERIFICATION = 'menunggu_verifikasi';

    protected $fillable = [
        'category',
        'region_id',
        'library_name',
        'npp',
        'agency_name',
        'typology',
        'address',
        'province_id',
        'city_id',
        'subdistrict_id',
        'village_id',
        'institution_head_name',
        'email',
        'telephone_number',
        'mobile_number',
        'library_head_name',
        'library_worker_name',
        'registration_form_file',
        'title_count',
        'status',
        'last_predicate',
        'last_certification_date',
        'user_id',
        'validated_at',
        'predicate',
        'accredited_at',
        'accreditation_expires_at',
    ];

    protected $filterable = [
        'category',
        'created_at',
        'updated_at',
        'status',
    ];

    protected $casts = [
        'library_worker_name' => 'array',
    ];

    public static function typologyList()
    {
        return ['A', 'B', 'C'];
    }

    public static function predicateList()
    {
        return ['A', 'B', 'C', 'Tidak Akreditasi'];
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function region()
    {
        return $this->belongsTo(Region::class);
    }

    public function province()
    {
        return $this->belongsTo(Province::class);
    }

    public function city()
    {
        return $this->belongsTo(City::class);
    }

    public function subdistrict()
    {
        return $this->belongsTo(Subdistrict::class);
    }

    public function village()
    {
        return $this->belongsTo(Village::class);
    }

    public function requests()
    {
        return $this->hasMany(InstitutionRequest::class, 'institution_id');
    }

    public function scopeInvalid($query)
    {
        return $query->where('status', self::STATUS_INVALID);
    }

    public function isValid()
    {
        return ($this->status === static::STATUS_VALID) || (bool) $this->validated_at;
    }

    public function setValid()
    {
        $this->status = static::STATUS_VALID;
        $this->validated_at = now();

        return $this;
    }

    public function setInvalid()
    {
        $this->status = static::STATUS_INVALID;
        $this->validated_at = now();

        return $this;
    }
}
