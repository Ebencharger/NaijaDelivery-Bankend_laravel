<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class Revenue extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
        Schema::create('revenue',function(Blueprint $table){
            $table->id();
            $table->integer('rest_id');
            $table->string('date');
            $table->string('time');
            $table->string('month');
            $table->string('year');
            $table->string('amount');
            $table->string('account_no')->nullable();
            $table->string('bank_name')->nullable();
            $table->string('transref')->nullable();
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
