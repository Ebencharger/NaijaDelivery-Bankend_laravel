<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class Restuarant extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
        Schema::create('restuarant',function(Blueprint $table){
            $table->id();
            $table->string('name');
            $table->string('location');
            $table->string('email');
            $table->string('address')->nullable();
            $table->string('phone');
            $table->string('wallpaper')->nullable();
            $table->string('status');
            $table->string('instagram');
            $table->string('regulation')->nullable();
            $table->string('package');
            $table->string('visible');
            $table->string('password');
            $table->string('picture')->nullable();
            $table->string('admin')->nullable();
            $table->string('followers')->nullable();
            $table->string('reviews')->nullable();
            $table->string('motto')->nullable();
            $table->string('transyear')->nullable();
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
