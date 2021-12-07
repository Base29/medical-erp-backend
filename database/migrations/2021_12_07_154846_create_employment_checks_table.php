<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEmploymentChecksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('employment_checks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('passport_number')->nullable();
            $table->string('passport_country_of_issue')->nullable();
            $table->date('passport_date_of_expiry')->nullable();
            $table->boolean('is_uk_citizen')->default(null)->nullable();
            $table->string('right_to_work_status')->nullable();
            $table->string('share_code')->nullable();
            $table->date('date_issued')->nullable();
            $table->date('date_checked')->nullable();
            $table->date('expiry_date')->nullable();
            $table->boolean('visa_required')->default(null)->nullable();
            $table->string('visa_number')->nullable();
            $table->date('visa_start_date')->nullable();
            $table->date('visa_expiry_date')->nullable();
            $table->string('restrictions')->nullable();
            $table->boolean('is_dbs_required')->default(null)->nullable();
            $table->boolean('self_declaration_completed')->default(null)->nullable();
            $table->string('self_declaration_certificate')->nullable();
            $table->boolean('is_dbs_conducted')->default(null)->nullable();
            $table->date('dbs_conducted_date')->nullable();
            $table->date('follow_up_date')->nullable();
            $table->string('dbs_certificate')->nullable();
            $table->string('driving_license_number')->nullable();
            $table->string('driving_license_country_of_issue')->nullable();
            $table->string('driving_license_class')->nullable();
            $table->date('driving_license_date_of_expiry')->nullable();
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
        Schema::dropIfExists('employment_checks');
    }
}