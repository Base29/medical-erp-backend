<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ModifyUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('first_name')->default(null)->after('id');
            $table->string('middle_name')->default(null)->after('first_name');
            $table->string('maiden_name')->default(null)->after('middle_name');
            $table->string('last_name')->default(null)->after('maiden_name');
            $table->string('profile_image')->default(null)->after('last_name');
            $table->string('gender')->default(null)->after('profile_image');
            $table->string('email_personal')->default(null)->after('email');
            $table->string('work_phone')->default(null)->after('email_verified_at');
            $table->string('home_phone')->default(null)->after('work_phone');
            $table->string('mobile_phone')->default(null)->after('home_phone');
            $table->date('dob')->default('1970-01-01')->after('mobile_phone');
            $table->string('address')->default(null)->after('dob');
            $table->string('city')->default(null)->after('address');
            $table->string('county')->default(null)->after('city');
            $table->string('country')->default(null)->after('county');
            $table->string('zip_code')->default(null)->after('country');
            $table->string('nhs_card')->default(null)->after('zip_code');
            $table->string('nhs_number')->default(null)->after('nhs_card');
            $table->dropColumn('name');
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
            $table->dropColumn('first_name');
            $table->dropColumn('middle_name');
            $table->dropColumn('maiden_name');
            $table->dropColumn('last_name');
            $table->dropColumn('profile_image');
            $table->dropColumn('gender');
            $table->dropColumn('email_personal');
            $table->dropColumn('work_phone');
            $table->dropColumn('home_phone');
            $table->dropColumn('mobile_phone');
            $table->dropColumn('dob');
            $table->dropColumn('address');
            $table->dropColumn('city');
            $table->dropColumn('county');
            $table->dropColumn('country');
            $table->dropColumn('zip_code');
            $table->dropColumn('nhs_card');
            $table->dropColumn('nhs_number');
        });
    }
}