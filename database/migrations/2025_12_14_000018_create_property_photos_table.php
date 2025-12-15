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
        if (!Schema::hasTable('property_photos')) {
            Schema::create('property_photos', function (Blueprint $table) {
                $table->id();
                $table->foreignId('property_id')->constrained()->cascadeOnDelete()->comment('Недвижимость');
                $table->string('path')->comment('Путь файла');
                $table->string('filename')->comment('Имя файла');
                $table->integer('sort_order')->default(0)->comment('Порядок сортировки');
                $table->boolean('is_main')->default(false)->comment('Основное фото');
                $table->timestamps();

                // Індекси
                $table->index(['property_id', 'sort_order']);
                $table->index(['property_id', 'is_main']);
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('property_photos');
    }
};
