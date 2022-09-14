<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddExamRequiredFieldToModuleProgressTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('module_progress', function (Blueprint $table) {
            $table->boolean('is_exam_required')->nullable()->after('completion_evidence');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('module_progress', function (Blueprint $table) {
            $table->dropColumn('is_exam_required');
        });
    }
}