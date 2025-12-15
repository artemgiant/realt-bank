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
        if (!Schema::hasTable('dictionaries')) {
            Schema::create('dictionaries', function (Blueprint $table) {
                $table->id();
                $table->string('type', 50)->index()->comment('Тип словаря');
                $table->string('name')->comment('Название элемента');
                $table->string('value')->nullable()->comment('Значение');
                $table->string('slug')->comment('URL идентификатор');
                $table->integer('sort_order')->default(0)->comment('Порядок сортировки');
                $table->boolean('is_active')->default(true)->comment('Активность');
                $table->timestamps();

                // Індекси
                $table->unique(['type', 'slug']);
                $table->index(['type', 'is_active', 'sort_order']);
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('dictionaries');
    }
};
