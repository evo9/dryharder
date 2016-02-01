<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class Services extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('service_titles', function(Blueprint $table){
			$table->increments('id');
			$table->unsignedInteger('agbis_id')->default(0);
			$table->string('code', 10)->default('');
			$table->string('name', 100)->default('');
			$table->text('lang')->default('');
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
		Schema::drop('service_titles');
	}

}
