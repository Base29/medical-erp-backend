<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAppraisalAnswersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('appraisal_answers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('appraisal')
                ->references('id')
                ->on('appraisals')
                ->constrained()
                ->cascadeOnDelete();
            $table->foreignId('question')
                ->references('id')
                ->on('appraisal_questions')
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
        Schema::dropIfExists('appraisal_answers');
    }
}