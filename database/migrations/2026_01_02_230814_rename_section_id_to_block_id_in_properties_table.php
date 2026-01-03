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
        Schema::table('properties', function (Blueprint $table) {
            // Переименовываем колонку
            $table->renameColumn('section_id', 'block_id');
        });

        Schema::table('properties', function (Blueprint $table) {
            // Добавляем foreign key
            $table->foreign('block_id')->references('id')->on('blocks')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('properties', function (Blueprint $table) {
            $table->dropForeign(['block_id']);
            $table->renameColumn('block_id', 'section_id');
        });
    }
};
