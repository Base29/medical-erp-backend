<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAdhocQuestionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('adhoc_questions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('interview')
                ->references('id')
                ->on('interview_schedules')
                ->constrained()
                ->cascadeOnDelete();
            $table->text('question')->nullable();
            $table->text('answer')->nullable();
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
        Schema::dropIfExists('adhoc_questions');
    }
}