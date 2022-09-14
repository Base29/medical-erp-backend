<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddFieldsToProfilesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('profiles', function (Blueprint $table) {
            $table->boolean('nhs_employment')->after('nhs_number')->nullable();
            $table->string('nhs_smart_card_number')->after('nhs_employment')->nullable();
            $table->boolean('tutorial_completed')->after('nhs_smart_card_number')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('profiles', function (Blueprint $table) {
            $table->dropColumn('nhs_employment');
            $table->dropColumn('tutorial_completed');
            $table->dropColumn('nhs_smart_card_number');
        });
    }
}