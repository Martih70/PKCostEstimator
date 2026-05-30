<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('projects', function (Blueprint $table) {
            $table->date('completion_date')
                ->nullable()
                ->after('project_start_date')
                ->comment('Actual completion date of a historical project');

            $table->string('area_source', 20)
                ->nullable()
                ->after('completion_date')
                ->comment('How GFA was determined: Drawing/Survey/Satellite/Estimate/Unknown');

            $table->string('area_verified', 10)
                ->nullable()
                ->after('area_source')
                ->comment('Whether GFA has been verified: Pending/Yes/No');
        });
    }

    public function down(): void
    {
        Schema::table('projects', function (Blueprint $table) {
            $table->dropColumn(['completion_date', 'area_source', 'area_verified']);
        });
    }
};
