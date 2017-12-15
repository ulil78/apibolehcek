<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateHotListsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('hot_lists', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->string('path')->default('images/hots/');
            $table->string('filename')->default('noimages.png');
            $table->string('url');
            $table->enum('status', ['true', 'false'])->default('true');
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
        Schema::dropIfExists('hot_lists');
    }
}
