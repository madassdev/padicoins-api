<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTransactionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id');
            $table->foreignId('wallet_id');
            $table->string('reference');
            $table->text('hash')->nullable();
            $table->string('type')->default('deposit');
            $table->decimal('amount_received', 40, 10)->default(0);
            $table->string('currency_received')->default('BTC');
            $table->decimal('amount_paid', 40, 10)->default(0);
            $table->string('currency_paid')->nullable();
            $table->string('status')->default('pending');
            $table->boolean('complete')->default(false);
            $table->timestamp('completed_at', 6)->nullable();
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
        Schema::dropIfExists('transactions');
    }
}
