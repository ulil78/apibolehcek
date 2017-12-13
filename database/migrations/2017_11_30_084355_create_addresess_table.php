<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAddresessTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('addresess', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id')->unsigned();
            $table->string('address_name');
            $table->string('name_receive');
            $table->text('address');
            $table->integer('cities_id')->unsigned();
            $table->integer('provices_id')->unsigned();
            $table->integer('disctrics_id')->unsigned();
            $table->string('postcode');
            $table->string('phone');
            $table->string('fax');
            $table->enum('default_address', ['true', 'false'])->default('false');
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('addresess');
    }
}
