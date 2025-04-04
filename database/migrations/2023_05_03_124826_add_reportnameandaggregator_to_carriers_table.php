<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddReportnameandaggregatorToCarriersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('carriers', function (Blueprint $table) {
            //
            $table->string('aggregator')->after('unit_type')->nullable();
            $table->string('reportName')->after('unit_type')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('carriers', function (Blueprint $table) {
            //
        });
    }
}
