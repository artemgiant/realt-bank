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
        Schema::table('blocks', function (Blueprint $table) {
            // Этажность и год сдачи (эти поля есть в fillable модели, но отсутствуют в таблице)
            $table->integer('floors_total')->nullable()->after('building_number')->comment('Количество этажей');
            $table->integer('year_built')->nullable()->after('floors_total')->comment('Год сдачи');

            // Справочники
            $table->unsignedBigInteger('heating_type_id')->nullable()->after('year_built')->comment('Тип отопления');
            $table->unsignedBigInteger('wall_type_id')->nullable()->after('heating_type_id')->comment('Тип стен');

            // Файлы
            $table->string('plan_path')->nullable()->after('wall_type_id')->comment('Путь к плану блока');

            // FK
            $table->foreign('heating_type_id', 'blocks_heating_type_id_foreign')
                ->references('id')
                ->on('dictionaries')
                ->onDelete('set null');

            $table->foreign('wall_type_id', 'blocks_wall_type_id_foreign')
                ->references('id')
                ->on('dictionaries')
                ->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('blocks', function (Blueprint $table) {
            $table->dropForeign('blocks_heating_type_id_foreign');
            $table->dropForeign('blocks_wall_type_id_foreign');

            $table->dropColumn([
                'floors_total',
                'year_built',
                'heating_type_id',
                'wall_type_id',
                'plan_path',
            ]);
        });
    }
};
