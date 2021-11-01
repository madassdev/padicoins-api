<?php

namespace Database\Seeders;

use App\Models\Bank;
use Illuminate\Database\Seeder;

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
        $banks = nuban();


        foreach ($banks as $code => $bank) {

            Bank::updateOrCreate(['code' => $code], ['name' => $bank, 'code' => $code]);
        }
    }
}
