<?php


use Dryharder\Agbis\Api;
use Dryharder\Agbis\ApiException;
use Dryharder\Components\Reporter;
use Dryharder\Models\Customer;
use Illuminate\Console\Command;

class CustomersUpdaterCommand extends Command
{

    protected $name = 'dh:customers-updater';
    protected $description = "Customer updater command";

    public function fire()
    {
        $api = new Api();

        $list = Customer::whereEmail('')->get()->all();
        $this->line('Найдено клиентов без email: ' . count($list));
        foreach ($list as $customer) {

            $phone = '+7' . $customer->phone;
            $password = $customer->credential->agbis_password;

            try {
                $auth = $api->Login_con($phone, $password);
                $key = $auth->key;
                $this->update($key, $api, $customer);
            } catch (ApiException $e) {
                $this->error('Ошибка авторизации в Агбис: ' . $e->getMessage());
            }

        }


    }

    /**
     * @param string   $key
     * @param Api      $api
     * @param Customer $customer
     *
     * @throws ApiException
     * @throws Exception
     */
    public function update($key, $api, $customer)
    {

        $this->info('начинаем сбор информации о клиенте: ' . $customer->id . ' [' . $customer->agbis_id . ']');
        Reporter::aggregateExternalInfoStart($key, $customer->agbis_id, $customer->id);

        $client = $api->ContrInfo($key);
        $this->line('... общая информация');
        $client['key'] = $key;

        try {
            $promo = $api->PromoCodeUse($key);
            $this->line('... промокод');
        } catch (ApiException $e) {
            if ($e->isDataError()) {
                $promo = null;
            } else {
                throw $e;
            }
        }

        $client['promo'] = $promo;
        $client['bonus'] = $api->Bonus($key)['bonus'];
        $this->line('... бонус');
        $client['deposit'] = $api->Deposit($key)['deposit'];
        $this->line('... депозитный счет');
        $client['orders'] = $api->Orders($key)['orders'];
        $this->line('... заказы');
        $client['history'] = $api->OrdersHistory($key)['orders'];
        $this->line('... история заказов');
        $client['tokens'] = $api->TokenPayList($key)['tokens'];
        $this->line('... токены платежей');

        Reporter::aggregateExternalInfoEnd($customer->id);

        $component = \Dryharder\Components\Customer::instance()->initByExternalId($client['id']);

        $this->line('... обновляем информацию в нашей базе данных');
        $component->updateExternalInfo($client);
        $this->info('закончили работу с клиентом');

    }

}