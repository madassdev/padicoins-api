<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateWalletsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('wallets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained();
            $table->foreignId('bank_account_id')->constrained();
            $table->decimal('balance', 20, 10)->default(0);

            $table->foreignId('coin_id');
            $table->string('coin_symbol');
            $table->string('track_id');
            $table->string('provider');

            $table->text('address');
            $table->text('private_key');
            $table->text('public_key');
            $table->text('wif')->nullable();
            $table->text('payload')->nullable();
            $table->text('webhook_url')->nullable();
            $table->string('status')->default('active');
            $table->text('encryption_key')->nullable();
            $table->string('encryption_protocol')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('wallets');
    }
}
