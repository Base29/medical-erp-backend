<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateInterviewScoresTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('interview_scores', function (Blueprint $table) {
            $table->id();
            $table->foreignId('interview')
                ->references('id')
                ->on('interview_schedules')
                ->nullable()
                ->constrained()
                ->cascadeOnDelete();
            $table->integer('cultural_fit')->nullable();
            $table->integer('career_motivation')->nullable();
            $table->integer('social_skills')->nullable();
            $table->integer('team_work')->nullable();
            $table->integer('technical_skills')->nullable();
            $table->integer('leadership_capability')->nullable();
            $table->integer('critical_thinking_problem_solving')->nullable();
            $table->integer('self_awareness')->nullable();
            $table->integer('total')->nullable();
            $table->text('remarks')->nullable();
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
        Schema::dropIfExists('interview_scores');
    }
}