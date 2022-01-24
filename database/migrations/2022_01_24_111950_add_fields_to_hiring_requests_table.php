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
            $table->boolean('is_declined')->default(0)->after('is_approved');
            $table->boolean('is_escalated')->default(0)->after('is_declined');
            $table->string('decision_reason')->nullable()->after('is_escalated');
            $table->text('decision_comment')->nullable()->after('decision_reason');
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
            $table->dropColumn('is_declined');
            $table->dropColumn('is_escalated');
            $table->dropColumn('decision_reason');
            $table->dropColumn('decision_comment');
        });
    }
}