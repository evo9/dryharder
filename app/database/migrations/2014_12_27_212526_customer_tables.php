<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CustomerTables extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {

        Schema::create('promo_codes', function (Blueprint $table) {
            $table->increments('id');
            $table->string('code', 100)->default('');
            $table->boolean('enabled')->default(1);
            $table->boolean('deleted')->default(0);
            $table->timestamps();
        });

        Schema::create('addresses', function (Blueprint $table) {
            $table->increments('id');
            $table->string('address', 100)->default('');
            $table->boolean('enabled')->default(1);
            $table->boolean('deleted')->default(0);
            $table->timestamps();
        });

        Schema::create('promo_code_addresses', function (Blueprint $table) {
            $table->unsignedInteger('promo_code_id');
            $table->unsignedInteger('address_id');
            $table->unique(['promo_code_id', 'address_id']);
        });

        Schema::create('customer_addresses', function (Blueprint $table) {
            $table->unsignedInteger('customer_id');
            $table->unsignedInteger('address_id');
            $table->unique(['customer_id', 'address_id']);
        });

        Schema::create('customers', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('agbis_id')->default(0);
            $table->unsignedInteger('promo_code_id')->default(0);
            $table->string('email', 100)->default('');
            $table->string('name', 50)->default('');
            $table->string('phone', 12)->default('');
            $table->timestamps();
        });

        Schema::create('customer_credentials', function (Blueprint $table) {
            $table->increments('customer_id');
            $table->string('agbis_password', 100)->default('');
            $table->string('password', 100)->default('');
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

        Schema::drop('promo_codes');
        Schema::drop('addresses');
        Schema::drop('promo_code_addresses');
        Schema::drop('customer_addresses');
        Schema::drop('customers');
        Schema::drop('customer_credentials');

    }

}
