<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCloudPaymentsCard extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('cloud_payments_card', function(Blueprint $table) {
			$table->increments('id');
			$table->unsignedInteger('customer_id')->default(0); // клиент
			$table->double('amount', 8, 2)->default(0); // сумма
			$table->string('token', 500)->default(''); // токен
			$table->string('card_pan', 20)->default(''); // номер карты
			$table->string('card_type', 50)->default(''); // тип карты
			$table->string('card_holder', 255)->default(''); // имя владельца карты
			$table->string('ip', 20)->default(''); // ip адрес
			$table->string('payment_id')->default(0); // transaction_id // id транзакции
			$table->string('email', 255)->default(''); // email плательщика
			$table->boolean('is_default')->default(0); // карта по умолчанию
			$table->timestamp('external_at'); // datetime_load // Дата создания по времени сервера оплаты
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
		Schema::drop('cloud_payments_card');
	}

}
