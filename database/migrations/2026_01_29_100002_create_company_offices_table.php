<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('company_offices', function (Blueprint $table) {
            $table->id();

            // Связь с компанией
            $table->unsignedBigInteger('company_id')->comment('ID компании');

            // Название офиса
            $table->string('name')->comment('Название офиса');

            // Локация офиса - используем integer для совместимости
            $table->unsignedBigInteger('country_id')->nullable()->comment('Страна');
            $table->unsignedBigInteger('state_id')->nullable()->comment('Регион');
            $table->integer('city_id')->nullable()->comment('Город');
            $table->integer('district_id')->nullable()->comment('Район');
            $table->integer('zone_id')->nullable()->comment('Зона');
            $table->integer('street_id')->nullable()->comment('Улица');
            $table->string('building_number', 50)->nullable()->comment('Номер дома');
            $table->string('office_number', 50)->nullable()->comment('Номер офиса');

            // Кешированные данные локации для отображения
            $table->string('full_address')->nullable()->comment('Полный адрес (кеш)');

            // Настройки
            $table->boolean('is_active')->default(true)->comment('Активность');
            $table->integer('sort_order')->default(0)->comment('Порядок сортировки');

            $table->timestamps();
            $table->softDeletes();

            // Индексы
            $table->index(['company_id', 'is_active']);

            // Foreign Keys
            $table->foreign('company_id', 'company_offices_company_id_foreign')
                ->references('id')
                ->on('companies')
                ->onDelete('cascade');

            $table->foreign('country_id', 'company_offices_country_id_foreign')
                ->references('id')
                ->on('countries')
                ->onDelete('set null');

            $table->foreign('state_id', 'company_offices_state_id_foreign')
                ->references('id')
                ->on('states')
                ->onDelete('set null');

            $table->foreign('city_id', 'company_offices_city_id_foreign')
                ->references('id')
                ->on('cities')
                ->onDelete('set null');

            $table->foreign('district_id', 'company_offices_district_id_foreign')
                ->references('id')
                ->on('districts')
                ->onDelete('set null');

            $table->foreign('zone_id', 'company_offices_zone_id_foreign')
                ->references('id')
                ->on('zones')
                ->onDelete('set null');

            $table->foreign('street_id', 'company_offices_street_id_foreign')
                ->references('id')
                ->on('streets')
                ->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('company_offices');
    }
};
