<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateInterviewPolicyInterviewScheduleTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('interview_policy_interview_schedule', function (Blueprint $table) {
            $table->id();
            $table->foreignId('interview_policy_id')
                ->constrained()
                ->cascadeOnDelete()->index('policy_id');
            $table->foreignId('interview_schedule_id')
                ->constrained()
                ->cascadeOnDelete()->index('schedule_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('interview_policy_interview_schedule');
    }
}