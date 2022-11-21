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
        Schema::table('offer_amendments', function (Blueprint $table) {
            $table->date('joining_date')->nullable()->after('amount');
            $table->boolean('is_active')->nullable()->after('joining_date');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('offer_amendments', function (Blueprint $table) {
            $table->dropColumn('joining_date');
            $table->dropColumn('is_active');
        });
    }
};