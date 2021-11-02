<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ReceiptUpdatesToOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('orders', function (Blueprint $table) {
            //
            $table->timestamp('received_at', 6)->nullable();
            $table->decimal('amount_received', 11, 8)->default(0);
            $table->decimal('equivalent_usd', 11, 8)->default(0);
            $table->decimal('equivalent_ngn', 11, 8)->default(0);
            $table->text('callback_data')->nullable();
            $table->timestamp('paid_at', 6)->nullable();
            $table->decimal('amount_paid', 11, 8)->default(0);
            $table->string('currency_paid')->nullable();
            $table->boolean('complete')->default(false);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn('received_at');
            $table->dropColumn('amount_received');
            $table->dropColumn('equivalent_usd');
            $table->dropColumn('equivalent_ngn');
            $table->dropColumn('callback_data');
            $table->dropColumn('paid_at');
            $table->dropColumn('amount_paid');
            $table->dropColumn('currency_paid');
            $table->dropColumn('complete');
        });
    }
}
