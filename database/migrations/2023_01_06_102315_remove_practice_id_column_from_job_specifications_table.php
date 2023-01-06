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
        Schema::table('job_specifications', function (Blueprint $table) {
            $table->dropConstrainedForeignId('practice_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('job_specifications', function (Blueprint $table) {
            $table->foreignId('practice_id')->nullable()->after('id')->constrained()->cascadeOnDelete();
        });
    }
};