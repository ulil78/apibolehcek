<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateShippingCitiesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('shipping_cities', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('shipping_province_id')->unsigned();
            $table->string('name');
            $table->timestamps();

            $table->foreign('shipping_province_id')->references('id')->on('shipping_provinces')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('shipping_cities');
    }
}
