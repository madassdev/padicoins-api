<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Order extends Model
{
    use HasFactory, SoftDeletes;
    protected $casts = [
        'api_data' => 'array',
        'callback_data' => 'array',
        'transaction_data' => 'array',
        'amount_received' => 'float',
        'amount_in_ngn' => 'float',
        'amount_in_usd' => 'float',
        'amount_paid' => 'float',
        'complete' => 'bool',
        'paid_at' => 'date',
        'received_at' => 'date',
];
    protected $guarded = [];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function bankAccount()
    {
        return $this->belongsTo(BankAccount::class);
    }

    public function coin()
    {
        return $this->belongsTo(Coin::class);
    }

    public function markAsComplete()
    {
        $this->complete = true;
        $this->save();
    }
    public function wallet()
    {
        return $this->belongsTo(Wallet::class);
    }
}
