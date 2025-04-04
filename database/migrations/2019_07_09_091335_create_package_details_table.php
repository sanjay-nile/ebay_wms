<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePackageDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('package_details', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('reverse_logistic_waybill_id');
            $table->foreign('reverse_logistic_waybill_id')->references('id')->on('reverse_logistic_waybills')->onDelete('cascade')->onUpdate('cascade');
            $table->string('bar_code',50)->nullable();
            $table->string('title')->nullable();
            $table->integer('package_count');
            $table->double('length',8,2);
            $table->double('width',8,2);
            $table->double('height',8,2);
            $table->double('weight',8,2);
            $table->double('charged_weight',8,2);
            $table->string('selected_package_type_code',50);
            $table->string('status')->nullable();
            $table->double('custom_price',8,2)->nullable();
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
        Schema::dropIfExists('package_details');
    }
}
