<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Pivot таблица для связи многие-ко-многим между Property и Contact
     */
    public function up(): void
    {
        Schema::create('property_contact', function (Blueprint $table) {
            $table->id();
            $table->foreignId('property_id')
                ->constrained('properties')
                ->cascadeOnDelete()
                ->comment('ID объекта недвижимости');
            $table->foreignId('contact_id')
                ->constrained('contacts')
                ->cascadeOnDelete()
                ->comment('ID контакта');
            $table->timestamps();

            // Уникальный индекс для предотвращения дублей
            $table->unique(['property_id', 'contact_id']);

            // Индексы для быстрого поиска
            $table->index('property_id');
            $table->index('contact_id');
        });

        // Удаляем старую колонку contact_id из properties (если существует)
        if (Schema::hasColumn('properties', 'contact_id')) {
            Schema::table('properties', function (Blueprint $table) {
                $table->dropForeign(['contact_id']);
                $table->dropColumn('contact_id');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('property_contact');

        // Восстанавливаем колонку contact_id в properties
        Schema::table('properties', function (Blueprint $table) {
            $table->foreignId('contact_id')
                ->nullable()
                ->after('user_id')
                ->constrained('contacts')
                ->nullOnDelete();
        });
    }
};
