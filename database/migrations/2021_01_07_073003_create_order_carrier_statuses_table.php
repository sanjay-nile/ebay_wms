<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateOrderCarrierStatusesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('order_carrier_statuses', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('parcel_code')->nullable();
            $table->integer('parcel_status_id')->nullable();
            $table->string('parcel_status_name')->nullable();
            $table->string('carrier_status_code')->nullable();
            $table->string('carrier_reason_code')->nullable();
            $table->string('con_matching_code')->nullable();
            $table->string('achieved_datetime')->nullable();
            $table->string('matched_datetime')->nullable();
            $table->string('notified_by_source_datetime')->nullable();
            $table->string('processed_datetime')->nullable();
            $table->string('achieved_timezone')->nullable();
            $table->string('trackable_item_completed_date')->nullable();
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
        Schema::dropIfExists('order_carrier_statuses');
    }
}
