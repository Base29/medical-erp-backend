<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddUserFieldToInterviewAnswersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('interview_answers', function (Blueprint $table) {
            $table->foreignId('user')
                ->after('interview')
                ->nullable()
                ->references('id')
                ->on('users')
                ->constrained()
                ->cascadeOnDelete();

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('interview_answers', function (Blueprint $table) {
            $table->dropConstrainedForeignId('user');
        });
    }
}