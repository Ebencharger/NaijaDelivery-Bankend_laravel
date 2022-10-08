<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class Estimated extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
        Schema::create('estimated',function(Blueprint $table){
            $table->id();
            $table->integer('rest_id');
            $table->string('exp_menu');
            $table->string('exp_revenue');
            $table->string('exp_customer');
            $table->string('exp_order');
            $table->string('year');
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
