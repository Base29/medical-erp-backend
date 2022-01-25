<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddFieldsToHiringRequestsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('hiring_requests', function (Blueprint $table) {
            $table->string('status')->nullable()->after('rota_information');
            $table->string('decision_reason')->nullable()->after('status');
            $table->text('decision_comment')->nullable()->after('decision_reason');
            $table->dropColumn('is_approved');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('hiring_requests', function (Blueprint $table) {
            $table->dropColumn('status');
            $table->dropColumn('decision_reason');
            $table->dropColumn('decision_comment');
            $table->boolean('is_approved')->default(0)->after('rota_information');
        });
    }
}