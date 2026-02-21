<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Mark all existing projects (those without project_id and unique_id) as historical
        DB::table('projects')
            ->whereNull('project_id')
            ->whereNull('unique_id')
            ->update(['project_type' => 'historical']);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Reset to forecast (default)
        DB::table('projects')
            ->where('project_type', 'historical')
            ->update(['project_type' => 'forecast']);
    }
};
