<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateOrderItemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('order_items', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('order_data_id')->nullable();
            $table->foreign('order_data_id')->references('id')->on('order_data')->onDelete('cascade')->onUpdate('cascade');
            $table->string('order_id')->nullable();
            $table->string('item_id')->nullable();
            $table->string('sku')->nullable();
            $table->string('name')->nullable();
            $table->string('price')->nullable();
            $table->string('size')->nullable();
            $table->float('tax_amount')->nullable();
            $table->float('shipping_amount')->nullable();
            $table->float('discount_amount')->nullable();
            $table->string('discount_type')->nullable();
            $table->double('total',10,3)->nullable();
            $table->string('ordered_qty')->nullable();
            $table->integer('confirm_qty')->default(0);
            $table->string('order_status')->default('CREATED');
            $table->boolean('returnable')->default(True);
            $table->string('hs_code')->nullable();
            $table->string('country_of_origin')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('order_items');
    }
}
