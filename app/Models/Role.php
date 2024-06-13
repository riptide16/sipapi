<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Role extends Model
{
    public const SUPER_ADMIN = 'super_admin';
    public const CERTIFICATE_ADMIN = 'sertifikat';
    public const ADMIN = 'admin';
    public const ASSESSOR = 'asesor';
    public const ASSESSEE = 'asesi';
    public const PROVINCE = 'provinsi';

    use HasFactory;
    use Traits\HasUuidPrimaryKey;
    use Traits\Loggable;
    use SoftDeletes;

    protected $fillable = [
        'display_name',
    ];

    public function permissions()
    {
        return $this->belongsToMany(Permission::class);
    }

    public function scopeAdmins($query)
    {
        return $query->where('name', static::SUPER_ADMIN)
                     ->orWhere('name', static::ADMIN);
    }

    public function scopeAssessee($query)
    {
        return $query->where('name', static::ASSESSEE);
    }

    public function scopeAssessor($query)
    {
        return $query->where('name', static::ASSESSOR);
    }

    public function isSuperAdmin()
    {
        return $this->name === static::SUPER_ADMIN;
    }

    public function isCertificateAdmin()
    {
        return $this->name === static::CERTIFICATE_ADMIN;
    }
    
    public function isAdmin()
    {
        return ($this->name === static::SUPER_ADMIN) || ($this->name === static::ADMIN);
    }

    public function isAssessor()
    {
        return $this->name === static::ASSESSOR;
    }

    public function isAssessee()
    {
        return $this->name === static::ASSESSEE;
    }

    public function isProvince()
    {
        return $this->name === static::PROVINCE;
    }
}
