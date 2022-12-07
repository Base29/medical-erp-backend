<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('appraisal_reschedules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('appraisal')->references('id')->on('appraisals')->constrained()->cascadeOnDelete();
            $table->text('reason')->nullable();
            $table->date('date')->nullable();
            $table->time('time')->nullable();
            $table->string('duration')->nullable();
            $table->string('location')->nullable();
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
        Schema::dropIfExists('appraisal_reschedules');
    }
};