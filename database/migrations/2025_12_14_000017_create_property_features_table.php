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
        if (!Schema::hasTable('property_features')) {
            Schema::create('property_features', function (Blueprint $table) {
                $table->foreignId('property_id')->constrained()->cascadeOnDelete()->comment('Недвижимость');
                $table->foreignId('feature_id')->constrained('dictionaries')->cascadeOnDelete()->comment('Характеристика');

                // Primary key
                $table->primary(['property_id', 'feature_id']);
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('property_features');
    }
};
