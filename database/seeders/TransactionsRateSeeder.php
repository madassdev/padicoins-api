<?php

namespace Database\Seeders;

use App\Models\Transaction;
use Illuminate\Database\Seeder;

class TransactionsRateSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //
        Transaction::latest()->get()->map(function ($t) {
            $wallet = $t->wallet;
            $usd_to_ngn_rate = config('app_config')['usd_to_ngn_rate'];
            $coin_to_usd_rate = $wallet->getRate();
            $usd_value = round($coin_to_usd_rate * $t->amount_received, 2);
            $ngn_value = $usd_value * $usd_to_ngn_rate;
            $t->update(
                [
                    "coin_to_usd_rate" => $coin_to_usd_rate,
                    "usd_to_ngn_rate" => $usd_to_ngn_rate,
                    "usd_value" => ($usd_value),
                    "ngn_value" => round($ngn_value),
                ]
            );
        });
    }
}
