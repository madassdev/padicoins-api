<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAppExceptionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('app_exceptions', function (Blueprint $table) {
            $table->id();
            $table->text('message');
            $table->text('trace')->nullable();
            $table->text('request');
            $table->boolean('reported')->default(false);
            $table->text('report_recipients')->nullable();
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
        Schema::dropIfExists('app_exceptions');
    }
}
