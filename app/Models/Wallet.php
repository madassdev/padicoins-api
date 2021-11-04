<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Wallet extends Model
{
    use HasFactory;
    protected $guarded = [];

    protected $casts = [
        "payload" => "array"
    ];

    protected $hidden = ["payload", "encryption_key", "encryption_protocol"];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function coin()
    {
        return $this->belongsTo(Coin::class);
    }

    public function createBitcoinWallet(User $user)
    {
        $this->coin_id = $this->coin->id;
        $this->coin_symbol = $this->coin->symbol;
        $this->track_id = generate_track_id();
        $this->user = $user;
        return $this;
    }
    public function createForUser(User $user, Coin $coin)
    {
        $this->coin = $coin;
        switch (strtolower($coin->name)) {
            case 'bitcoin':
                $wallet = $this->createBitcoinWallet($user);
                break;

            default:
                # code...
                break;
        }
        return $wallet;
    }
}
