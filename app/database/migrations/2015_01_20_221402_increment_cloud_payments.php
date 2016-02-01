<?php

use Illuminate\Database\Migrations\Migration;

class IncrementCloudPayments extends Migration {

	public function up()
	{
		DB::connection()->statement(
			"ALTER TABLE `payment_cloud` AUTO_INCREMENT =500"
		);
	}

	public function down()
	{
		//
	}

}
