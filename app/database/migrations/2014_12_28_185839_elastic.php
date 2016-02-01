<?php

use Illuminate\Database\Migrations\Migration;

class Elastic extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        /*$client = new Elasticsearch\Client();

        $params = [
            'index' => 'logger',
            'body'  => [
                'settings' => [
                    'number_of_shards'   => 1,
                    'number_of_replicas' => 0,
                ],
            ],
        ];


        $client->indices()->create($params);*/

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        /*$client = new Elasticsearch\Client();
        $client->indices()->delete('logger');
        $client->indices()->delete('reporter');*/
    }

}
