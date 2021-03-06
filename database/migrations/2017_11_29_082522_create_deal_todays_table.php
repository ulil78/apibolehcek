<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDealTodaysTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('deal_todays', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->string('slug_name');
            $table->string('path')->default('images/deals');
            $table->string('filename')->default('noimages.png');
            $table->datetime('end_date');
            $table->text('description');
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
        Schema::dropIfExists('deal_todays');
    }
}
