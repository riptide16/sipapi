<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\CanResetPassword As CanResetPasswordContract;
use Illuminate\Auth\Passwords\CanResetPassword;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\Eloquent\SoftDeletes;
use Laravel\Passport\HasApiTokens;
use App\Notifications\VerifyEmail;
use Hash;

class User extends Authenticatable implements
    MustVerifyEmail,
    CanResetPasswordContract
{
    public const STATUS_INACTIVE = 'inactive';
    public const STATUS_ACTIVE = 'active';
    public const STATUS_FAILED = 'failed';

    use HasFactory;
    use Notifiable;
    use HasApiTokens;
    use CanResetPassword;
    use SoftDeletes;
    use Traits\HasUuidPrimaryKey;
    use Traits\Filterable;
    use Traits\Loggable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'username',
        'email',
        'password',
        'role_id',
        'status',
        'region_id',
        'profile_picture',
        'phone_number',
        'institution_name',
        'province_id',
    ];

    protected $filterable = [
        'name',
        'username',
        'email',
        'role:display_name',
        'role:name',
        'region:name',
        'region_id',
        'province_id',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function setPasswordAttribute($value)
    {
        $this->attributes['password'] = Hash::make($value);
    }

    public function role()
    {
        return $this->belongsTo(Role::class);
    }

    public function institution()
    {
        return $this->belongsTo(Institution::class);
    }

    public function institutionRequests()
    {
        return $this->hasMany(InstitutionRequest::class);
    }

    public function region()
    {
        return $this->belongsTo(Region::class);
    }

    public function evaluations()
    {
        return $this->hasMany(Evaluation::class, 'assessor_id');
    }

    public function accreditations()
    {
        return $this->hasMany(Accreditation::class);
    }

    public function accreditationSimulations()
    {
        return $this->hasMany(AccreditationSimulation::class);
    }

    public function province()
    {
        return $this->belongsTo(Province::class);
    }

    public function evaluationAssignments()
    {
        return $this->belongsToMany(EvaluationAssignment::class, 'evaluation_assignment_user');
    }

    public function scopeAdmins($query)
    {
        return $query->whereHas('role', function ($role) {
            $role->admins();
        });
    }

    public function scopeAssessors($query)
    {
        return $query->whereHas('role', function ($role) {
            $role->assessor();
        });
    }

    public function scopeActive($query)
    {
        return $query->where('status', static::STATUS_ACTIVE);
    }

    public function scopeVerified($query)
    {
        return $query->whereNotNull('email_verified_at');
    }

    public function isSuperAdmin()
    {
        if (!$this->role) {
            return false;
        }

        return $this->role->isSuperAdmin();
    }

    public function isAdmin()
    {
        if (!$this->role) {
            return false;
        }

        return $this->role->isAdmin();
    }

    public function isProvince()
    {
        if (!$this->role) {
            return false;
        }

        return $this->role->isProvince();
    }

    public function isAssessor()
    {
        if (!$this->role) {
            return false;
        }

        return $this->role->isAssessor();
    }

    public function isAssessee()
    {
        if (!$this->role) {
            return false;
        }

        return $this->role->isAssessee();
    }

    public function isActive()
    {
        return $this->activated_at || $this->status === static::STATUS_ACTIVE;
    }

    public function isFailed()
    {
        return $this->status === static::STATUS_FAILED;
    }

    /**
     * Send the email verification notification.
     *
     * @return void
     */
    public function sendEmailVerificationNotification()
    {
        $this->notify(new VerifyEmail);
    }

    /**
     * Find the user instance for the given username.
     *
     * @param  string  $username
     * @return \App\Models\User
     */
    public function findForPassport($username)
    {
        $user = $this->where('email', $username)
                     ->orWhere('username', $username)
                     ->first();
        if (!$user) {
            return null;
        }

        // Always return if this is superadmin
        if ($user->isSuperAdmin()) {
            return $user;
        }

        // Must have verified email and active otherwise
        if ($user->isActive()) {
            return $user;
        }

        return null;
    }

    /**
     * Send the password reset notification.
     *
     * @param  string  $token
     * @return void
     */
    public function sendPasswordResetNotification($token)
    {
        $notification = new ResetPassword($token);
        $notification::$createUrlCallback = function ($notifiable, $token) {
            return config('services.frontend.reset_password_url')
                . '?'
                . http_build_query([
                    'token' => $token,
                    'email' => $notifiable->getEmailForPasswordReset(),
                ]);
        };
        $this->notify($notification);
    }

    public function ableTo($key)
    {
        if (!$this->role) {
            return false;
        }

        return $this->role->permissions()->where('key', $key)->exists();
    }

    public function canOrFail($key)
    {
        $can = $this->ableTo($key);
        if (!$can) {
            throw new AuthorizationException();
        }

        return $can;
    }

    public function canEvaluate($accreditationId)
    {
        $this->canOrFail('input_evaluations');

        return EvaluationAssignment::whereHas('assessors', function ($query) {
            $query->where('user_id', $this->id);
        })->where('accreditation_id', $accreditationId)->exists();
    }

    public function canUpdateCreateAccreditation()
    {
        if ($this->isAssessee() && optional($this->institution)->isValid()) {
            $accreditations = $this->accreditations()->orderBy('created_at', 'desc')->get();
            if ($accreditations->isEmpty()) {
                return true;
            } else {
                $accreditation = $accreditations->first();
                if ($accreditation->isIncomplete()) {
                    // Means updating
                    return true;
                }
                if ($accreditation->appealed_at) {
                    // Means appealing
                    return true;
                }
                if ($accreditation->isCertificateExpired() || $accreditation->isReaccreditationEligible()) {
                    // Means creating
                    return true;
                }
            }
        }

        return false;
    }

    public static function statusList()
    {
        return [
            static::STATUS_INACTIVE,
            static::STATUS_ACTIVE,
            static::STATUS_FAILED,
        ];
    }
}
