<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class Offer extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
        Schema::create('offer',function(Blueprint $table){
            $table->id();
            $table->integer('restid');
            $table->string('category');
            $table->string('name');
            $table->string('free_package');
            $table->string('amount');
            $table->string('meat');
            $table->string('what');
            $table->string('picture');
            $table->string('reviews');
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
