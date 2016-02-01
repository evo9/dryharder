<?php

use Illuminate\Database\Migrations\Migration;

class FixCloudRequestLog extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		DB::connection()->statement(
			'
			ALTER TABLE `payment_cloud`
			CHANGE `request`
				`request`
				VARCHAR( 2000 )
				CHARACTER SET utf8 COLLATE utf8_unicode_ci
				NOT NULL
				DEFAULT ""
			'
		);
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		DB::connection()->statement(
			'
			ALTER TABLE `payment_cloud`
			CHANGE `request`
				`request`
				VARCHAR( 255 )
				CHARACTER SET utf8 COLLATE utf8_unicode_ci
				NOT NULL
				DEFAULT ""
			'
		);
	}

}
