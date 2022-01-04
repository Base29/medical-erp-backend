<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddAdditionalFoeldsToEmploymentChecksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('employment_checks', function (Blueprint $table) {
            $table->string('right_to_work_certificate')->after('right_to_work_status')->nullable();
            $table->string('dbs_certificate_number')->after('dbs_certificate')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('employment_checks', function (Blueprint $table) {
            $table->dropColumn('right_to_work_certificate');
            $table->dropColumn('dbs_certificate_number');
        });
    }
}