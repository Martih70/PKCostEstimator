<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('regions', function (Blueprint $table) {
            $table->decimal('cost_adjustment_factor', 5, 4)
                ->default(1.0000)
                ->after('slug')
                ->comment('Regional cost multiplier. 1.0 = base rate. 1.15 = 15% above base. 0.85 = 15% below.');
        });
    }

    public function down(): void
    {
        Schema::table('regions', function (Blueprint $table) {
            $table->dropColumn('cost_adjustment_factor');
        });
    }
};
