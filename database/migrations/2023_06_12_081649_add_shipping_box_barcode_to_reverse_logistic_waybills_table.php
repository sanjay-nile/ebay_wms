<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddShippingBoxBarcodeToReverseLogisticWaybillsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('reverse_logistic_waybills', function (Blueprint $table) {
            //
            $table->string('shippingBoxBarcode')->after('inscan_status')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('reverse_logistic_waybills', function (Blueprint $table) {
            //
        });
    }
}
