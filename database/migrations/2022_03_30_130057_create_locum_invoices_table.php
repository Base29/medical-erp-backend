<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLocumInvoicesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('locum_invoices', function (Blueprint $table) {
            $table->id();
            $table->foreignId('session')->references('id')->on('locum_sessions')->constrained()->cascadeOnDelete();
            $table->foreignId('locum')->references('id')->on('users')->constrained()->cascadeOnDelete();
            $table->foreignId('location')->references('id')->on('practices')->constrained()->cascadeOnDelete();
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->time('start_time')->nullable();
            $table->time('end_time')->nullable();
            $table->string('session_invoice')->nullable();
            $table->integer('esm_status')->default(1);
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
        Schema::dropIfExists('locum_invoices');
    }
}