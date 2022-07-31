<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateModuleLessonsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('module_lessons', function (Blueprint $table) {
            $table->id();
            $table->foreignId('module')->references('id')->on('course_modules')->constrained()->cascadeOnDelete();
            $table->string('name')->nullable();
            $table->date('start_date')->nullable();
            $table->date('due_date')->nullable();
            $table->text('description')->nullable();
            $table->string('url')->nullable();
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
        Schema::dropIfExists('module_lessons');
    }
}