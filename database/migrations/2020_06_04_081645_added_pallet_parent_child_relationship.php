<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddedPalletParentChildRelationship extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('pallet_deatils', function (Blueprint $table) {
            $table->unsignedBigInteger('parent')->nullable()->after('id');
            $table->foreign('parent')->references('id')->on('pallet_deatils')->onDelete('cascade')->onUpdate('cascade');
            $table->enum('type',['S','M'])->nullable()->after('parent');
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
            $table->dropColumn('parent');
        });
    }
}
