<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Создание таблицы regions (Районы области) и добавление region_id в cities и properties
     */
    public function up(): void
    {
        // 1. Создаём таблицу regions
        if (!Schema::hasTable('regions')) {
            Schema::create('regions', function (Blueprint $table) {
                $table->id();
                $table->foreignId('state_id')->constrained()->cascadeOnDelete()->comment('Область');
                $table->string('name')->comment('Название района области');
                $table->boolean('is_active')->default(true)->comment('Активность');
                $table->timestamps();
                $table->softDeletes();

                $table->index(['state_id', 'is_active']);
            });
        }

        // 2. Добавляем region_id в cities
        if (!Schema::hasColumn('cities', 'region_id')) {
            Schema::table('cities', function (Blueprint $table) {
                $table->foreignId('region_id')->nullable()->after('state_id')->constrained()->nullOnDelete()->comment('Район области');
            });
        }

        // 3. Добавляем region_id в properties
        if (!Schema::hasColumn('properties', 'region_id')) {
            Schema::table('properties', function (Blueprint $table) {
                $table->foreignId('region_id')->nullable()->after('state_id')->constrained()->nullOnDelete()->comment('Район области');
            });
        }
    }

    /**
     * Откат миграции
     */
    public function down(): void
    {
        // Убираем region_id из properties
        if (Schema::hasColumn('properties', 'region_id')) {
            Schema::table('properties', function (Blueprint $table) {
                $table->dropForeign(['region_id']);
                $table->dropColumn('region_id');
            });
        }

        // Убираем region_id из cities
        if (Schema::hasColumn('cities', 'region_id')) {
            Schema::table('cities', function (Blueprint $table) {
                $table->dropForeign(['region_id']);
                $table->dropColumn('region_id');
            });
        }

        // Удаляем таблицу regions
        Schema::dropIfExists('regions');
    }
};
