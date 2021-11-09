<?php

namespace App\Services;

use App\Exceptions\ProductionActionUnavailableException;
use App\Models\Coin;
use App\Models\Order;
use Carbon\Carbon;
use Illuminate\Support\Facades\Http;

class Crypto
{
    public $coin;
    public $provider = "blockcypher";

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
            $this->amount_in_satoshi = rand(50, 3000);
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
            $this->webhook_url =  route('orders.callback', ['track_id' => $track_id]);
            return $this;
        }
        throw new ProductionActionUnavailableException("Not Available on production", "400");
    }

    public function fetchBtcState($address)
    {
        $this->wallet_address = $address;
        $this->provider = cfg('btc_webhook_provider') ?? 'blockcypher';
        $url = "https://api.blockcypher.com/v1/btc/main/addrs/$address";
        $response = Http::get($url)->json();
        $this->address_data = $response;
        $this->transactions = @$response['txrefs'];
        return $this;
    }

    public function fetchBtcTx($hash)
    {
        $url = "https://api.blockcypher.com/v1/btc/main/txs/$hash";
        $response = Http::get($url)->json();
        $this->tx = $response;
        // $this->transactions = $response['txrefs'];
        return $this;
    }

    public function createBitcoinWallet($track_id = null)
    {
        $this->provider = cfg('btc_webhook_provider') ?? 'blockcypher';

        // Prepare address request
        $url = "https://api.blockcypher.com/v1/btc/main/addrs";
        $response = Http::post($url)->json();
        $this->wallet_address = $response['address'];
        $this->private_key = encrypt($response['private']);
        $this->public_key = encrypt($response['public']);
        $this->wif = encrypt($response['wif']);

        // Prepare webhook request for address.
        $this->webhook_url =  route('wallets.callback', ['track_id' => $track_id, 'webhook_provider' => $this->provider]);
        $wh_url = "https://api.blockcypher.com/v1/btc/main/hooks";
        $wh_data = [
            'event' => 'confirmed-tx',
            'address' => $this->wallet_address,
            'url' => $this->webhook_url,
            'token' => config('blockcypher.token')
        ];

        $wh_response = Http::post($wh_url, $wh_data)->json();
        $this->webhook_request_data = $wh_response;
        
        return $this;


        if (mock()) {
            // https://api.blockchain.info/v2/receive?xpub=$xpub&callback=$callback_url&key=$key&gap_limit=$gap_limit
            $this->wallet_address = "random_wallet_address_goes_here_$track_id";
            $this->private_key = "random_private_key$track_id";
            $this->public_key = "random_public_key$track_id";
            $this->wif = "random_wif$track_id";
            $this->webhook_url =  route('orders.callback', ['track_id' => $track_id, 'webhook_provider' => $this->provider]);
            return $this;
        }
        throw new ProductionActionUnavailableException("Not Available on production", "400");
    }
    public function createEthWallet($track_id = null)
    {
        $this->provider = cfg('btc_webhook_provider') ?? 'blockcypher';

        // Prepare address request
        $url = "https://api.blockcypher.com/v1/eth/main/addrs";
        $response = Http::post($url)->json();
        $this->wallet_address = $response['address'];
        $this->private_key = encrypt($response['private']);
        $this->public_key = encrypt($response['public']);
        $this->wif = encrypt(@$response['wif']);
        
        // Prepare webhook request for address.
        $this->webhook_url =  route('wallets.callback', ['track_id' => $track_id, 'webhook_provider' => $this->provider]);
        $wh_url = "https://api.blockcypher.com/v1/eth/main/hooks";
        $wh_data = [
            'event' => 'confirmed-tx',
            'address' => $this->wallet_address,
            'url' => $this->webhook_url,
            'token' => config('blockcypher.token')
        ];

        $wh_response = Http::post($wh_url, $wh_data)->json();
        $this->webhook_request_data = $wh_response;
        
        return $this;


        if (mock()) {
            // https://api.blockchain.info/v2/receive?xpub=$xpub&callback=$callback_url&key=$key&gap_limit=$gap_limit
            $this->wallet_address = "random_wallet_address_goes_here_$track_id";
            $this->private_key = "random_private_key$track_id";
            $this->public_key = "random_public_key$track_id";
            $this->wif = "random_wif$track_id";
            $this->webhook_url =  route('wallets.callback', ['track_id' => $track_id, 'webhook_provider' => $this->provider]);
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
