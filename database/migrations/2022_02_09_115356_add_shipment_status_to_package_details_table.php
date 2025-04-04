<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddShipmentStatusToPackageDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('package_details', function (Blueprint $table) {
            $table->string('shipment_status')->nullable()->after('country_of_origin');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('package_details', function (Blueprint $table) {
            $table->dropColumn('shipment_status');
        });
    }
}
