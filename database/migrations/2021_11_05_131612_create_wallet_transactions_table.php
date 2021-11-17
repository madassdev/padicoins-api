<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateWalletTransactionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('wallet_transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('wallet_id');
            $table->text('hash');
            $table->string('type')->default('input');
            $table->decimal('amount_received', 40, 10)->default(0);
            $table->decimal('amount_spent', 40, 10)->default(0);
            $table->decimal('amount_in_usd', 15, 4)->default(0);
            $table->decimal('amount_in_ngn', 15, 4)->default(0);
            $table->text('callback_payload')->nullable();
            $table->text('transaction_payload')->nullable();

            // $table->string('payment_status')->default('pending')->change();
            // $table->string('payment_status')->default('pending');
            // $table->boolean('complete')->default(false);

            $table->decimal('amount_paid', 15, 4)->default(0);
            $table->integer('confirmations')->default(0);
            $table->string('confirmed_at')->nullable();
            $table->string('status')->default('unconfirmed');
            $table->timestamp('paid_at', 6)->nullable();
            $table->timestamp('received_at', 6)->nullable();
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
