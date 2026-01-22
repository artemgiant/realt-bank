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
            // URL и ссылки (добавляем после is_active)
            $table->string('website')->nullable()->after('is_active')->comment('Сайт комплекса');
            $table->string('company_website')->nullable()->after('website')->comment('Сайт компании девелопера');
            $table->string('materials_url')->nullable()->after('company_website')->comment('Материалы девелопера');

            // Примечания
            $table->text('agent_notes')->nullable()->after('materials_url')->comment('Примечание для агентов');
            $table->text('special_conditions')->nullable()->after('agent_notes')->comment('Специальные условия (акции)');

            // Класс жилья (справочник)
            $table->unsignedBigInteger('housing_class_id')->nullable()->after('special_conditions')->comment('Класс жилья');

            // Мультиязычность (JSON)
            $table->json('name_translations')->nullable()->after('name')->comment('Переводы названия {ua, ru, en}');
            $table->json('description_translations')->nullable()->after('description')->comment('Переводы описания {ua, ru, en}');

            // Файлы
            $table->string('logo_path')->nullable()->after('housing_class_id')->comment('Путь к логотипу');

            // FK на dictionaries для housing_class
            $table->foreign('housing_class_id', 'complexes_housing_class_id_foreign')
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
            $table->dropForeign('complexes_housing_class_id_foreign');

            $table->dropColumn([
                'website',
                'company_website',
                'materials_url',
                'agent_notes',
                'special_conditions',
                'housing_class_id',
                'name_translations',
                'description_translations',
                'logo_path',
            ]);
        });
    }
};
