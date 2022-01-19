<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateInductionChecklistInductionScheduleTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('induction_checklist_induction_schedule', function (Blueprint $table) {
            $table->foreignId('induction_checklist_id')->constrained()->cascadeOnDelete()->index('ic_id');
            $table->foreignId('induction_schedule_id')->constrained()->cascadeOnDelete()->index('is_id');
            $table->boolean('is_complete')->default(0);
            $table->date('completed_date')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('induction_checklist_induction_schedule');
    }
}