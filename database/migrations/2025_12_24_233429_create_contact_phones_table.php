<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Таблица телефонов контактов (у одного контакта может быть несколько номеров)
     */
    public function up(): void
    {
        Schema::create('contact_phones', function (Blueprint $table) {
            $table->id();
            $table->foreignId('contact_id')
                ->constrained('contacts')
                ->cascadeOnDelete()
                ->comment('ID контакта');
            $table->string('phone', 50)->comment('Номер телефона');
            $table->boolean('is_primary')->default(false)->comment('Основной номер');
            $table->timestamps();

            // Индексы
            $table->index('contact_id');
            $table->index('phone');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('contact_phones');
    }
};
