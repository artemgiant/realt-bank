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
        if (!Schema::hasTable('cities')) {
            Schema::create('cities', function (Blueprint $table) {
                $table->id();
                $table->foreignId('region_id')->constrained()->cascadeOnDelete()->comment('Регион');
                $table->string('name')->comment('Название города');
                $table->string('slug')->comment('URL идентификатор');
                $table->boolean('is_active')->default(true)->comment('Активность');
                $table->timestamps();

                // Індекси
                $table->unique(['region_id', 'slug']);
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cities');
    }
};
