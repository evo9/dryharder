<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CloudPaymentsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{

		Schema::create('payment_cloud', function(Blueprint $table){

			$table->increments('id');
			$table->string('guid', 50)->default(''); // код выгрузки

			$table->unsignedInteger('customer_id')->default(0); // contr_id // клиент
			$table->unsignedInteger('order_id')->default(0); // dor_id // заказ

			$table->double('amount', 8, 2)->default(0); // сумма оплаты

			$table->string('token', 500)->default(''); // токен

			$table->string('card_pan', 13)->default(''); // card_last_four // последние 4 цифры карты
			$table->string('card_type', 50)->default(''); // тип карты
			$table->string('card_holder', 50)->default(''); // user_name // имя владельца карты

			$table->string('ip', 30)->default(''); // ip_address
			$table->string('payment_id')->default(0); // transaction_id // id транзакции

			$table->string('email')->default(''); // email плательщика
			$table->boolean('exported')->default(0); // is_loaded // флаг успешного экспорта
			$table->string('request')->default(''); // query_string // данные запроса/
			$table->boolean('waiting')->default(1); // Ожидание подтверждения от системы оплаты. 1 ожидан6ие, 0 уже оплачено

			$table->timestamp('external_at'); // datetime_load // Дата создания по времени сервера оплаты
			$table->timestamp('exported_at'); // datetime_unloading // Дата выгрузки в БД агбис
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
		Schema::drop('payment_cloud');
	}

}
