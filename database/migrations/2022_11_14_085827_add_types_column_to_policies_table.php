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
        Schema::table('policies', function (Blueprint $table) {
            $table->text('description')->nullable()->after('name');
            $table->string('type')->nullable()->after('description');
            $table->foreignId('practice_id')->nullable()->change();
            $table->string('name')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('policies', function (Blueprint $table) {
            $table->dropColumn('description');
            $table->dropColumn('type');
            $table->foreignId('practice_id')->nullable(false)->change();
            $table->string('name')->nullable(false)->change();
        });
    }
};