<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    use HasFactory;
    use Traits\HasUuidPrimaryKey;

    protected $casts = [
        'data' => 'array',
    ];

    public function notifiable()
    {
        return $this->morphTo();
    }

    public function scopeByUserId($query, $userId)
    {
        $query->where('notifiable_type', User::class)
              ->where('notifiable_id', $userId);
    }

    public function scopeSort($query)
    {
        $query->orderBy('created_at', 'desc');
    }

    public function scopeUnread($query)
    {
        $query->whereNull('read_at');
    }
}
