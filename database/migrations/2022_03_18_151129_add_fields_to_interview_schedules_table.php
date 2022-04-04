<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddFieldsToInterviewSchedulesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('interview_schedules', function (Blueprint $table) {
            $table->foreignId('additional_staff')
                ->nullable()
                ->after('is_completed')
                ->references('id')
                ->on('users')
                ->constrained()
                ->cascadeOnDelete();
            $table->foreignId('hq_staff')
                ->nullable()
                ->after('additional_staff')
                ->references('id')
                ->on('users')
                ->constrained()
                ->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('interview_schedules', function (Blueprint $table) {
            $table->dropConstrainedForeignId('additional_staff');
            $table->dropConstrainedForeignId('hq_staff');
        });
    }
}