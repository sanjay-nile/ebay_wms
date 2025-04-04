<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddFullfillmenttrackingToWebhookcalls extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('webhookcalls', function (Blueprint $table) {
            //
            $table->string('fullfillmenttrackingnumber')->after('cost')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('webhookcalls', function (Blueprint $table) {
            //
        });
    }
}
