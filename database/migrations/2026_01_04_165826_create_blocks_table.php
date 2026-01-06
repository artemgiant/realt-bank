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
            $this->dropForeignKeyIfExists('properties', 'properties_block_id_foreign');
        }

        // Удаляем таблицу если существует
        Schema::dropIfExists('blocks');

        Schema::create('blocks', function (Blueprint $table) {
            $table->id();
            $table->string('name')->comment('Название блока');
            $table->string('slug')->comment('URL идентификатор');
            $table->unsignedBigInteger('complex_id')->comment('Комплекс');
            $table->unsignedBigInteger('developer_id')->nullable()->comment('Застройщик');
            $table->integer('street_id')->nullable()->comment('Улица');
            $table->string('building_number')->nullable()->comment('Номер дома');
            $table->boolean('is_active')->default(true)->comment('Активность');
            $table->timestamps();
            $table->softDeletes();

            // Индексы
            $table->unique(
                ['complex_id', 'street_id', 'slug', 'building_number'],
                'blocks_complex_street_slug_building_unique'
            );
            $table->index('complex_id', 'blocks_complex_id_index');
            $table->index('street_id', 'blocks_street_id_index');
            $table->index('is_active', 'blocks_is_active_index');
            $table->index('developer_id', 'blocks_developer_id_foreign');

            // Foreign Keys
            $table->foreign('complex_id', 'blocks_complex_id_foreign')
                ->references('id')
                ->on('complexes')
                ->onDelete('cascade');

            $table->foreign('developer_id', 'blocks_developer_id_foreign')
                ->references('id')
                ->on('developers')
                ->onDelete('set null');

            $table->foreign('street_id', 'blocks_street_id_foreign')
                ->references('id')
                ->on('streets')
                ->onDelete('set null');
        });

        // Восстанавливаем FK в таблице properties
        if (Schema::hasTable('properties')) {
            Schema::table('properties', function (Blueprint $table) {
                $table->foreign('block_id', 'properties_block_id_foreign')
                    ->references('id')
                    ->on('blocks')
                    ->onDelete('set null');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('blocks');
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
