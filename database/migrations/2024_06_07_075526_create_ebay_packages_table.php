<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateEbayPackagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ebay_packages', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('post_id')->unsigned();
            $table->foreign('post_id')->references('id')->on('posts')->onDelete('cascade')->onUpdate('cascade');
            $table->string('package_id');
            $table->string('pallet_id')->nullable();
            $table->string('itemSku')->nullable();
            $table->string('lineItemId')->nullable();
            $table->string('serviceName')->nullable();
            $table->string('itemUrl', 1000)->nullable();
            $table->string('title')->nullable();
            $table->integer('itemQuantity')->nullable();
            $table->text('description')->nullable();
            $table->double('length',8,2)->nullable();
            $table->double('width',8,2)->nullable();
            $table->double('height',8,2)->nullable();
            $table->double('weight',8,2)->nullable();
            $table->string('status')->nullable()->default('IS-01');
            $table->string('process_status')->nullable()->default('UnProcessed');
            $table->string('shipment_status')->nullable();
            $table->double('price',8,2)->nullable();
            $table->string('currency')->nullable();
            $table->string('hs_code')->nullable();
            $table->string('coo')->nullable();
            $table->string('category')->nullable();
            $table->string('sub_category_1')->nullable();
            $table->string('sub_category_2')->nullable();
            $table->string('sub_category_3')->nullable();
            $table->string('reason_of_return')->nullable();
            $table->string('condition')->nullable();
            $table->string('received_condition')->nullable();
            $table->string('inspection_status')->nullable();
            $table->text('comments')->nullable();
            $table->string('image_url', 500)->nullable();
            $table->text('imageUrls')->nullable();
            $table->string('discrepancy_status')->nullable();
            $table->string('item_date_type  ')->nullable();
            $table->date('discrepancy_date_in')->nullable();
            $table->date('discrepancy_date_out')->nullable();
            $table->date('item_date')->nullable();
            $table->text('package_data')->nullable();
            $table->string('oversize')->nullable();
            $table->string('dang_gud')->nullable();
            $table->string('measure_unit')->nullable();
            $table->string('inbound_dim')->nullable();
            $table->string('inspected_dim')->nullable();
            $table->string('empty_box')->nullable();
            $table->string('einr')->nullable();
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('ebay_packages');
    }
}
