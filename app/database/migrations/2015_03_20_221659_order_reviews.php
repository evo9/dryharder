<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class OrderReviews extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('order_reviews', function(Blueprint $t){
            $t->increments('id');
            $t->unsignedInteger('customer_id')->default(0);
            $t->unsignedInteger('order_id')->default(0);
            $t->tinyInteger('stars')->unsigned();
            $t->string('doc_number')->default('');
            $t->text('text');
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
		Schema::dropIfExists('order_reviews');
	}

}
