<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddCreatorFieldInLocumSessionInvitesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('locum_session_invites', function (Blueprint $table) {
            $table->foreignId('creator')
                ->after('id')
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
        Schema::table('locum_session_invites', function (Blueprint $table) {
            $table->dropConstrainedForeignId('creator');
        });
    }
}