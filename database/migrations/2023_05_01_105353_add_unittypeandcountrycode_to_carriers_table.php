<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddUnittypeandcountrycodeToCarriersTable extends Migration
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
            $table->string('unit_type')->after('status')->nullable();
            $table->string('countrycode')->after('status')->nullable();
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
