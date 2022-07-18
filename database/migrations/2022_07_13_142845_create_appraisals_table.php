<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAppraisalsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('appraisals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('practice')
                ->references('id')
                ->on('practices')
                ->constrained()
                ->cascadeOnDelete();
            $table->foreignId('department')
                ->references('id')
                ->on('departments')
                ->constrained()
                ->cascadeOnDelete();
            $table->foreignId('user')
                ->references('id')
                ->on('users')
                ->constrained()
                ->cascadeOnDelete();
            $table->date('date')->nullable();
            $table->time('time')->nullable();
            $table->string('location')->nullable();
            $table->string('type')->nullable();
            $table->string('status')->nullable();
            $table->boolean('is_completed')->nullable();
            $table->string('progress')->nullable();
            $table->integer('additional_staff')->nullable();
            $table->integer('hq_staff')->nullable();
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
        Schema::dropIfExists('appraisals');
    }
}