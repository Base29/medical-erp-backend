<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddFieldsToUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('name');
            $table->boolean('is_active')->default(0)->after('email');
            $table->boolean('is_candidate')->default(0)->after('is_active');
            $table->boolean('is_hired')->default(0)->after('is_candidate');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('name')->after('id');
            $table->dropColumn('is_active');
            $table->dropColumn('is_candidate');
            $table->dropColumn('is_hired');
        });
    }
}