<?php


use Illuminate\Console\Command;

class ExampleCommand extends Command
{

    protected $name = 'dh:example';
    protected $description = "Example";

    public function fire()
    {
        $this->elastic();
    }

    private function elastic()
    {

        $client = new Elasticsearch\Client();
        $results = $client->search([
            'index' => 'reporter',
            'type'  => 'reporter',
            'size' => 1,
            'body'  => [
                'query' => [
                    'match_all' => [
                        //'ip' => '10.0.2.2',
                        //'customer_id' => '100197',
                    ],
                ],
                'sort' => [
                    'time' => [
                        'order' => 'asc',
                    ]
                ]
            ],
        ]);


        print_r($results);


    }

}