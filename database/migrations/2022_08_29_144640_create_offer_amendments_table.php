<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOfferAmendmentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('offer_amendments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('offer')
                ->references('id')
                ->on('offers')
                ->constrained()
                ->cascadeOnDelete();
            $table->foreignId('work_pattern')
                ->references('id')
                ->on('work_patterns')
                ->constrained()
                ->cascadeOnDelete();
            $table->decimal('amount', 5, 2)->nullable();
            $table->integer('status')->nullable();
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
        Schema::dropIfExists('offer_amendments');
    }
}