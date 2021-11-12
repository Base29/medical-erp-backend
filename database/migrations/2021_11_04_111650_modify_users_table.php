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
            $table->string('first_name')->after('id');
            $table->string('middle_name')->default(null)->nullable()->after('first_name');
            $table->string('maiden_name')->default(null)->nullable()->after('middle_name');
            $table->string('last_name')->after('maiden_name');
            $table->string('profile_image')->default(null)->nullable()->after('last_name');
            $table->string('gender')->after('profile_image');
            $table->string('email_professional')->default(null)->nullable()->after('email');
            $table->string('work_phone')->default(null)->nullable()->after('email_verified_at');
            $table->string('home_phone')->default(null)->nullable()->after('work_phone');
            $table->string('mobile_phone')->default(null)->after('home_phone');
            $table->date('dob')->default('1970-01-01')->after('mobile_phone');
            $table->string('address')->after('dob');
            $table->string('city')->after('address');
            $table->string('county')->after('city');
            $table->string('country')->after('county');
            $table->string('zip_code')->after('country');
            $table->string('nhs_card')->default(null)->nullable()->after('zip_code');
            $table->string('nhs_number')->default(null)->nullable()->after('nhs_card');
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
            $table->dropColumn('primary_role');
            $table->dropColumn('gender');
            $table->dropColumn('email_professional');
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
            $table->string('name')->default(null)->after('id');
        });
    }
}