<?php

namespace App\Observers;

use App\Models\Wallet;

class WalletObserver
{
    //
    public function creating(Wallet $wallet)
    {
        $wallet->public_key = encrypt($wallet->public_key);
        $wallet->private_key = encrypt($wallet->private_key);
        if ($wallet->wif) {
            $wallet->wif = encrypt($wallet->wif);
        }
        $wallet->encryption_protocol = "Laravel OpenSSL and the AES-256-CBC cipher";
        $wallet->encryption_key = env('APP_KEY');
    }
}
