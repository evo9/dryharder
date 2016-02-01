<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class Contents extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('content_blocks', function(Blueprint $table){

			$table->increments('id');

			$table->string('code', 50)->default('')->unique();

			$table->text('comment')->nullable(true);
			$table->text('lang')->nullable(true);

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
		Schema::drop('content_blocks');
	}

}
