<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class RenameJobDescriptionColumnToJobSpecificationInMiscellaneousInformationTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('miscellaneous_information', function (Blueprint $table) {
            $table->renameColumn('job_description', 'job_specification')->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('miscellaneous_information', function (Blueprint $table) {
            $table->renameColumn('job_specification', 'job_description')->change();
        });
    }
}