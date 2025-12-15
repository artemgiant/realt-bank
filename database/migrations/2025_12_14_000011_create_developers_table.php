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
        if (!Schema::hasTable('developers')) {
            Schema::create('developers', function (Blueprint $table) {
                $table->id();
                $table->string('name')->comment('Название разработчика');
                $table->string('slug')->unique()->comment('URL идентификатор');
                $table->string('phone', 50)->nullable()->comment('Телефон');
                $table->string('email')->nullable()->comment('Email');
                $table->string('website')->nullable()->comment('Веб-сайт');
                $table->text('description')->nullable()->comment('Описание');
                $table->boolean('is_active')->default(true)->comment('Активность');
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('developers');
    }
};
