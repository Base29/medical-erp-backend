<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddInterviewPolicyIdToInterviewSchedulesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('interview_schedules', function (Blueprint $table) {
            $table->foreignId('interview_policy_id')->nullable()->after('practice_id')->constrained()->cascadeOnDelete();
            $table->foreignId('department_id')->nullable()->after('interview_policy_id')->constrained()->cascadeOnDelete();
            $table->dropConstrainedForeignId('interview_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('interview_schedules', function (Blueprint $table) {
            $table->dropConstrainedForeignId('interview_policy_id');
        });
    }
}