<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class Transaction extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
        Schema::create('transaction', function(Blueprint $table){
            $table->id();
            $table->string('userid');
            $table->string('restid');
            $table->string('riderid');
            $table->string('riderarrive');
            $table->string('pickup');
            $table->string('productname');
            $table->string('productimage');
            $table->string('price');
            $table->string('quantity');
            $table->string('orderid');
            $table->string('amount');
            $table->integer('subtotal');
            $table->integer('deliveryfee');
            $table->integer('discount');
            $table->string('status')->nullable();
            $table->string('date');
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
