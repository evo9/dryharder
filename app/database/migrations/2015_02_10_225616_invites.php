<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class Invites extends Migration {

	public function up()
	{

		Schema::table('customers', function(Blueprint $table){
			$table->string('invite', 6)->default('');
			$table->timestamp('auth_at')->default('0000-00-00 00:00:00');
		});

		Schema::create('customer_invites', function(Blueprint $table){

			$table->increments('id');
			$table->unsignedInteger('customer_id')->default(0);
			$table->unsignedInteger('owner_id')->default(0);
			$table->unsignedInteger('source_id')->default(0);
			$table->timestamps();

			$table->unique(['customer_id','owner_id'], 'customers');

		});

	}

	public function down()
	{

		Schema::table('customers', function(Blueprint $table){
			$table->dropColumn('invite');
			$table->dropColumn('auth_at');
		});

		Schema::dropIfExists('customer_invites');

	}

}
