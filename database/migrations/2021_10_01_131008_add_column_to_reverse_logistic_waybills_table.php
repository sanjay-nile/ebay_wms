<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColumnToReverseLogisticWaybillsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('reverse_logistic_waybills', function (Blueprint $table) {
            $table->string('rg_reference_number')->nullable()->after('cancel_return_status');
            $table->string('suppliercode')->nullable()->after('rg_reference_number');
            $table->string('manifest_data')->nullable()->after('suppliercode');
            $table->string('inscan_status')->nullable()->after('manifest_data');
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
            $table->dropColumn('rg_reference_number');
            $table->dropColumn('suppliercode');
            $table->dropColumn('manifest_data');
            $table->dropColumn('inscan_status');
        });
    }
}
