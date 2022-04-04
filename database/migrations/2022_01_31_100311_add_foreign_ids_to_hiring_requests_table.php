<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddForeignIdsToHiringRequestsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('hiring_requests', function (Blueprint $table) {
            $table->foreignId('job_specification_id')->after('practice_id')->constrained()->cascadeOnDelete();
            $table->foreignId('person_specification_id')->after('job_specification_id')->constrained()->cascadeOnDelete();
            $table->dropColumn('job_specification');
            $table->dropColumn('person_specification');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('hiring_requests', function (Blueprint $table) {
            $table->dropConstrainedForeignId('job_specification_id');
            $table->dropConstrainedForeignId('person_specification_id');
        });
    }
}