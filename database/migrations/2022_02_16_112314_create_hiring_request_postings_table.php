<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateHiringRequestPostingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('hiring_request_postings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('hiring_request_id')->constrained()->cascadeOnDelete();
            $table->string('site_name');
            $table->date('post_date');
            $table->date('end_date');
            $table->string('link');
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
        Schema::dropIfExists('hiring_request_postings');
    }
}