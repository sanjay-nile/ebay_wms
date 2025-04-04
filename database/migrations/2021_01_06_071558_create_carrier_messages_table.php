<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCarrierMessagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('carrier_messages', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('parcel_status_name')->nullable();
            $table->string('parcel_status_id')->nullable();
            $table->string('status_text')->nullable();
            $table->string('carrier_status_code')->nullable();
            $table->string('carrier_reason_code')->nullable();
            $table->string('depot_achieving_status')->nullable();
            $table->string('parcel_code')->nullable();
            $table->string('system_correlation_code')->nullable();
            $table->string('custom1')->nullable();
            $table->string('carrier_consignment_code')->nullable();
            $table->string('carrier_code')->nullable();
            $table->string('con_matching_code')->nullable();
            $table->string('achieved_datetime')->nullable();
            $table->string('matched_datetime')->nullable();
            $table->string('notified_by_source_datetime')->nullable();
            $table->string('processed_datetime')->nullable();
            $table->string('postcode')->nullable();
            $table->string('transaction_type_id')->nullable();
            $table->string('destination_country_code')->nullable();
            $table->string('system_code')->nullable();
            $table->string('retailer_id')->nullable();
            $table->string('retailer_key')->nullable();
            $table->string('metapack_carrier_service_code')->nullable();
            $table->string('order_ref')->nullable();
            $table->string('achieved_timezone')->nullable();
            $table->string('trackable_item_completed_date')->nullable();
            $table->unsignedBigInteger('rg_id')->nullable();
            $table->unsignedBigInteger('order_id')->nullable();
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
        Schema::dropIfExists('carrier_messages');
    }
}
