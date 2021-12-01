<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMiscellaneousInformationTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('miscellaneous_information', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('job_description')->nullable();
            $table->text('interview_notes')->nullable();
            $table->string('offer_letter_email')->nullable();
            $table->string('job_advertisement')->nullable();
            $table->string('health_questionnaire')->nullable();
            $table->string('annual_declaration')->nullable();
            $table->string('employee_confidentiality_agreement')->nullable();
            $table->string('employee_privacy_notice')->nullable();
            $table->string('locker_key_agreement')->nullable();
            $table->boolean('is_locker_key_assigned')->default(0);
            $table->string('equipment_provided_policy')->nullable();
            $table->string('resume')->nullable();
            $table->string('proof_of_address')->nullable();
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
        Schema::dropIfExists('miscellaneous_information');
    }
}