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
        Schema::create('regional_rates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('region_id')->constrained('regions');
            $table->foreignId('estimating_element_id')->constrained('estimating_elements');
            $table->decimal('low_rate', 10, 2)->nullable();
            $table->decimal('medium_rate', 10, 2)->nullable();
            $table->decimal('high_rate', 10, 2)->nullable();
            $table->decimal('high_plus_rate', 10, 2)->nullable();
            $table->integer('project_count')->default(0);
            $table->string('min_project_nr')->nullable();
            $table->string('max_project_nr')->nullable();
            $table->date('data_from_date')->nullable();
            $table->date('data_to_date')->nullable();
            $table->timestamp('calculated_at')->nullable();
            $table->timestamps();
            $table->unique(['region_id', 'estimating_element_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('regional_rates');
    }
};
