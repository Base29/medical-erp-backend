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
            $table->id();
            $table->foreignId('induction_checklist_id')->constrained()->cascadeOnDelete();
            $table->foreignId('induction_schedule_id')->constrained()->cascadeOnDelete();
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