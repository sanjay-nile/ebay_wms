<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColumnUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->text('user_permissions')->nullable()->after('token');
            $table->string('client_type')->nullable()->after('user_permissions');
            $table->enum('priority',['1','2'])->nullable()->after('client_type');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('user_permissions');
            $table->dropColumn('client_type');
            $table->dropColumn('priority');
        });
    }
}
