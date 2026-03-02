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
        Schema::create('projects', function (Blueprint $table) {
            $table->id();
            $table->string('project_nr')->unique();
            $table->string('name');
            $table->string('location')->nullable();
            $table->string('city_area')->nullable();
            $table->foreignId('region_id')->nullable()->constrained('regions');
            $table->decimal('gross_floor_area', 8, 2)->nullable();
            $table->text('notes')->nullable();
            $table->boolean('exclude_from_estimator')->default(false);
            $table->boolean('is_overhead_centre')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('projects');
    }
};
