<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePalletDeatilsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pallet_deatils', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('pallet_id');
            $table->unsignedBigInteger('client_id')->nullable();
            $table->foreign('client_id')->references('id')->on('users')->onDelete('cascade')->onUpdate('cascade');

            $table->unsignedBigInteger('warehouse_id')->nullable();
            $table->foreign('warehouse_id')->references('id')->on('warehouses')->onDelete('set null')->onUpdate('cascade');

            $table->unsignedBigInteger('shipping_type_id')->nullable();
            $table->foreign('shipping_type_id')->references('id')->on('shipping_types')->onDelete('set null')->onUpdate('cascade');

            $table->double('rate',8,2)->nullable();

            /*$table->unsignedBigInteger('carrier_id')->nullable();
            $table->foreign('carrier_id')->references('id')->on('carriers')->onDelete('cascade')->onUpdate('cascade');*/

            $table->string('carrier')->nullable();
            $table->string('tracking_id')->nullable();
            $table->double('fright_charges',8,2)->nullable();
            $table->string('custom_duty')->nullable();
            $table->string('custom_vat')->nullable();
            $table->string('export_vat_number')->nullable();
            $table->string('import_duty_paid')->nullable();
            $table->string('import_vat_paid')->nullable();
            $table->string('import_vat_number')->nullable();
            $table->string('weight_of_shipment')->nullable();
            $table->string('weight_unit_type')->nullable();
            $table->string('return_type')->nullable();
            $table->string('hawb_number')->nullable();
            $table->string('mawb_number')->nullable();
            $table->string('manifest_number')->nullable();
            $table->string('flight_number')->nullable();
            $table->string('rtn_import_entry_number')->nullable();
            $table->string('export_declaration_number')->nullable();
            $table->string('exchange_rate')->nullable();
            $table->datetime('flight_date')->nullable();
            $table->datetime('rtn_import_entry_date')->nullable();
            $table->datetime('export_declaration_date')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('pallet_deatils');
    }
}
