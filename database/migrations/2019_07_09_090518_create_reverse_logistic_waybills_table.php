<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateReverseLogisticWaybillsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('reverse_logistic_waybills', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('created_by');
            $table->foreign('created_by')->references('id')->on('users')->onDelete('cascade')->onUpdate('cascade');

            $table->unsignedBigInteger('client_id');
            $table->foreign('client_id')->references('id')->on('users')->onDelete('cascade')->onUpdate('cascade');

            $table->unsignedBigInteger('warehouse_id')->nullable();
            $table->foreign('warehouse_id')->references('id')->on('warehouses')->onDelete('set null')->onUpdate('cascade');

            $table->unsignedBigInteger('shipping_policy_id')->nullable();
            $table->foreign('shipping_policy_id')->references('id')->on('shipping_policies')->onDelete('set null')->onUpdate('cascade');

            $table->string('way_bill_number')->nullable();
            $table->date('delivery_date')->nullable();
            $table->string('invoice_number',50)->nullable();
            $table->string('payment_mode',50)->nullable();
            $table->string('cod_payment_mode',50)->nullable();
            $table->double('amount',10,3)->nullable();
            $table->enum('type',['A','I','D'])->nullable();
            $table->enum('return_by',['EQTOR_ADMIN','EQTOR_CUSTOMER','RG_ADMIN','RG_CUSTOMER'])->nullable();
            $table->enum('created_from',['RG','EQTOR'])->nullable();
            $table->enum('status',['Pending','Success','Deleted'])->default('Pending');
            $table->string('process_status')->default('unprocessed');
            $table->string('pallet_id')->nullable();
            $table->double('reverse_logistic_refund_amount',10,3)->nullable();            
            $table->string('tracking_id',200)->nullable();
            $table->string('qr_code')->nullable();
            $table->mediumText('tracking_json_data')->nullable();
            $table->date('rcvd_at_hub_date')->nullable();
            $table->timestamp('created_at')->useCurrent();           
            $table->timestamp('updated_at')->useCurrent();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('reverse_logistic_waybills');
    }
}
