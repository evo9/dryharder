<?php


namespace Dryharder\Components;


use Dryharder\Agbis\Api;

class OrderServiceComponent
{

    public function parseOrderService($id, $sid = null)
    {

        $api = new Api();
        $clothes = $api->FullService($id, $sid);
        $result = [];

        foreach ($clothes as $item) {

            $description = [
                'name'           => $item->service,
                'service'        => $item->service,
                'dirty_name'     => $item->dirty_name,
                'status_name'    => $item->status_name,
                'price'          => $item->price,
                'amount'         => $item->price,
                'nurseries_name' => $item->nurseries_name,
                'group_tov'      => $item->group_tov,
                'qnt'            => $item->qty,
            ];
            $properties = [];

            foreach ($item->addons as $addon) {

                if (is_numeric($addon->aos_value) && strlen($addon->aos_value) > 0 && $addon->aos_value == 0) {
                    continue;

                } elseif (is_numeric($addon->aos_value) && $addon->aos_value == 1) {

                    $properties[] = [
                        'title' => $addon->descr,
                        'value' => null,
                    ];

                } elseif (!empty($addon->aos_value)) {

                    $properties[] = [
                        'title' => $addon->descr,
                        'value' => $addon->aos_value,
                    ];
                }

                $description['properties'] = $properties;
            }

            $result[] = $description;

        }

        return $result;

    }

}