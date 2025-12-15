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
        if (!Schema::hasTable('currencies')) {
            Schema::create('currencies', function (Blueprint $table) {
                $table->id();
                $table->string('code', 3)->unique()->comment('ISO 4217 код валюты');
                $table->string('symbol', 10)->comment('Символ валюты для отображения');
                $table->string('name', 100)->comment('Полное название валюты');
                $table->boolean('is_default')->default(false)->comment('Валюта по умолчанию');
                $table->boolean('is_active')->default(true)->comment('Статус активности валюты');
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('currencies');
    }
};
