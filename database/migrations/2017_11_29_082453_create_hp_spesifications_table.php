<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateHpSpesificationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('hp_spesipfications', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('product_id')->unsigned();
            $table->string('model');
            $table->string('fitur');
            $table->string('type_procesor');
            $table->string('screen_size');
            $table->string('screen_type');
            $table->string('video_resolution');
            $table->string('rear_camera');
            $table->string('front_camera');
            $table->string('condition');
            $table->string('battery_type');
            $table->string('network_connection');
            $table->string('slim_slot');
            $table->string('operating_system');
            $table->string('ram_memory');
            $table->string('resolution');
            $table->string('battery_capacity');
            $table->string('handphone_type');
            $table->string('hazmart');
            $table->timestamps();

            $table->foreign('product_id')->references('id')->on('products');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('hp_spesipfications');
    }
}
