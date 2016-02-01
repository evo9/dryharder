<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class InvitesExternals extends Migration {

	public function up()
	{
		Schema::create('customer_invite_externals', function(Blueprint $table){

			$table->increments('id');
			$table->string('phone', 12)->default('');
			$table->unsignedInteger('owner_id')->default(0);
			$table->unsignedInteger('source_id')->default(0);
			$table->timestamps();

			$table->unique(['phone','owner_id'], 'customers');

		});
	}

	public function down()
	{
		Schema::dropIfExists('customer_invite_externals');
	}

}
