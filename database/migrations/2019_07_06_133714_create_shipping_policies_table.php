<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateShippingPoliciesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('shipping_policies', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('user_id');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade')->onUpdate('cascade');
            $table->unsignedBigInteger('shipping_type_id')->nullable();
            $table->foreign('shipping_type_id')->references('id')->on('shipping_types')->onDelete('cascade')->onUpdate('cascade');

            $table->unsignedBigInteger('carrier_id')->nullable();
            $table->foreign('carrier_id')->references('id')->on('carriers')->onDelete('cascade')->onUpdate('cascade');

            $table->unsignedBigInteger('other_charge_id')->nullable();
            $table->foreign('other_charge_id')->references('id')->on('other_charges')->onDelete('cascade')->onUpdate('cascade');
            $table->string('type',50)->nullable();
            $table->string('currency',50)->nullable();
            $table->double('rate',8,2);
            $table->enum('status',['1','2']);
            $table->enum('is_default',['1','0'])->default(0);
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
        Schema::dropIfExists('shipping_policies');
    }
}
