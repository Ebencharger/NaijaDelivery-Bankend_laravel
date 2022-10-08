<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class Customers extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
        Schema::create('customers',function(Blueprint $table){
            $table->id();
            $table->string('name');
            $table->string('location');
            $table->string('email')->unique();
            $table->string('password')->nullable();
            $table->string('address');
            $table->string('phone');
            $table->string('picture')->nullable();
            $table->string('status')->nullable();
            $table->string('transactionDate')->nullable();
            $table->string('lasttime')->nullable();
            $table->string('lastdate')->nullable();
            $table->string('lastseen')->nullable();
            $table->string('available')->nullable();
            $table->string('bio')->nullable();
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
