<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddFailed2cloud extends Migration
{

    public function up()
    {
        Schema::table('payment_cloud', function (Blueprint $table) {
            $table->boolean('failed')->default(0);
        });
    }

    public function down()
    {
        Schema::table('payment_cloud', function (Blueprint $table) {
            $table->dropColumn('failed');
        });
    }

}
