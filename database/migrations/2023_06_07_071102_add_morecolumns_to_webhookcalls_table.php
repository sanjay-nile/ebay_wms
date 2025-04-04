<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddMorecolumnsToWebhookcallsTable extends Migration
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
            $table->string('Dispcountry')->nullable()->after('happyReturnsItemID');
            $table->string('OriginCountry')->nullable()->after('Dispcountry');
            $table->string('GoodsDescription')->nullable()->after('OriginCountry');
            $table->string('CommodityCode')->nullable()->after('GoodsDescription');
            $table->string('ItemGrossMass')->nullable()->after('CommodityCode');
            $table->string('ItemNetMass')->nullable()->after('ItemGrossMass');
            $table->string('Quantity')->nullable()->after('ItemNetMass');
            $table->string('Currency')->nullable()->after('Quantity');
            $table->string('SellingPrice')->nullable()->after('Currency');
            $table->string('ConsignorName')->nullable()->after('SellingPrice');
            $table->string('ConsignorStreet')->nullable()->after('ConsignorName');
            $table->string('ConsignorCity')->nullable()->after('ConsignorStreet');
            $table->string('ConsignorPostcode')->nullable()->after('ConsignorCity');
            $table->string('ConsignorCountry')->nullable()->after('ConsignorPostcode');
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
