<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    use HasFactory;
    protected $guarded = [];
    protected $casts = ['transaction_payload' => 'array', 'callback_payload' => 'array'];
    public function wallet()
    {
        return $this->belongsTo(Wallet::class);
    }

}
