<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddEmailhscodeandproductcodeToWebhookcallsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('webhookcalls', function (Blueprint $table) {
            //
            $table->string('ProductCode')->nullable()->after('happyReturnsItemID');
            $table->string('HSCode')->nullable()->after('ProductCode');
            $table->string('Email')->nullable()->after('HSCode');

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('webhookcalls', function (Blueprint $table) {
            //
        });
    }
}
