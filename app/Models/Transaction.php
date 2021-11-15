<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    use HasFactory;
    protected $guarded = [];
    protected $casts = [
        'transaction_payload' => 'array',
        'callback_payload' => 'array',
        "amount_received" => "float",
        "amount_paid" => "float",
    ];
    public function wallet()
    {
        return $this->belongsTo(Wallet::class);
    }

    public function getAmountReceivedAttribute($value)
    {
        switch (strtolower($this->wallet->coin->name)) {
            case 'bitcoin':
                $converted_value = $value/100000000;
                break;
            case 'ethereum':
                $converted_value = $value/1000000000000000000;
                break;

            default:
                $converted_value = $value;
                break;
        }
        return $converted_value;
    }
}
