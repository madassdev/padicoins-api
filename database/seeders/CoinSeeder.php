<?php

namespace Database\Seeders;

use App\Models\Coin;
use Illuminate\Database\Seeder;

class CoinSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //

        $coins = [
            [
                "name" => "Bitcoin",
                "symbol" => "BTC",
                "type" => "btc",
                "title" => "Bitcoin (BTC)"
            ],
            [
                "name" => "Ethereum",
                "symbol" => "ETH",
                "type" => "erc20",
                "title" => "Ethereum (ETH)"
            ],
            [
                "name" => "USDT_TRC20",
                "symbol" => "USDT_TRC20",
                "type" => "USDT_TRC20",
                "title" => "USD Tether (USDT_TRC20)"
            ],
            [
                "name" => "BSC",
                "symbol" => "BSC",
                "type" => "BSC",
                "title" => "Binance Coin (BNB_BSC)"
            ],
        ];

        Coin::insert($coins);
    }
}
