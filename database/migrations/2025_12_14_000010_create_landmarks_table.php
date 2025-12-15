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
        if (!Schema::hasTable('landmarks')) {
            Schema::create('landmarks', function (Blueprint $table) {
                $table->id();
                $table->foreignId('city_id')->constrained()->cascadeOnDelete()->comment('Город');
                $table->string('name')->comment('Название ориентира');
                $table->string('type', 50)->default('landmark')->comment('Тип ориентира');
                $table->boolean('is_active')->default(true)->comment('Активность');
                $table->timestamps();

                // Індекси
                $table->index(['city_id', 'type']);
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('landmarks');
    }
};
