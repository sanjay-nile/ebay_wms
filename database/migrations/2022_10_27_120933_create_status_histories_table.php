<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateStatusHistoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('status_histories', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('post_id')->unsigned();
            $table->string('status_id')->nullable();
            $table->string('type')->nullable();
            $table->string('addition_info')->nullable();
            $table->string('status_date')->nullable();
            $table->string('status_time')->nullable();
            $table->string('user')->nullable();            
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
        Schema::dropIfExists('status_histories');
    }
}
