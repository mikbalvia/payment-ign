<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePaymentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('order_id');
            $table->bigInteger('user_id');
            $table->float('amount');
            $table->string('status')->nullable()->comment = '1: success, 2:pending, 3:failed';
            $table->string('status_code')->nullable();
            $table->string('status_message')->nullable();
            $table->string('transaction_status')->nullable();
            $table->string('payment_type')->nullable();
            $table->string('bank')->nullable();
            $table->string('currency')->nullable();
            $table->string('transaction_id')->nullable();
            $table->bigInteger('product_id')->nullable();
            $table->dateTime('transaction_time')->nullable();
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
        Schema::dropIfExists('payments');
    }
}
