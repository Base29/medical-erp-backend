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
            $table->string('passport_number');
            $table->string('passport_country_of_issue');
            $table->date('passport_date_of_expiry');
            $table->boolean('is_uk_citizen')->default(0);
            $table->string('right_to_work_status');
            $table->string('share_code');
            $table->date('date_issued');
            $table->date('date_checked');
            $table->date('expiry_date');
            $table->boolean('visa_required');
            $table->string('visa_number');
            $table->date('visa_start_date');
            $table->date('visa_expiry_date');
            $table->string('restrictions');
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