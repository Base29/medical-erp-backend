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
            $table->boolean('is_dbs_required')->default(0);
            $table->boolean('self_declaration_completed')->default(0);
            $table->string('self_declaration_certificate');
            $table->boolean('is_dbs_conducted')->default(0);
            $table->date('dbs_conducted_date');
            $table->date('follow_up_date');
            $table->string('dbs_certificate');
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