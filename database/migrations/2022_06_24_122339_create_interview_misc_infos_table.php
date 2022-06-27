<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateInterviewMiscInfosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('interview_misc_infos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('interview')
                ->references('id')
                ->on('interview_schedules')
                ->nullable()
                ->constrained()
                ->cascadeOnDelete();
            $table->string('current_salary')->nullable();
            $table->string('expected_salary')->nullable();
            $table->string('difference')->nullable();
            $table->string('availability')->nullable();
            $table->string('available_time')->nullable();
            $table->string('job_type')->nullable();
            $table->string('dbs')->nullable();
            $table->boolean('dismissals')->nullable();
            $table->boolean('given_notice')->nullable();
            $table->date('notice_start')->nullable();
            $table->string('notice_duration')->nullable();
            $table->boolean('interviewing_elsewhere')->nullable();
            $table->text('salary_notes')->nullable();
            $table->text('notice_notes')->nullable();
            $table->softDeletes();
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
        Schema::dropIfExists('interview_misc_infos');
    }
}