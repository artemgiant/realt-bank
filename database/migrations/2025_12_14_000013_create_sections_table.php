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
        if (!Schema::hasTable('sections')) {
            Schema::create('sections', function (Blueprint $table) {
                $table->id();
                $table->foreignId('complex_id')->constrained()->cascadeOnDelete()->comment('Комплекс');
                $table->string('name')->comment('Название секции');

                // Локація
                $table->foreignId('country_id')->nullable()->constrained()->nullOnDelete()->comment('Страна');
                $table->foreignId('region_id')->nullable()->constrained()->nullOnDelete()->comment('Регион');
                $table->foreignId('city_id')->nullable()->constrained()->nullOnDelete()->comment('Город');
                $table->foreignId('district_id')->nullable()->constrained()->nullOnDelete()->comment('Район');
                $table->foreignId('zone_id')->nullable()->constrained()->nullOnDelete()->comment('Зона');
                $table->foreignId('street_id')->nullable()->constrained()->nullOnDelete()->comment('Улица');
                $table->string('building_number', 50)->nullable()->comment('Номер здания');

                // Інше
                $table->integer('sort_order')->default(0)->comment('Порядок сортировки');
                $table->boolean('is_active')->default(true)->comment('Активность');
                $table->timestamps();

                // Індекси
                $table->index(['complex_id', 'sort_order']);
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sections');
    }
};
