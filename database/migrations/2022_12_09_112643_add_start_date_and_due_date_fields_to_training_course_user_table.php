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
        Schema::table('training_course_user', function (Blueprint $table) {
            $table->date('start_date')->nullable()->after('user_id');
            $table->date('due_date')->nullable()->after('start_date');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('training_course_user', function (Blueprint $table) {
            $table->dropColumn('start_date');
            $table->dropColumn('due_date');
        });
    }
};