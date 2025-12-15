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
        if (!Schema::hasTable('property_translations')) {
            Schema::create('property_translations', function (Blueprint $table) {
                $table->id();
                $table->foreignId('property_id')->constrained()->cascadeOnDelete()->comment('Недвижимость');
                $table->string('locale', 2)->comment('Язык');
                $table->string('title')->comment('Заголовок');
                $table->text('description')->nullable()->comment('Описание');
                $table->timestamps();

                // Індекси
                $table->unique(['property_id', 'locale']);
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('property_translations');
    }
};
