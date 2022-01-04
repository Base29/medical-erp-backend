<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateHiringRequestWorkPatternTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('hiring_request_work_pattern', function (Blueprint $table) {
            $table->id();
            $table->foreignId('hiring_request_id')->constrained()->cascadeOnDelete();
            $table->foreignId('work_pattern_id')->constrained()->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('hiring_request_work_pattern');
    }
}