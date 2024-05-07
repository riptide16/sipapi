<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Infographic extends Model
{
    use HasFactory;
    use Traits\HasUuidPrimaryKey;

    protected $fillable = [
        'province_name',
        'province_code'
    ];
}
