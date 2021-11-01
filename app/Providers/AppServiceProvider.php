<?php

namespace App\Providers;

use App\Models\AppConfig;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        URL::forceScheme('https');
        Schema::defaultStringLength(191);
        // mock_buy(env("APP_ENV") === "local");
        // dd(isMock());
        try {
            //code...
            $sc = AppConfig::whereActive(true)->pluck('value', 'key');
            // dd($sc);
            config()->set(['app_config' => $sc]);
        } catch (\Throwable $th) {
            //throw $th;
        }
    }
}
