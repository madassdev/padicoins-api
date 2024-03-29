<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    use HasFactory;
    protected $guarded = [];
    protected $casts = [
        "amount_paid" => "float",
        "amount_received" => "decimal: 10",
        "coin_to_usd_rate" => "float",
        "usd_value" => "float",
        "usd_to_ngn_rate" => "float",
        "ngn_value" => "float",
    ];

    public function wallet()
    {
        return $this->belongsTo(Wallet::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
