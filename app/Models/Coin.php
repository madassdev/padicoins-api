<?php

namespace App\Models;

use App\Services\Crypto;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Coin extends Model
{
    use HasFactory, SoftDeletes;
    protected $guarded = [];
    protected $casts = [
        "active" => 'boolean'
    ];
    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    public function wallets()
    {
        return $this->hasMany(Wallet::class);
    }

    public function createBitcoinWallet(User $user)
    {
        $track_id = generate_track_id();
        $crypto = new Crypto($this);
        $btc_wallet = $crypto->createBitcoinWallet($track_id);
        $wallet = $this->wallets()->create([
            "user_id" => $user->id,
            'track_id' => $track_id,
            'provider' => $btc_wallet->provider ?? 'TestServer',
            'coin_symbol' => $this->symbol,
            'address' => $btc_wallet->wallet_address,
            'private_key' => $btc_wallet->private_key,
            'public_key' => $btc_wallet->public_key,
            'wif' => $btc_wallet->wif ?? "N/A",
            'payload' => $btc_wallet,
            'webhook_url' => $btc_wallet->webhook_url,
        ]);

        $wallet->refresh();
        return $wallet;
    }

    public function createWallet(User $user)
    {
        switch (strtolower($this->name)) {
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
