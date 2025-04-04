<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUsers extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('user_code',40)->nullable();
            $table->string('slug')->nullable();
            $table->string('name',40)->nullable();
            $table->string('first_name',20)->nullable();
            $table->string('last_name',20)->nullable();
            $table->string('email')->unique();
            $table->string('password');
            $table->string('phone',15)->nullable();
            $table->string('contact_person_name',191)->nullable();
            $table->string('department',191)->nullable();
            $table->string('role',191)->nullable();
            $table->enum('is_assigned',['NA','N','Y']);
            $table->string('sub_client_permission',2)->nullable();
            $table->enum('status',['1','2']);
            $table->unsignedBigInteger('user_type_id');
            $table->unsignedBigInteger('created_by')->nullable();
            $table->foreign('user_type_id')->references('id')->on('user_types')->onDelete('cascade')->onUpdate('cascade');
            $table->foreign('created_by')->references('id')->on('users')->onDelete('cascade')->onUpdate('cascade');
            $table->string('token',191)->nullable();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('address')->nullable();
            $table->rememberToken();
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
        Schema::dropIfExists('users');
    }
}
