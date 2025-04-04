<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateReverseLogisticResponsesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('reverse_logistic_responses', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('label_url');
            $table->string('message',50);
            $table->string('message_type',50);
            $table->string('package_sticker_url');
            $table->string('status',50);
            $table->string('way_bill_number');
            $table->unsignedBigInteger('reverse_logistic_waybill_id');
            $table->foreign('reverse_logistic_waybill_id')->references('id')->on('reverse_logistic_waybills')->onDelete('cascade')->onUpdate('cascade');
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
        Schema::dropIfExists('reverse_logistic_responses');
    }
}
