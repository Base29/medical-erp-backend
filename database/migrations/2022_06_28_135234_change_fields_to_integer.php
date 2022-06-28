<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ChangeFieldsToInteger extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('interview_misc_infos', function (Blueprint $table) {
            $table->integer('current_salary')->nullable()->change();
            $table->integer('expected_salary')->nullable()->change();
            $table->integer('difference')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('interview_misc_infos', function (Blueprint $table) {
            $table->string('current_salary')->nullable()->change();
            $table->string('expected_salary')->nullable()->change();
            $table->string('difference')->nullable()->change();
        });
    }
}