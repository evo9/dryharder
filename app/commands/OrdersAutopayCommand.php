<?php

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;
use Dryharder\Agbis\Api;
use Dryharder\Agbis\ApiException;
use Dryharder\Models\Customer;
use Dryharder\Gateway\Models\PaymentCloud;

class OrdersAutopayCommand extends Command {

	/**
	 * The console command name.
	 *
	 * @var string
	 */
	protected $name = 'dh:orders-autopay';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Command description.';

	/**
	 * Create a new command instance.
	 *
	 * @return void
	 */
	public function __construct()
	{
		parent::__construct();
	}

	/**
	 * Execute the console command.
	 *
	 * @return mixed
	 */
	public function fire()
	{
		try {
			$api = new Api();

			$customers = Customer::all();
			foreach ($customers as $customer) {
				$customerId = $customer->agbis_id;
				$card = PaymentCloud::getCustomerAutopayCard($customerId);
				if ($card) {
					$phone = '+7' . $customer->phone;
					$password = $customer->credential->agbis_password;
					$user = $api->Login_con($phone, $password);
					$key = $user->key;
					$orders = $api->Orders($key)['orders'];
					foreach ($orders as $order) {
						if ($this->isNotPaidOrder($customerId, $order['id'])) {
							if ($api->IsGoodOrder($order['id'], $customerId)) {
								$api->payByToken($order['id'], $card->token, $order['amount'], $order['doc_number'], $key);
							}
						}
					}
				}
			}

		} catch (ApiException $e) {

		}

	}

	private function isNotPaidOrder($customerId, $orderId)
	{
		$orders = PaymentCloud::getCustomersPaidOrders($customerId);

		if (!in_array($orderId, $orders)) {
			return true;
		}
		return false;
	}
}
