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
        Schema::create('dme_sub_module_completions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('dme_sub_module_id')->constrained('dme_sub_modules')->onDelete('cascade');
            $table->string('batch');
            $table->timestamps();

            $table->unique(['dme_sub_module_id', 'batch'], 'dme_sub_mod_batch_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('dme_sub_module_completions');
    }
};
