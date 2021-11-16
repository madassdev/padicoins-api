<?php

namespace Database\Seeders;

use App\Models\AppConfig;
use App\Models\User;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        // \App\Models\User::factory(10)->create();

        $roles = ["admin", "user"];
        collect($roles)->map(function($role){
            $user = User::create(['name' => $role, "email" => $role."@padicoins.com", 'password' => bcrypt('password'), 'mobile' => '08000000000']);
            $r = Role::insert(['name' => $role, 'guard_name' => 'web']);
            $user->assignRole($role);
        });
        $faves = User::create(['name' => 'Favour', "email" => "favescsskr@gmail.com", 'password' => bcrypt('f6a1v22o15'), 'mobile' => '08136051712']);
        $faves->assignRole('admin');
        $configs = [
            [
                'key' => 'paystack_secret_key_test',
                'value' => 'sk_test_cc8f2f5a5dc2b79a638e93b3d42306a53fb13f3d',
            ],
            [
                'key' => 'paystack_public_key_test',
                'value' => 'pk_test_deca92ff9fd72063fce8ed64c97007ed4e0d34e8',
            ],
        ];

        collect($configs)->map(function ($config) {
            return AppConfig::updateOrCreate(['key' => $config['key']], $config);
        });
        $this->call(CoinSeeder::class);
        $this->call(BankSeeder::class);
    }
}
