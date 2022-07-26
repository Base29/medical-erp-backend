<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateModuleProgressTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('module_progress', function (Blueprint $table) {
            $table->id();
            $table->foreignId('module')->references('id')->on('course_modules')->constrained()->cascadeOnDelete();
            $table->foreignId('user')->references('id')->on('users')->constrained()->cascadeOnDelete();
            $table->date('completed_at')->nullable();
            $table->string('completion_evidence')->nullable();
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
        Schema::dropIfExists('module_progress');
    }
}