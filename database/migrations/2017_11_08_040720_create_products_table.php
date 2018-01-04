<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('products', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('seller_id')->unsigned();
            $table->foreign('seller_id')->references('id')->on('sellers');
            $table->string('name');
            $table->string('slug_name');
            $table->integer('brand_id')->unsigned();
            $table->foreign('brand_id')->references('id')->on('brands');
            $table->integer('cat_level_one_id')->unsigned();
            $table->foreign('cat_level_one_id')->references('id')->on('cat_level_ones');
            $table->integer('cat_level_two_id')->unsigned();
            $table->foreign('cat_level_two_id')->references('id')->on('cat_level_twos');
            $table->integer('cat_level_three_id')->unsigned();
            $table->foreign('cat_level_three_id')->references('id')->on('cat_level_threes');
            $table->integer('price');
            $table->integer('stock');
            $table->integer('discount');
            $table->integer('weight');
            $table->integer('minimum_stock')->default('2');
            $table->enum('preorder',['true', 'false'])->default('false');
            $table->enum('insurance',['true', 'false'])->default('false');
            $table->enum('share',['true', 'false'])->default('false');
            $table->integer('racks_id')->unsigned();
            $table->integer('racks_id')->references('id')->on('racks');
            $table->integer('view');
            $table->integer('sold');
            $table->text('highlight');
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
        Schema::dropIfExists('products');
    }
}
