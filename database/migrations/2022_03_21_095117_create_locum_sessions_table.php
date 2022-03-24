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
            $table->foreignId('practice')
                ->nullable()
                ->references('id')
                ->on('practices')
                ->constrained()
                ->cascadeOnDelete();
            $table->foreignId('role')
                ->nullable()
                ->references('id')
                ->on('roles')
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