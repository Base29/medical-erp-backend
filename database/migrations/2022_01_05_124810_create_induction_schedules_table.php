<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateInductionSchedulesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('induction_schedules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('practice_id')->constrained()->cascadeOnDelete();
            $table->foreignId('induction_checklist_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->date('date')->nullable();
            $table->time('time')->nullable();
            $table->string('duration')->nullable();
            $table->boolean('is_hq_required')->default(0);
            $table->integer('hq_staff_role_id')->nullable();
            $table->integer('hq_staff_id')->nullable();
            $table->boolean('is_additional_staff_required')->default(0);
            $table->integer('additional_staff_role_id')->nullable();
            $table->integer('additional_staff_id')->nullable();
            $table->boolean('is_completed')->default(0);
            $table->date('completed_date')->nullable();
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
        Schema::dropIfExists('induction_schedules');
    }
}