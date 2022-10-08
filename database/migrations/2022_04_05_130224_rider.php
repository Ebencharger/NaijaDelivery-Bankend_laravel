<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class Rider extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
        Schema::create('rider',function(Blueprint $table){
            $table->id();
            $table->string('name');
            $table->string('location');
            $table->string('email')->unique();
            $table->string('password');
            $table->string('address');
            $table->string('phone');
            $table->string('token')->nullable();
            $table->string('picture')->nullable();
            $table->integer('balance')->nullable();
            $table->string('status')->nullable();
            $table->string('company')->nullable();
            $table->string('transactionDate');
            $table->string('lasttime')->nullable();
            $table->string('lastdate')->nullable();
            $table->string('lastseen')->nullable();
            $table->string('available')->nullable();
            $table->string('delivertime')->nullable();
            $table->string('bio')->nullable();
            $table->string('why');
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
