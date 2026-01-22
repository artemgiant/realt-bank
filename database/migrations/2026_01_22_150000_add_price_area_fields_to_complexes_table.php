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
        Schema::table('complexes', function (Blueprint $table) {
            // Площадь
            $table->decimal('area_from', 10, 2)->nullable()->after('logo_path')->comment('Площадь от (м²)');
            $table->decimal('area_to', 10, 2)->nullable()->after('area_from')->comment('Площадь до (м²)');

            // Цена
            $table->decimal('price_per_m2', 15, 2)->nullable()->after('area_to')->comment('Цена от за м²');
            $table->decimal('price_total', 15, 2)->nullable()->after('price_per_m2')->comment('Цена за объект');
            $table->string('currency', 3)->default('USD')->after('price_total')->comment('Валюта (USD, UAH, EUR)');

            // Категория и тип объекта
            $table->unsignedBigInteger('category_id')->nullable()->after('currency')->comment('Категория');
            $table->unsignedBigInteger('object_type_id')->nullable()->after('category_id')->comment('Тип объекта');
            $table->integer('objects_count')->nullable()->after('object_type_id')->comment('Количество объектов');

            // Состояния (JSON массив ID из dictionaries)
            $table->json('conditions')->nullable()->after('objects_count')->comment('Состояния (массив ID)');

            // Особенности (JSON массив ID из dictionaries)
            $table->json('features')->nullable()->after('conditions')->comment('Особенности (массив ID)');

            // FK
            $table->foreign('category_id', 'complexes_category_id_foreign')
                ->references('id')
                ->on('dictionaries')
                ->onDelete('set null');

            $table->foreign('object_type_id', 'complexes_object_type_id_foreign')
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
        Schema::table('complexes', function (Blueprint $table) {
            $table->dropForeign('complexes_category_id_foreign');
            $table->dropForeign('complexes_object_type_id_foreign');

            $table->dropColumn([
                'area_from',
                'area_to',
                'price_per_m2',
                'price_total',
                'currency',
                'category_id',
                'object_type_id',
                'objects_count',
                'conditions',
                'features',
            ]);
        });
    }
};
