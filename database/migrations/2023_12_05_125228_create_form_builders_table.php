<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateFormBuildersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('form_builders', function (Blueprint $table) {
            $table->increments('id')->unsigned();
            $table->string('name');
            $table->integer('cat_id')->unsigned()->nullable();
            $table->integer('sub_cat_id')->unsigned()->nullable();
            $table->integer('cat_tier_2')->unsigned()->nullable();
            $table->integer('cat_tier_3')->unsigned()->nullable();
            $table->text('form_content');
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
        Schema::dropIfExists('form_builders');
    }
}
