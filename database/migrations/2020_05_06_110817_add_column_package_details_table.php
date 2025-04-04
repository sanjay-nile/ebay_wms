<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColumnPackageDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('package_details', function (Blueprint $table) {
            $table->double('price',10,3)->default(0)->after('title');
            $table->string('color',50)->nullable()->after('custom_price');
            $table->string('size',50)->nullable()->after('color');
            $table->enum('dimension',['CM','IN'])->nullable()->after('size');
            $table->enum('weight_unit_type',['LBS','KGS'])->nullable()->after('dimension');
            $table->mediumText('file_data')->nullable()->after('weight_unit_type');
            $table->string('estimated_value',50)->nullable()->after('file_data');
            $table->string('hs_code',50)->nullable()->after('estimated_value');
            $table->string('return_status',100)->nullable()->after('file_data');
            $table->enum('refund_status',['Yes','No'])->default('No')->after('return_status');
            $table->string('return_reason',100)->nullable()->after('refund_status');
            $table->string('hiting_count')->default(0)->after('return_reason');
            $table->text('image_url')->nullable()->after('hiting_count');
            $table->datetime('rcvd_date_at_returnbar')->nullable()->default(null)->after('image_url');
            $table->string('note')->nullable()->after('rcvd_date_at_returnbar');
            $table->enum('process_status',['Processed','UnProcessed'])->default('UnProcessed')->after('note');
            $table->string('pallet_id')->nullable()->after('process_status');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('package_details', function (Blueprint $table) {
            $table->dropColumn('price');
            $table->dropColumn('color');
            $table->dropColumn('size');
            $table->dropColumn('dimension');
            $table->dropColumn('weight_unit_type');
            $table->dropColumn('file_data');
            $table->dropColumn('estimated_value');
            $table->dropColumn('hs_code');
            $table->dropColumn('return_status');
            $table->dropColumn('return_reason');
            $table->dropColumn('hiting_count');
            $table->dropColumn('image_url');
            $table->dropColumn('rcvd_date_at_returnbar');
        });
    }
}
