<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class Subscriptions extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('subscriptions', function(Blueprint $t){
            $t->unsignedInteger('id');
            $t->unsignedInteger('customer_id')->default(0);
            $t->unsignedInteger('price')->default(0);
            $t->unsignedInteger('order_id')->default(0);
            $t->string('name')->default('');
            $t->string('description')->default('');
            $t->timestamps();
        });
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
        Schema::dropIfExists('subscriptions');
	}

}
