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
        // Re-applies the rule from 2026_02_21_102424_update_existing_projects_to_historical
        // for any rows that were imported after that one-time migration already ran
        // (e.g. on environments where historical data was loaded post-deploy and
        // is still stuck on the project_type column's 'forecast' default).
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
        // No-op: reverting would incorrectly mark genuinely historical projects
        // as forecast again, and there's no way to distinguish which rows this
        // migration actually changed.
    }
};
