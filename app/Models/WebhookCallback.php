<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WebhookCallback extends Model
{
    protected $guarded = [];
    protected $casts = ["payload" => "array"];
    use HasFactory;
}
