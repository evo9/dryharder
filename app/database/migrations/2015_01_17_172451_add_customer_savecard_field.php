<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddCustomerSavecardField extends Migration {

	public function up()
	{
		Schema::table('customers', function (Blueprint $table) {
			$table->boolean('save_card')->default(1);
		});
	}

	public function down()
	{
		Schema::table('customers', function (Blueprint $table) {
			$table->dropForeign('save_card');
		});
	}

}
