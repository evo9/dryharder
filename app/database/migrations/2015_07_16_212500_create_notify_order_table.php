<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateNotifyOrderTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('notify_orders', function(Blueprint $table)
		{
			$table->increments('id');
			$table->unsignedInteger('customer_id');
			$table->unsignedInteger('order_id');
			$table->boolean('sent');
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
		Schema::table('notify_orders', function(Blueprint $table)
		{
			$table->drop();
		});
	}

}
