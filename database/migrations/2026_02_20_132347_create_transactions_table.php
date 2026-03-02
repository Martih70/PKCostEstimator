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
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->date('transaction_date');
            $table->string('raw_pd_code');
            $table->foreignId('pd_code_id')->nullable()->constrained('pd_codes');
            $table->string('raw_area_code')->nullable();
            $table->string('raw_ac_code_mh')->nullable();
            $table->foreignId('project_id')->nullable()->constrained('projects');
            $table->string('raw_project_nr');
            $table->text('item_description');
            $table->decimal('amount', 12, 2);
            $table->string('import_batch')->nullable();
            $table->timestamps();
            $table->index('transaction_date');
            $table->index('project_id');
            $table->index('pd_code_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};
