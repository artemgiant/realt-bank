<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('contacts', function (Blueprint $table) {
            // Удаляем старый FK (nullOnDelete несовместим с NOT NULL)
            $table->dropForeign(['company_id']);

            // Делаем колонку NOT NULL
            $table->foreignId('company_id')->nullable(false)->change();

            // Новый FK с restrictOnDelete (нельзя удалить компанию, если есть контакты)
            $table->foreign('company_id')
                ->references('id')->on('companies')
                ->restrictOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('contacts', function (Blueprint $table) {
            $table->dropForeign(['company_id']);

            $table->foreignId('company_id')->nullable()->change();

            $table->foreign('company_id')
                ->references('id')->on('companies')
                ->nullOnDelete();
        });
    }
};
