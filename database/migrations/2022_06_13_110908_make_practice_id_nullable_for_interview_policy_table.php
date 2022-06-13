<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class MakePracticeIdNullableForInterviewPolicyTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('interview_policies', function (Blueprint $table) {
            $table->foreignId('practice_id')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('interview_policies', function (Blueprint $table) {
            $table->foreignId('practice_id')->nullable(false)->change();
        });
    }
}