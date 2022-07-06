<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddSignedFieldToContractSummariesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('contract_summaries', function (Blueprint $table) {
            $table->boolean('is_signed')->after('contract_document')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('contract_summaries', function (Blueprint $table) {
            $table->dropColumn('is_signed');
        });
    }
}