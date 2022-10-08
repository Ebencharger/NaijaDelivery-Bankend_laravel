<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class Order extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
        Schema::create('order', function(Blueprint $table){
            $table->id();
            $table->string('userid');
            $table->string('customerid');
            $table->string('restid');
            $table->string('date');
            $table->string('time');
            $table->string('ordernum');
            $table->string('productweight');
            $table->string('producttype');
            $table->string('servicefee');
            $table->string('bookingtype');
            $table->string('note');
            $table->string('distance');
            $table->string('pickup');
            $table->string('pickuptime');
            $table->string('status');
            $table->string('deliverto');
            $table->string('year');
            $table->string('delivertime')->nullable();
            $table->string('transactionDate');
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
        //
    }
}
