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
        Schema::create('ha_unit_completions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ha_unit_id')->constrained()->cascadeOnDelete();
            $table->string('batch');
            $table->timestamps();

            $table->unique(['ha_unit_id', 'batch']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ha_unit_completions');
    }
};
