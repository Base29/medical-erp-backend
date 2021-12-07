<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDbsChecksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('dbs_checks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->boolean('is_dbs_required')->default(null)->nullable();
            $table->boolean('self_declaration_completed')->default(null)->nullable();
            $table->string('self_declaration_certificate')->nullable();
            $table->boolean('is_dbs_conducted')->default(null)->nullable();
            $table->date('dbs_conducted_date')->nullable();
            $table->date('follow_up_date')->nullable();
            $table->string('dbs_certificate')->nullable();
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
        Schema::dropIfExists('dbs_checks');
    }
}