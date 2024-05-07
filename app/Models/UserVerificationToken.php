<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserVerificationToken extends Model
{
    use HasFactory;

    protected $primaryKey = 'token';

    protected $fillable = [
        'user_id',
        'expires_at',
    ];

    protected $casts = [
        'expires_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Bootstrap the model and its traits.
     *
     * @return void
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (!$model->getAttribute($model->getKeyName())) {
                do {
                    $token = static::generateToken();
                } while (static::where($model->getKeyName(), $token)->exists());

                $model->setAttribute($model->getKeyName(), $token);
            }
        });
    }

    /**
     * Generate random 64-length of hexadecimal characters token
     * 
     * @return string
     */
    protected static function generateToken()
    {
        return bin2hex(random_bytes(32));
    }

    /**
     * Get the auto-incrementing key type.
     *
     * @return string
     */
    public function getKeyType()
    {
        return 'string';
    }

    /**
     * Get the value indicating whether the IDs are incrementing.
     *
     * @return bool
     */
    public function getIncrementing()
    {
        return false;
    }

    public function isValid()
    {
        return $this->expires_at->isFuture();
    }
}
