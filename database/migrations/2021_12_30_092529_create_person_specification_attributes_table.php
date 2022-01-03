<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePersonSpecificationAttributesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('person_specification_attributes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('person_specification_id')->constrained()->cascadeOnDelete();
            $table->string('attribute')->nullable();
            $table->string('essential')->nullable();
            $table->string('desirable')->nullable();
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
        Schema::dropIfExists('person_specification_attributes');
    }
}