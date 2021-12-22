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
            $table->boolean('is_nurse')->default(0);
            $table->string('name')->nullable();
            $table->string('location')->nullable();
            $table->date('expiry_date')->nullable();
            $table->string('registration_status')->nullable();
            $table->string('register_entry')->nullable();
            $table->string('nmc_document')->nullable();
            $table->string('gms_reference_number')->nullable();
            $table->date('gp_register_date')->nullable();
            $table->string('specialist_register')->nullable();
            $table->date('provisional_registration_date')->nullable();
            $table->date('full_registration_date')->nullable();
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