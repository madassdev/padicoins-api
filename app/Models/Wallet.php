<?php

namespace App\Models;

use App\Services\Crypto;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Http;

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

    public function bankAccount()
    {
        return $this->belongsTo(BankAccount::class);
    }

    public function walletTransactions()
    {
        return $this->hasMany(WalletTransaction::class);
    }

    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }

    public function getBaseValue($value)
    {
        switch (strtolower($this->coin->name)) {
            case 'bitcoin':
                $value = $value/100000000;
                break;
            case 'ethereum':
                $value = $value/1000000000000000000;
                break;
        }
        return $value;
    }

    public function fetchState()
    {
        $crypto = new Crypto($this->coin);
        $state = $crypto;
        switch (strtolower($this->coin->name)) {
            case 'bitcoin':
                $state = $crypto->fetchBtcState($this->address);
                break;
            case 'ethereum':
                $state = $crypto->fetchEthState($this->address);
                break;
        }
        return $state;
    }

    public function getRate()
    {
        $rates = json_decode(Http::get('https://bitpay.com/api/rates'));
        $btc_rate = $rates[2]->rate;
        $eth_rate = $btc_rate/$rates[13]->rate;
        switch (strtolower($this->coin->name)) {
            case 'bitcoin':
                $rate = $btc_rate;
                break;
            case 'ethereum':
                $rate = $eth_rate;
                break;
        }
        return $rate;

    }

    

    public function saveState($state)
    {
        $transactions = collect($state->transactions)->take(10);
        $saved_transactions = $transactions->map(function ($t) {
            $transaction = WalletTransaction::firstOrNew(["hash" => $t['tx_hash']]);
            $transaction->wallet_id = $this->id;
            $transaction->type = $t['tx_input_n'] < 0 ? "input" : "output";
            if ($t['tx_input_n'] < 0) {
                // It's an input
                $transaction->type = "input";
                $transaction->amount_received = $t['value'];
                $transaction->amount_spent = 0;
            } else {
                $transaction->type = "output";
                $transaction->amount_spent = $t['value'];
                $transaction->amount_received = 0;
            }
            $transaction->transaction_payload = $t;
            $transaction->confirmations = $t['confirmations'];
            $transaction->confirmed_at = $t['confirmed'];
            $transaction->status = $t['confirmations'] ? 'confirmed' : 'unconfirmed';
            $transaction->save();
            return $transaction;
        });
        return $saved_transactions;
    }

    public function fetchTx($hash)
    {
        $crypto = new Crypto($this->coin);
        $state = $crypto->fetchBtcTx($hash);
        return $state;
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
