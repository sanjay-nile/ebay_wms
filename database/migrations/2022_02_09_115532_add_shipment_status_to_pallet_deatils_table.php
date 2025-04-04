<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddShipmentStatusToPalletDeatilsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('pallet_deatils', function (Blueprint $table) {
            $table->string('shipment_status')->nullable()->after('pallet_type');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('pallet_deatils', function (Blueprint $table) {
            $table->dropColumn('shipment_status');
        });
    }
}
