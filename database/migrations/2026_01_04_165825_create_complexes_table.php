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
        // Удаляем FK из таблицы properties (если существует)
        if (Schema::hasTable('properties')) {
            $this->dropForeignKeyIfExists('properties', 'properties_complex_id_foreign');
        }

        // Удаляем FK из таблицы blocks (если существует)
        if (Schema::hasTable('blocks')) {
            $this->dropForeignKeyIfExists('blocks', 'blocks_complex_id_foreign');
        }

        // Удаляем таблицу если существует
        Schema::dropIfExists('complexes');

        Schema::create('complexes', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('developer_id')->nullable()->comment('Застройщик');
            $table->integer('city_id')->nullable()->comment('Город');
            $table->integer('district_id')->nullable()->comment('Район');
            $table->integer('zone_id')->nullable()->comment('Зона');
            $table->string('name')->comment('Название комплекса');
            $table->string('slug')->comment('URL идентификатор');
            $table->text('description')->nullable()->comment('Описание');
            $table->boolean('is_active')->default(true)->comment('Активность');
            $table->timestamps();
            $table->softDeletes();

            // Индексы
            $table->unique(
                ['developer_id', 'city_id', 'district_id', 'zone_id', 'slug'],
                'complexes_developer_city_district_zone_slug_unique'
            );
            $table->index('city_id', 'complexes_city_id_foreign');
            $table->index('district_id', 'complexes_district_id_foreign');
            $table->index('zone_id', 'complexes_zone_id_foreign');

            // Foreign Keys
            $table->foreign('developer_id', 'complexes_developer_id_foreign')
                ->references('id')
                ->on('developers')
                ->onDelete('set null');

            $table->foreign('city_id', 'complexes_city_id_foreign')
                ->references('id')
                ->on('cities')
                ->onDelete('set null');

            $table->foreign('district_id', 'complexes_district_id_foreign')
                ->references('id')
                ->on('districts')
                ->onDelete('set null');

            $table->foreign('zone_id', 'complexes_zone_id_foreign')
                ->references('id')
                ->on('zones')
                ->onDelete('set null');
        });

        // Восстанавливаем FK в таблице properties
        if (Schema::hasTable('properties')) {
            Schema::table('properties', function (Blueprint $table) {
                $table->foreign('complex_id', 'properties_complex_id_foreign')
                    ->references('id')
                    ->on('complexes')
                    ->onDelete('set null');
            });
        }

        // Восстанавливаем FK в таблице blocks
        if (Schema::hasTable('blocks')) {
            Schema::table('blocks', function (Blueprint $table) {
                $table->foreign('complex_id', 'blocks_complex_id_foreign')
                    ->references('id')
                    ->on('complexes')
                    ->onDelete('cascade');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('complexes');
    }

    /**
     * Удаляет FK если существует
     */
    protected function dropForeignKeyIfExists(string $table, string $foreignKey): void
    {
        $exists = \DB::select("
            SELECT CONSTRAINT_NAME 
            FROM information_schema.TABLE_CONSTRAINTS 
            WHERE CONSTRAINT_TYPE = 'FOREIGN KEY' 
            AND TABLE_SCHEMA = DATABASE() 
            AND TABLE_NAME = ? 
            AND CONSTRAINT_NAME = ?
        ", [$table, $foreignKey]);

        if (!empty($exists)) {
            Schema::table($table, function (Blueprint $table) use ($foreignKey) {
                $table->dropForeign($foreignKey);
            });
        }
    }
};
