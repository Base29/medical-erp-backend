<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLocumSessionInvitesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('locum_session_invites', function (Blueprint $table) {
            $table->id();
            $table->foreignId('session')
                ->references('id')
                ->on('locum_sessions')
                ->constrained()
                ->cascadeOnDelete();
            $table->foreignId('locum')
                ->references('id')
                ->on('users')
                ->constrained()
                ->cascadeOnDelete();
            $table->string('title')->nullable();
            $table->integer('status')->default(1);
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('locum_session_invites');
    }
}