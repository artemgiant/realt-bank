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
        if (!Schema::hasTable('properties')) {
            Schema::create('properties', function (Blueprint $table) {
                $table->id();

                // Зв'язки
                $table->foreignId('user_id')->constrained()->cascadeOnDelete()->comment('Пользователь');
                $table->foreignId('contact_id')->nullable()->constrained()->nullOnDelete()->comment('Контакт');
                $table->foreignId('source_id')->nullable()->constrained()->nullOnDelete()->comment('Источник');
                $table->foreignId('currency_id')->constrained()->comment('Валюта');

                // Комплекс
                $table->foreignId('complex_id')->nullable()->constrained()->nullOnDelete()->comment('Комплекс');
                $table->foreignId('section_id')->nullable()->constrained()->nullOnDelete()->comment('Секция');

                // Локація (якщо без комплексу)
                $table->foreignId('country_id')->nullable()->constrained()->nullOnDelete()->comment('Страна');
                $table->foreignId('region_id')->nullable()->constrained()->nullOnDelete()->comment('Регион');
                $table->foreignId('city_id')->nullable()->constrained()->nullOnDelete()->comment('Город');
                $table->foreignId('district_id')->nullable()->constrained()->nullOnDelete()->comment('Район');
                $table->foreignId('zone_id')->nullable()->constrained()->nullOnDelete()->comment('Зона');
                $table->foreignId('street_id')->nullable()->constrained()->nullOnDelete()->comment('Улица');
                $table->foreignId('landmark_id')->nullable()->constrained()->nullOnDelete()->comment('Ориентир');
                $table->string('building_number', 50)->nullable()->comment('Номер здания');
                $table->string('apartment_number', 50)->nullable()->comment('Номер квартиры');
                $table->string('location_name')->nullable()->comment('Название локации');
                $table->decimal('latitude', 10, 7)->nullable()->comment('Широта');
                $table->decimal('longitude', 10, 7)->nullable()->comment('Долгота');

                // Довідники (dictionaries)
                $table->foreignId('deal_type_id')->constrained('dictionaries')->comment('Тип сделки');
                $table->foreignId('deal_kind_id')->nullable()->constrained('dictionaries')->nullOnDelete()->comment('Вид сделки');
                $table->foreignId('building_type_id')->nullable()->constrained('dictionaries')->nullOnDelete()->comment('Тип здания');
                $table->foreignId('property_type_id')->nullable()->constrained('dictionaries')->nullOnDelete()->comment('Тип недвижимости');
                $table->foreignId('condition_id')->nullable()->constrained('dictionaries')->nullOnDelete()->comment('Состояние');
                $table->foreignId('wall_type_id')->nullable()->constrained('dictionaries')->nullOnDelete()->comment('Тип стен');
                $table->foreignId('heating_type_id')->nullable()->constrained('dictionaries')->nullOnDelete()->comment('Отопление');
                $table->foreignId('room_count_id')->nullable()->constrained('dictionaries')->nullOnDelete()->comment('Количество комнат');
                $table->foreignId('bathroom_count_id')->nullable()->constrained('dictionaries')->nullOnDelete()->comment('Ванные комнаты');
                $table->foreignId('ceiling_height_id')->nullable()->constrained('dictionaries')->nullOnDelete()->comment('Высота потолков');

                // Характеристики
                $table->decimal('area_total', 10, 2)->nullable()->comment('Общая площадь');
                $table->decimal('area_living', 10, 2)->nullable()->comment('Жилая площадь');
                $table->decimal('area_kitchen', 10, 2)->nullable()->comment('Площадь кухни');
                $table->decimal('area_land', 10, 2)->nullable()->comment('Площадь земли');
                $table->integer('floor')->nullable()->comment('Этаж');
                $table->integer('floors_total')->nullable()->comment('Всего этажей');
                $table->integer('year_built')->nullable()->comment('Год постройки');

                // Ціна
                $table->decimal('price', 15, 2)->nullable()->comment('Цена');
                $table->decimal('commission', 10, 2)->nullable()->comment('Комиссия');
                $table->string('commission_type', 20)->default('percent')->comment('Тип комиссии');

                // Медіа
                $table->string('youtube_url')->nullable()->comment('YouTube видео');
                $table->string('external_url')->nullable()->comment('Внешняя ссылка');

                // Налаштування
                $table->boolean('is_visible_to_agents')->default(false)->comment('Видна агентам');
                $table->text('notes')->nullable()->comment('Описание');
                $table->text('agent_notes')->nullable()->comment('Заметки агента');
                $table->string('status', 20)->default('draft')->comment('Статус');

                // Timestamps
                $table->timestamps();
                $table->softDeletes();

                // Індекси
                $table->index('status');
                $table->index(['user_id', 'status']);
                $table->index(['city_id', 'status']);
                $table->index(['deal_type_id', 'status']);
                $table->index('price');
                $table->index('created_at');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('properties');
    }
};
