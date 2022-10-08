<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class Holding extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
        Schema::create('holding', function(Blueprint $table){
        $table->id();
        $table->string('userid');
        $table->string('ordernum');
        $table->string('card_no');
        $table->string('ccExpiryMonth');
        $table->string('ccExpiryYear');
        $table->string('cvvNumber');
        $table->string('amount');
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
