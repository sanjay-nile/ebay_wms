<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateOrderDataTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('order_data', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('order_type')->nullable();
            $table->string('doc_type')->nullable();
            $table->string('order_id')->nullable();
            $table->string('order_taken_date')->nullable();
            $table->string('company')->nullable();
            $table->string('sales_org')->nullable();
            $table->string('site')->nullable();
            $table->string('channel')->nullable();
            $table->string('local')->nullable();
            $table->string('customer_id')->nullable();
            $table->string('customer_name')->nullable();
            $table->string('customer_email')->nullable();
            $table->string('dob')->nullable();
            $table->string('contact_name')->nullable();
            $table->string('contact_phone')->nullable();
            $table->float('sales_order_total')->nullable();
            $table->float('tax_amount')->nullable();
            $table->float('shipping_amount')->nullable();
            $table->float('discount_amount')->nullable();
            $table->string('discount_type')->nullable();
            $table->double('total',10,3)->nullable();
            $table->double('total_amount',10,3)->nullable();
            $table->string('transaction_type')->nullable();
            $table->string('tender_type')->nullable();
            $table->string('card_type')->nullable();
            $table->string('aquirer')->nullable();
            $table->double('amount',10,3)->nullable();
            $table->string('tender_currency')->nullable();
            $table->string('transaction_id')->nullable();
            $table->string('transaction_date')->nullable();
            $table->string('billing_title')->nullable();
            $table->string('billing_name')->nullable();
            $table->string('billing_company')->nullable();
            $table->string('billing_address1')->nullable();
            $table->string('billing_address2')->nullable();
            $table->string('billing_town')->nullable();
            $table->string('billing_country')->nullable();
            $table->string('billing_country_code')->nullable();
            $table->string('billing_postcode')->nullable();
            $table->string('billing_currency')->nullable();
            $table->string('billing_local_cuurency')->nullable();
            $table->string('package_type')->nullable();
            $table->string('delivery_title')->nullable();
            $table->string('delivery_name')->nullable();
            $table->string('delivery_company')->nullable();
            $table->string('delivery_address1')->nullable();
            $table->string('delivery_address2')->nullable();
            $table->string('delivery_town')->nullable();
            $table->string('delivery_country')->nullable();
            $table->string('delivery_postcode')->nullable();
            $table->string('delivery_country_code')->nullable();
            $table->string('waiver')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('order_data');
    }
}
