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
        Schema::create('project_element_costs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->constrained('projects');
            $table->foreignId('estimating_element_id')->constrained('estimating_elements');
            $table->decimal('total_cost', 14, 2);
            $table->decimal('gross_floor_area', 8, 2)->nullable();
            $table->decimal('rate_per_m2', 10, 2)->nullable();
            $table->timestamp('calculated_at');
            $table->timestamps();
            $table->unique(['project_id', 'estimating_element_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('project_element_costs');
    }
};
