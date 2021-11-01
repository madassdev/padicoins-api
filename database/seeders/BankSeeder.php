<?php

namespace Database\Seeders;

use App\Models\Bank;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Http;

class BankSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //
        $banks = Http::get('https://merchant.birrionapi.com/api/get-banks')->json()['data'];
        foreach ($banks as $bank) {

            Bank::updateOrCreate(['code' => $bank['code']], ['name' => $bank['name'], 'code' => $bank['code']]);
        }
        return Bank::all();
    }
}
