<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAppraisalQuestionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('appraisal_questions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('appraisal_policy')
                ->references('id')
                ->on('appraisal_policies')
                ->constrained()
                ->cascadeOnDelete();
            $table->string('type');
            $table->text('question');
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
        Schema::dropIfExists('appraisal_questions');
    }
}