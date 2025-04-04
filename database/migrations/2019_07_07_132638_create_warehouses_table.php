<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateWarehousesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('warehouses', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('user_id');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade')->onUpdate('cascade');
             $table->unsignedBigInteger('country_id');
            $table->foreign('country_id')->references('id')->on('countries')->onDelete('cascade')->onUpdate('cascade');
            $table->string('name');
            $table->string('contact_person',50)->nullable();
            $table->string('email',50)->nullable();
            $table->string('state')->nullable();
            $table->string('state_code',10);
            $table->string('city')->nullable();
            $table->string('address')->nullable();
            $table->string('zip_code')->nullable();
            $table->string('code');
            $table->string('phone')->nullable();
            $table->enum('status',['1','2']);
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
        Schema::dropIfExists('warehouses');
    }
}
