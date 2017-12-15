<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBannerBigsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('banner_bigs', function (Blueprint $table) {
          $table->increments('id');
          $table->integer('cat_level_one_id')->unsigned();
          $table->string('name');
          $table->string('header');
          $table->string('path')->default('images/banners/');
          $table->string('filename')->default('noimages.png');
          $table->string('url');
          $table->enum('status', ['true', 'false'])->default('true');
          $table->timestamps();

          $table->foreign('cat_level_one_id')->references('id')->on('cat_level_ones');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('banner_bigs');
    }
}
