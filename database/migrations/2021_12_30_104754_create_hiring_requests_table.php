<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateHiringRequestsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('hiring_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('practice_id')->constrained()->cascadeOnDelete();
            $table->string('job_title')->nullable();
            $table->string('contract_type')->nullable();
            $table->string('department')->nullable();
            $table->integer('reporting_to')->nullable();
            $table->date('start_date')->nullable();
            $table->string('starting_salary')->nullable();
            $table->string('reason_for_recruitment')->nullable();
            $table->text('comment')->nullable();
            $table->integer('job_specification')->nullable();
            $table->integer('person_specification')->nullable();
            $table->integer('rota_information')->nullable();
            $table->boolean('is_approved')->default(0);
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
        Schema::dropIfExists('hiring_requests');
    }
}