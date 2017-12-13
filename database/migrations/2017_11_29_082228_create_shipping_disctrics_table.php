<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateShippingDisctricsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('shipping_districs', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('shipping_city_id')->unsigned();
            $table->string('name');
            $table->timestamps();

            $table->foreign('shipping_city_id')->references('id')->on('shipping_cities')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('shipping_districs');
    }
}
