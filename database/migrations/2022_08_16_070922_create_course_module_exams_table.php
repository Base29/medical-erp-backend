<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCourseModuleExamsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('course_module_exams', function (Blueprint $table) {
            $table->id();
            $table->foreignId('module')
                ->references('id')
                ->on('course_modules')
                ->constrained()->cascadeOnDelete();
            $table->foreignId('user')
                ->references('id')
                ->on('users')
                ->constrained()->cascadeOnDelete();
            $table->string('type')->nullable();
            $table->integer('number_of_questions')->nullable();
            $table->boolean('is_restricted')->nullable();
            $table->string('duration')->nullable();
            $table->longText('description')->nullable();
            $table->string('url')->nullable();
            $table->boolean('is_passing_percentage')->nullable();
            $table->integer('passing_percentage')->nullable();
            $table->boolean('is_passed')->nullable();
            $table->string('grade_achieved')->nullable();
            $table->integer('percentage_achieved')->nullable();
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
        Schema::dropIfExists('course_module_exams');
    }
}