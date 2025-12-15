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
        if (!Schema::hasTable('complexes')) {
            Schema::create('complexes', function (Blueprint $table) {
                $table->id();
                $table->foreignId('developer_id')->nullable()->constrained()->nullOnDelete()->comment('Разработчик');
                $table->string('name')->comment('Название комплекса');
                $table->string('slug')->unique()->comment('URL идентификатор');
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
        Schema::dropIfExists('complexes');
    }
};
