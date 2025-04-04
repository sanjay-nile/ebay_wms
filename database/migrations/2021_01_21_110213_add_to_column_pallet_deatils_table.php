<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddToColumnPalletDeatilsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('pallet_deatils', function (Blueprint $table) {
            $table->enum('pallet_type',['InProcess','Closed', 'Shipped'])->default('InProcess')->after('flight_date');
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
            $table->dropColumn('pallet_type');
        });
    }
}
