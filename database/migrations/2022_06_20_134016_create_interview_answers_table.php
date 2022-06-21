<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateInterviewAnswersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('interview_answers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('schedule')
                ->references('id')
                ->on('interview_schedules')
                ->constrained()
                ->cascadeOnDelete();
            $table->foreignId('question')
                ->references('id')
                ->on('interview_questions')
                ->constrained();
            $table->string('answer')->nullable();
            $table->integer('option')->nullable();
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
        Schema::dropIfExists('interview_answers');
    }
}