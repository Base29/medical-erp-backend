<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateNmcQualificationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('nmc_qualifications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('legal_id')->constrained()->cascadeOnDelete();
            $table->string('qualification');
            $table->date('qualification_date');
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
        Schema::dropIfExists('nmc_qualifications');
    }
}