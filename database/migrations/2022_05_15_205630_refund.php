<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class Refund extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
        Schema::create('refund',function(Blueprint $table){
            $table->id();
            $table->integer('user_id');
            $table->integer('restid');
            $table->integer('orderid');
            $table->string('date');
            $table->string('time');
            $table->string('transyear');
            $table->string('amount');
            $table->string('description');
            $table->string('status');
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
