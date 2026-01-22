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
        // Добавляем JSON поля для хранения фото и планов
        Schema::table('complexes', function (Blueprint $table) {
            $table->json('photos')->nullable()->after('logo_path')->comment('Фото комплекса (JSON массив путей)');
            $table->json('plans')->nullable()->after('photos')->comment('Планы комплекса (JSON массив путей)');
        });

        // Удаляем таблицу complex_media
        Schema::dropIfExists('complex_media');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Удаляем JSON поля
        Schema::table('complexes', function (Blueprint $table) {
            $table->dropColumn(['photos', 'plans']);
        });

        // Восстанавливаем таблицу complex_media
        Schema::create('complex_media', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('complex_id');
            $table->string('type')->comment('photo | plan');
            $table->string('path');
            $table->integer('sort_order')->default(0);
            $table->timestamps();

            $table->foreign('complex_id')
                ->references('id')
                ->on('complexes')
                ->onDelete('cascade');
        });
    }
};
