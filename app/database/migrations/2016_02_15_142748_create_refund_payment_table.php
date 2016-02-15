<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRefundPaymentTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('refund_payment', function(Blueprint $table) {
			$table->increments('id');
			$table->string('transaction_id')->default(0); // транзакция операции
			$table->string('payment_id')->default(0); // транзакция возращаемого платежа
			$table->double('amount', 8, 2)->default(0); // сумма
			$table->unsignedInteger('customer_id')->default(0); // клиент
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
		Schema::drop('refund_payment');
	}

}
