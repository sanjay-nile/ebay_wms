<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColumnToWebhookcallsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('webhookcalls', function (Blueprint $table) {
            $table->string('purchasedDate')->nullable()->after('payload');
            $table->string('orderNumber')->nullable()->after('purchasedDate');
            $table->string('orderID')->nullable()->after('orderNumber');
            $table->string('orderLineItemID')->nullable()->after('orderID');
            $table->string('sku')->nullable()->after('orderLineItemID');
            $table->string('upc')->nullable()->after('sku');
            $table->string('productVariantID')->nullable()->after('upc');
            $table->string('labelBarcode')->nullable()->after('productVariantID');
            $table->string('productName')->nullable()->after('labelBarcode');
            $table->string('returnReason')->nullable()->after('productName');
            $table->string('categoryID')->nullable()->after('returnReason');
            $table->string('refundType')->nullable()->after('categoryID');
            $table->string('oneClickExchange')->nullable()->after('refundType');
            $table->string('location')->nullable()->after('oneClickExchange');
            $table->string('locationMetro')->nullable()->after('location');
            $table->string('country')->nullable()->after('locationMetro');
            $table->string('startedDate')->nullable()->after('country');
            $table->string('approvedDate')->nullable()->after('startedDate');
            $table->string('inspectionOutcome')->nullable()->after('approvedDate');
            $table->string('poNumber')->nullable()->after('inspectionOutcome');
            $table->string('palletNumber')->nullable()->after('poNumber');
            $table->string('shippingBoxBarcode')->nullable()->after('palletNumber');
            $table->string('shippingBoxKind')->nullable()->after('shippingBoxBarcode');
            $table->string('tracking')->nullable()->after('shippingBoxKind');
            $table->string('carrier')->nullable()->after('tracking');
            $table->string('departureDate')->nullable()->after('carrier');
            $table->string('happyReturnsRetailerID')->nullable()->after('departureDate');
            $table->string('destinationAddressID')->nullable()->after('happyReturnsRetailerID');
            $table->string('happyReturnsExpressCode')->nullable()->after('destinationAddressID');
            $table->string('happyReturnsItemID')->nullable()->after('happyReturnsExpressCode');
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
            $table->dropColumn('purchasedDate');
            $table->dropColumn('orderNumber');
            $table->dropColumn('orderID');
            $table->dropColumn('orderLineItemID');
            $table->dropColumn('sku');
            $table->dropColumn('upc');
            $table->dropColumn('productVariantID');
            $table->dropColumn('labelBarcode');
            $table->dropColumn('productName');
            $table->dropColumn('returnReason');
            $table->dropColumn('categoryID');
            $table->dropColumn('refundType');
            $table->dropColumn('oneClickExchange');
            $table->dropColumn('location');
            $table->dropColumn('locationMetro');
            $table->dropColumn('country');
            $table->dropColumn('startedDate');
            $table->dropColumn('approvedDate');
            $table->dropColumn('inspectionOutcome');
            $table->dropColumn('poNumber');
            $table->dropColumn('palletNumber');
            $table->dropColumn('shippingBoxBarcode');
            $table->dropColumn('shippingBoxKind');
            $table->dropColumn('tracking');
            $table->dropColumn('carrier');
            $table->dropColumn('departureDate');
            $table->dropColumn('happyReturnsRetailerID');
            $table->dropColumn('destinationAddressID');
            $table->dropColumn('happyReturnsExpressCode');
            $table->dropColumn('happyReturnsItemID');
        });
    }
}
