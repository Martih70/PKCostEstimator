<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('projects', function (Blueprint $table) {
            $table->string('project_id')->nullable()->unique();
            $table->string('unique_id')->nullable()->unique();
            $table->decimal('budget_cost', 15, 2)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('projects', function (Blueprint $table) {
            $table->dropUnique(['project_id']);
            $table->dropUnique(['unique_id']);
            $table->dropColumn(['project_id', 'unique_id', 'budget_cost']);
        });
    }
};
