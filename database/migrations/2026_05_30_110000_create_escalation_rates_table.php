<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('escalation_rates', function (Blueprint $table) {
            $table->id();
            $table->decimal('monthly_rate_percent', 5, 3)
                ->default(0.500)
                ->comment('Monthly price escalation % applied to historic rates. Default 0.5% per month.');
            $table->string('notes', 500)
                ->nullable()
                ->comment('QS notes on how this rate was determined.');
            $table->foreignId('updated_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('escalation_rates');
    }
};
