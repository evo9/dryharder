<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddAutopayOrders extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('order_autopay', function(Blueprint $table)
		{
			$table->increments('id');
			$table->unsignedInteger('order_id')->default(0);
			$table->unsignedInteger('customer_id')->default(0);
			$table->double('amount', 10, 2)->default(0);
			$table->tinyInteger('state')->default(0);
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
		Schema::table('order_autopay', function(Blueprint $table)
		{
			$table->drop();
		});
	}

}
