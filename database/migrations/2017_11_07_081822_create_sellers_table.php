<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSellersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sellers', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name')->unique();
            $table->string('email')->unique();
            $table->string('password');
            $table->string('pic_name');
            $table->text('address');
            $table->integer('cities_id')->unsigned();
            $table->integer('provinces_id')->unsigned();
            $table->integer('disctrics_id')->unsigned();
            $table->string('postcode');
            $table->string('phone');
            $table->string('fax');
            $table->string('bank');
            $table->string('bank_account');
            $table->string('account_holder');
            $table->string('path1')->default('images/sellers/');
            $table->string('filename1')->default('noimages.png');
            $table->string('path2')->default('images/sellers/');
            $table->string('filename2')->default('noimages.png');
            $table->enum('type_seller', ['corporate', 'personal'])->default('corporate');
            $table->string('corporate_name');
            $table->text('corporate_address');
            $table->enum('status', ['true', 'false'])->default('true');
            $table->rememberToken();
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
        Schema::dropIfExists('sellers');
    }
}
