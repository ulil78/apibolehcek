<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCatLevelTwosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cat_level_twos', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('cat_level_one_id')->unsigned();
            $table->string('name');
            $table->string('slug_name');
            $table->string('path')->default('images/categories/');
            $table->string('filename')->default('noimages.png');
            $table->text('description');
            $table->text('tags');
            $table->enum('frontend', ['true', 'false'])->default('false');
            $table->enum('status', ['true', 'false'])->default('true');
            $table->timestamps();

            $table->foreign('cat_level_one_id')->references('id')->on('cat_level_ones')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('cat_level_twos');
    }
}
