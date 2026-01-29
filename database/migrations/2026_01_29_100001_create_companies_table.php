<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('companies', function (Blueprint $table) {
            $table->id();

            // Основное название (для поиска и отображения)
            $table->string('name')->comment('Основное название компании');
            $table->string('slug')->unique()->comment('URL идентификатор');

            // Мультиязычные названия (JSON)
            $table->json('name_translations')->nullable()->comment('Переводы названия {ua, ru, en}');

            // Мультиязычное описание (JSON)
            $table->json('description_translations')->nullable()->comment('Переводы описания {ua, ru, en}');

            // Локация компании (главный офис) - используем integer для совместимости
            $table->unsignedBigInteger('country_id')->nullable()->comment('Страна');
            $table->unsignedBigInteger('state_id')->nullable()->comment('Регион');
            $table->integer('city_id')->nullable()->comment('Город');
            $table->integer('district_id')->nullable()->comment('Район');
            $table->integer('zone_id')->nullable()->comment('Зона');
            $table->integer('street_id')->nullable()->comment('Улица');
            $table->string('building_number', 50)->nullable()->comment('Номер дома');
            $table->string('office_number', 50)->nullable()->comment('Номер офиса');

            // Основные поля
            $table->string('website')->nullable()->comment('Сайт агентства');
            $table->string('edrpou_code', 20)->nullable()->comment('Код ЕГРПОУ/ИНН');
            $table->string('company_type')->nullable()->comment('Тип агентства');
            $table->string('logo_path')->nullable()->comment('Путь к логотипу');

            // Настройки
            $table->boolean('is_active')->default(true)->comment('Активность');

            $table->timestamps();
            $table->softDeletes();

            // Индексы
            $table->index('name');
            $table->index('is_active');
            $table->index('company_type');
            $table->index('city_id', 'companies_city_id_index');
            $table->index('state_id', 'companies_state_id_index');

            // Foreign Keys
            $table->foreign('country_id', 'companies_country_id_foreign')
                ->references('id')
                ->on('countries')
                ->onDelete('set null');

            $table->foreign('state_id', 'companies_state_id_foreign')
                ->references('id')
                ->on('states')
                ->onDelete('set null');

            $table->foreign('city_id', 'companies_city_id_foreign')
                ->references('id')
                ->on('cities')
                ->onDelete('set null');

            $table->foreign('district_id', 'companies_district_id_foreign')
                ->references('id')
                ->on('districts')
                ->onDelete('set null');

            $table->foreign('zone_id', 'companies_zone_id_foreign')
                ->references('id')
                ->on('zones')
                ->onDelete('set null');

            $table->foreign('street_id', 'companies_street_id_foreign')
                ->references('id')
                ->on('streets')
                ->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('companies');
    }
};
