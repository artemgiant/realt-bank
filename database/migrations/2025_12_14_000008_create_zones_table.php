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
        if (!Schema::hasTable('zones')) {
            Schema::create('zones', function (Blueprint $table) {
                $table->id();
                $table->foreignId('city_id')->constrained()->cascadeOnDelete()->comment('Город');
                $table->foreignId('district_id')->nullable()->constrained()->nullOnDelete()->comment('Район');
                $table->string('name')->comment('Название зоны');
                $table->string('slug')->comment('URL идентификатор');
                $table->boolean('is_active')->default(true)->comment('Активность');
                $table->timestamps();

                // Індекси
                $table->unique(['city_id', 'slug']);
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('zones');
    }
};
