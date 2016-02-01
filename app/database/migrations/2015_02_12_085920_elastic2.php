<?php

use Illuminate\Database\Migrations\Migration;

class Elastic2 extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {

        // убрать после деплоя
        return;

        $mapping = [
            'properties' => [
                'ip'          => [
                    'type' => 'string',
                ],
                'sid'         => [
                    'type' => 'string',
                ],
                'customer_id' => [
                    'type' => 'integer',
                ],
                'dt'          => [
                    'type' => 'string',
                ],
                'date'        => [
                    'type' => 'string',
                ],
                'time'        => [
                    'type' => 'string',
                ],
                'info'        => [
                    'type'  => 'nested',
                    'index' => 'not_analyzed',
                ],
            ],
        ];

        $prefix = \Config::get('reports.prefix');
        if($prefix){
            $prefix .= '_';
        }

        $client = new Elasticsearch\Client();

        // для боя
        $params = [
            'index' => $prefix . 'reporter',
            'body'  => [
                'settings' => [
                    'number_of_shards'   => 1,
                    'number_of_replicas' => 0,
                ],
                'mappings' => [
                    'reporter' => $mapping,
                ],
            ],
        ];
        $client->indices()->create($params);


    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {

        $prefix = \Config::get('reports.prefix');
        if($prefix){
            $prefix .= '_';
        }

        $client = new Elasticsearch\Client();
        $client->indices()->delete(['index' => $prefix . 'reporter']);
    }

}
