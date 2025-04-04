<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateReverseLogisticTrackingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('reverse_logistic_trackings', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('tracking_id');
            $table->string('status')->nullable();
            $table->unsignedBigInteger('reverse_logistic_waybill_id');
            $table->foreign('reverse_logistic_waybill_id')->references('id')->on('reverse_logistic_waybills')->onDelete('cascade')->onUpdate('cascade');
            $table->mediumText('json_data');
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
        Schema::dropIfExists('reverse_logistic_trackings');
    }
}
