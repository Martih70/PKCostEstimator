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
        Schema::create('pd_codes', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique();
            $table->string('name');
            $table->string('area_code')->nullable();
            $table->integer('mh_sort_order')->nullable();
            $table->foreignId('estimating_element_id')->nullable()->constrained('estimating_elements');
            $table->enum('category', ['construction', 'externals', 'overhead', 'excluded']);
            $table->boolean('is_contractor')->default(false);
            $table->timestamps();
            $table->index('estimating_element_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pd_codes');
    }
};
