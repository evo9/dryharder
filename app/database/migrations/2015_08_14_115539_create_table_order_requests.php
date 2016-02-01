<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableOrderRequests extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('order_requests', function(Blueprint $table)
		{
			$table->increments('id');
			$table->timestamps();
            $table->char('phone', 10)->default('');
            $table->string('email')->default('');
            $table->string('address1')->default('');
            $table->string('address2')->default('');
            $table->string('orderText')->default('');
            $table->string('comment')->default('');
            $table->unsignedInteger('order_id')->default(0);
            $table->tinyInteger('state')->default(0);
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('order_requests', function(Blueprint $table)
		{
			$table->drop();
		});
	}

}
