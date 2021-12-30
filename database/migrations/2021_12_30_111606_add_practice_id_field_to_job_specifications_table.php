<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddPracticeIdFieldToJobSpecificationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('job_specifications', function (Blueprint $table) {
            $table->foreignId('practice_id')->constrained()->cascadeOnDelete()->default(1)->after('id');
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
            $table->dropConstrainedForeignId('practice_id');
        });
    }
}