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
        if (!Schema::hasTable('contacts')) {
            Schema::create('contacts', function (Blueprint $table) {
                $table->id();
                $table->string('name')->comment('Имя контакта');
                $table->string('role')->nullable()->comment('Должность');
                $table->string('phone', 50)->comment('Основной телефон');
                $table->string('phone_additional', 50)->nullable()->comment('Доп. телефон');
                $table->string('email')->nullable()->comment('Email адрес');
                $table->boolean('has_whatsapp')->default(false)->comment('WhatsApp');
                $table->boolean('has_viber')->default(false)->comment('Viber');
                $table->boolean('has_telegram')->default(false)->comment('Telegram');
                $table->text('notes')->nullable()->comment('Примечания');
                $table->timestamps();

                // Індекси
                $table->index('phone');
                $table->index('name');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('contacts');
    }
};
