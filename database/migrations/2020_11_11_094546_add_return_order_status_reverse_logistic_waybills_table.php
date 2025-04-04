<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddReturnOrderStatusReverseLogisticWaybillsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('reverse_logistic_waybills', function (Blueprint $table) {
            $table->enum('return_order_status',['1','2'])->default('2')->after('rcvd_at_hub_date');
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
            $table->dropColumn('return_order_status');
        });
    }
}
