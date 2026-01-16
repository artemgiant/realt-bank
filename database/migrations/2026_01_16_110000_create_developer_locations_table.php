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
        Schema::create('developer_locations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('developer_id')
                ->constrained('developers')
                ->cascadeOnDelete()
                ->comment('ID девелопера');

            // Тип локации: country, state, city
            $table->enum('location_type', ['country', 'state', 'city'])
                ->comment('Тип локации');

            // ID локации (полиморфная связь без FK для гибкости)
            $table->unsignedBigInteger('location_id')
                ->comment('ID локации в соответствующей таблице');

            // Кешированные данные для отображения
            $table->string('location_name')
                ->comment('Название локации');
            $table->string('full_location_name')
                ->nullable()
                ->comment('Полное название с родителем');

            $table->timestamps();

            // Уникальность: один девелопер не может иметь дублирующие локации
            $table->unique(['developer_id', 'location_type', 'location_id'], 'developer_location_unique');

            // Индексы
            $table->index(['location_type', 'location_id'], 'location_type_id_index');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('developer_locations');
    }
};
