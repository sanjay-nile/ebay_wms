<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateOrderCarrierDataTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('order_carrier_data', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('parcel_code');
            $table->string('order_ref');
            $table->string('system_correlation_code')->nullable();
            $table->string('custom1')->nullable();
            $table->string('carrier_consignment_code')->nullable();
            $table->string('carrier_code')->nullable();
            $table->string('postcode')->nullable();
            $table->string('transaction_type_id')->nullable();
            $table->string('destination_country_code')->nullable();
            $table->string('system_code')->nullable();
            $table->string('retailer_id')->nullable();
            $table->string('retailer_key')->nullable();
            $table->string('metapack_carrier_service_code')->nullable();
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
        Schema::dropIfExists('order_carrier_data');
    }
}
