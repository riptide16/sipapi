<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InstitutionRequest extends Institution
{
    public const TYPE_CREATE = 'create';
    public const TYPE_UPDATE = 'update';

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
        'type',
    ];

    public function scopeUnvalidated($query)
    {
        return $query->whereNull('validated_at');
    }

    public function scopeTypeUpdate($query)
    {
        return $query->where('type', self::TYPE_UPDATE);
    }
}
