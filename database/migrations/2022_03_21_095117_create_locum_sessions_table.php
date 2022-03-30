<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLocumSessionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('locum_sessions', function (Blueprint $table) {
            $table->id();
            $table->string('name')->nullable();
            $table->foreignId('practice_id')
                ->nullable()
                ->constrained()
                ->cascadeOnDelete();
            $table->foreignId('role_id')
                ->nullable()
                ->constrained()
                ->cascadeOnDelete();
            $table->integer('quantity')->nullable();
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->time('start_time')->nullable();
            $table->time('end_time')->nullable();
            $table->integer('rate')->nullable();
            $table->string('unit')->nullable();
            $table->string('location')->nullable();
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
        Schema::dropIfExists('locum_sessions');
    }
}