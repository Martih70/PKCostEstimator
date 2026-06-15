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
        DB::table('settings')->insert([
            'key' => 'forecast_uncertainty_c',
            'value' => '2',
            'description' => 'Stage 2 uncertainty constant (C) controlling how much the Low/High forecast band widens as the number of comparable projects (n) shrinks: multiplier = 1 + C/sqrt(n).',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::table('settings')->where('key', 'forecast_uncertainty_c')->delete();
    }
};
