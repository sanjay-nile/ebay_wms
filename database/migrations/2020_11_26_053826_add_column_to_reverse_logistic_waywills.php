<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColumnToReverseLogisticWaywills extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('reverse_logistic_waybills', function (Blueprint $table) {
            $table->string('cancel_return_status')->nullable()->after('return_order_status');
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
            $table->dropColumn('cancel_return_status');
        });
    }
}
