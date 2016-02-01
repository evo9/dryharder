<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class Flashes extends Migration
{

    public function up()
    {
        Schema::create('customer_flashes', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('customer_id');
            $table->unsignedInteger('flash_id');
            $table->unsignedInteger('qnt');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::drop('customer_flashes');
    }

}
