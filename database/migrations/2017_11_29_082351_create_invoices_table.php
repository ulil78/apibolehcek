<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateInvoicesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('invoices', function (Blueprint $table) {
            $table->increments('id');
            $table->string('order_id');
            $table->integer('seller_id')->unsigned();
            $table->integer('user_id')->unsigned();
            $table->datetime('due_date');
            $table->float('sub_total', 0, 2);
            $table->integer('total_qty');
            $table->integer('weight_total');
            $table->float('total_discount', 0, 2);
            $table->integer('coupons_id')->unsigned();
            $table->float('coupon_disc', 0, 2);
            $table->float('total_amount', 0, 2);
            $table->enum('status', ['unpaid', 'paid', 'deliver', 'finish', 'cancel'])->default('unpaid');
            $table->text('notice');
            $table->timestamps();

             $table->foreign('seller_id')->references('id')->on('sellers');
             $table->foreign('user_id')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('invoices');
    }
}
