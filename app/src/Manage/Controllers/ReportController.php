<?php


namespace Dryharder\Manage\Controllers;
use Dryharder\Models\CustomerInviteExternal;
use Dryharder\Models\OrderReview;
use Elasticsearch\Client;
use Config;
use Input;
use Response;

class ReportController extends BaseController {


    public function index()
    {

        if (!$this->isAcceptedJson()) {
            return \View::make('man::reporter.index');
        }

        $matches = [];

        $date = Input::get('date', date('Y-m-d'));
        $matches['date'] = $date;

        $ip = Input::get('ip');
        if($ip){
            $matches['ip'] = $date;
        }

        $customer_id = Input::get('customer_id');
        if($customer_id){
            $matches['customer_id'] = $customer_id;
        }

        $prefix = Config::get('reports.prefix');
        if($prefix){
            $prefix .= '_';
        }

        $client = new Client();
        $results = $client->search([
            'index' => $prefix . 'reporter',
            'type'  => $prefix . 'reporter',
            'body'  => [
                'from' => 0,
                'size' => 100,
                'query' => [
                    'match' => $matches,
                ],
                'sort' => [
                    'time' => [
                        'order' => 'desc',
                    ]
                ]
            ],
        ]);

        return Response::json($results['hits']['hits']);

    }


    public function inviteStat()
    {
        if (!$this->isAcceptedJson()) {
            return \View::make('man::reporter.invite');
        }

        $list = CustomerInviteExternal::with('registered.customer')
            ->with('owner')
            ->orderBy('id', 'desc')
            ->get()
            ->all();

        foreach($list as $item){
            $item->registered &&
            $item->registered->customer &&
            $item->registered->customer->initExistsPaid();
        }

        return $list;

    }

    public function orderReviews()
    {
        if (!$this->isAcceptedJson()) {
            return \View::make('man::reporter.reviews');
        }

        $list = OrderReview::with('customer')
            ->orderBy('id', 'desc')
            ->get();

        return $list;

    }


}