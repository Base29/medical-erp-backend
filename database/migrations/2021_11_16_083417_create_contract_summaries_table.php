<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateContractSummariesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('contract_summaries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('employee_type')->nullable();
            $table->date('employee_start_date')->nullable();
            $table->date('contract_start_date')->nullable();
            $table->string('working_time_pattern')->nullable();
            $table->string('contracted_hours_per_week')->nullable();
            $table->string('min_leave_entitlement')->nullable();
            $table->string('contract_document')->nullable();
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
        Schema::dropIfExists('contract_summaries');
    }
}