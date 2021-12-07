<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateNationalitiesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('nationalities', function (Blueprint $table) {
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
            $table->boolean('visa_required')->nullable();
            $table->string('visa_number')->nullable();
            $table->date('visa_start_date')->nullable();
            $table->date('visa_expiry_date')->nullable();
            $table->string('restrictions')->nullable();
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
        Schema::dropIfExists('nationalities');
    }
}