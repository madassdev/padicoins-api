<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Order extends Model
{
    use HasFactory, SoftDeletes;
    protected $casts = ['api_data' => 'array'];
    protected $hidden = ["api_data"];
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
}
