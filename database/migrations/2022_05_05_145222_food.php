<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class Food extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
        Schema::create('food',function(Blueprint $table){
            $table->id();
            $table->string('restuarant');
            $table->string('item_name');
            $table->string('category');
            $table->string('what');
            $table->string('add_ons');
            $table->string('price');
            $table->string('picture');
            $table->string('availablilty');
            $table->string('discount');
            $table->string('transyear');
            $table->string('item_id');
            $table->string('details');
            $table->string('total_order');
            $table->string('favourite');
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
