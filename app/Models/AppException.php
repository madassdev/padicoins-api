<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AppException extends Model
{
    use HasFactory;
    protected $guarded = [];
    protected $casts = [
        'trace' => 'array',
        'recipients' => 'array',
        'request' => 'array',
    ];
}
