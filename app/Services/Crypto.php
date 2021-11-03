<?php

namespace App\Services;

use App\Exceptions\ProductionActionUnavailableException;
use App\Models\Coin;
use App\Models\Order;
use Carbon\Carbon;

class Crypto
{
    public $coin;

    public function __construct(Coin $coin)
    {
        $this->coin = $coin->name;
    }

    public function makeTransaction($wallet_address)
    {
        // hash, address, confirmations, timestamp, value (in satoshis),
        $this->wallet_address = $wallet_address;
        if (mock()) {
            $this->success = true;
            $this->amount_in_satoshi = rand(10000000, 30000000);
            $this->amount_in_btc = $this->amount_in_satoshi / 1000000;
            $this->usd_per_btc = 63036.71;
            $this->ngn_per_btc = 551;
            $this->amount_in_usd = $this->convertBtcToUsd($this->amount_in_btc);
            $this->amount_in_ngn = $this->convertUsdToNgn($this->amount_in_usd);
            return $this;
        }
        throw new ProductionActionUnavailableException("Not Available on production", "400");
    }

    public function createWallet($track_id)
    {
        if (mock()) {
            // https://api.blockchain.info/v2/receive?xpub=$xpub&callback=$callback_url&key=$key&gap_limit=$gap_limit
            $this->wallet_request_data = null;
            $this->wallet_address = "random_wallet_address_goes_here_$track_id";
            $this->callback_url =  route('orders.callback', ['track_id' => $track_id]);
            return $this;
        }
        throw new ProductionActionUnavailableException("Not Available on production", "400");
    }

    public function convertBtcToUsd($amount_in_btc)
    {
        // Fetch current USD/BTC price.
        $rate = $this->usd_per_btc;
        $usd_value = $rate * $amount_in_btc;
        return round($usd_value, 2);
    }

    public function convertUsdToNgn($amount_in_usd)
    {
        // Fetch current NGN/USD price.
        $rate = $this->ngn_per_btc;
        $ngn_value = $rate * $amount_in_usd;
        return round($ngn_value, 2);
    }
}
