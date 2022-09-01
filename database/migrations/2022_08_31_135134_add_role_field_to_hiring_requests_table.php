<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddRoleFieldToHiringRequestsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('hiring_requests', function (Blueprint $table) {
            $table->foreignId('role')
                ->after('job_title')
                ->nullable()
                ->references('id')
                ->on('roles')
                ->constrained()
                ->cascadeOnDelete();

            if (Schema::hasColumn('hiring_requests', 'user_id')) {
                $table->dropConstrainedForeignId('user_id');
            }
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
            $table->dropConstrainedForeignId('role');
        });
    }
}