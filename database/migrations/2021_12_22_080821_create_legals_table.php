<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLegalsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('legals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->boolean('is_nurse');
            $table->string('name');
            $table->string('location');
            $table->date('expiry_date');
            $table->string('registration_status');
            $table->string('register_entry');
            $table->string('nmc_document');
            $table->string('gms_reference_number');
            $table->date('gp_register_date');
            $table->string('specialist_register');
            $table->date('provisional_registration_date');
            $table->date('full_registration_date');
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
        Schema::dropIfExists('legals');
    }
}