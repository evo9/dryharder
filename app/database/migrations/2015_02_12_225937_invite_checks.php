<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class InviteChecks extends Migration {

	public function up()
	{

		Schema::table('customer_invites', function(Blueprint $table){
			$table->unsignedInteger('customer_invite_external_id')->default(0);
			$table->boolean('bonus')->default(0);
		});

	}

	public function down()
	{

		Schema::table('customer_invites', function(Blueprint $table){
			$table->dropColumn('customer_invite_external_id');
			$table->dropColumn('bonus');
		});

	}

}
