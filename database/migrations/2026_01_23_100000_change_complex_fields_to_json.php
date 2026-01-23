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
            // Удаляем старые одиночные поля
            $table->dropForeign(['category_id']);
            $table->dropForeign(['object_type_id']);
            $table->dropForeign(['housing_class_id']);

            $table->dropColumn(['category_id', 'object_type_id', 'housing_class_id']);
        });

        Schema::table('complexes', function (Blueprint $table) {
            // Добавляем новые JSON поля для мульти-выбора
            $table->json('categories')->nullable()->comment('Категории комплекса (массив ID)');
            $table->json('object_types')->nullable()->comment('Типы объектов (массив ID)');
            $table->json('housing_classes')->nullable()->comment('Классы жилья (массив ID)');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('complexes', function (Blueprint $table) {
            $table->dropColumn(['categories', 'object_types', 'housing_classes']);
        });

        Schema::table('complexes', function (Blueprint $table) {
            $table->foreignId('category_id')->nullable()->constrained('dictionaries')->nullOnDelete();
            $table->foreignId('object_type_id')->nullable()->constrained('dictionaries')->nullOnDelete();
            $table->foreignId('housing_class_id')->nullable()->constrained('dictionaries')->nullOnDelete();
        });
    }
};
