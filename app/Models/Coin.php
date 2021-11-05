<?php

namespace App\Models;

use App\Exceptions\ProductionActionUnavailableException;
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

    public function createWallet(User $user)
    {
        $track_id = generate_track_id();
        $crypto = new Crypto($this);

        switch (strtolower($this->name)) {
            case 'bitcoin':
                $wallet = $crypto->createBitcoinWallet($track_id);
                break;
            case 'ethereum':
                $wallet = $crypto->createEthWallet($track_id);
                break;

            default:
                throw new ProductionActionUnavailableException("This Coin's implemantation is currently unavailable. Please try for BTC and ETH", "400");

                break;
        }

        // return $wallet;

        $created_wallet = $this->wallets()->create([
            "user_id" => $user->id,
            'track_id' => $track_id,
            'provider' => $wallet->provider ?? 'TestServer',
            'coin_symbol' => $this->symbol,
            'address' => $wallet->wallet_address,
            'private_key' => decrypt($wallet->private_key),
            'public_key' => decrypt($wallet->public_key),
            'wif' => $wallet->wif ? decrypt($wallet->wif) : "N/A",
            'payload' => $wallet,
            'webhook_url' => $wallet->webhook_url,
        ]);

        $created_wallet->refresh();


        return $created_wallet;
    }
}
