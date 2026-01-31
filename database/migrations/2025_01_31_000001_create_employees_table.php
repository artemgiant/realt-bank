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
        Schema::create('employees', function (Blueprint $table) {
            $table->id()->comment('ID сотрудника');

            // Связи
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete()->comment('Пользователь CRM');
            $table->foreignId('company_id')->nullable()->constrained('companies')->nullOnDelete()->comment('Компания');
            $table->foreignId('office_id')->nullable()->constrained('company_offices')->nullOnDelete()->comment('Офис');
            $table->foreignId('position_id')->nullable()->constrained('dictionaries')->nullOnDelete()->comment('Должность');
            $table->foreignId('status_id')->nullable()->constrained('dictionaries')->nullOnDelete()->comment('Статус');
            $table->json('tag_ids')->nullable()->comment('Теги (массив ID из agent_tag)');

            // Персональные данные
            $table->string('first_name')->comment('Имя');
            $table->string('last_name')->comment('Фамилия');
            $table->string('middle_name')->nullable()->comment('Отчество');
            $table->string('email')->nullable()->comment('Email');
            $table->string('phone')->nullable()->comment('Телефон');
            $table->date('birthday')->nullable()->comment('Дата рождения');

            // Документы
            $table->string('passport')->nullable()->comment('Паспорт');
            $table->string('inn')->nullable()->comment('ИНН');

            // Дополнительно
            $table->text('comment')->nullable()->comment('Комментарий');
            $table->string('photo_path')->nullable()->comment('Путь к фото');

            // Активность
            $table->timestamp('active_until')->nullable()->comment('Активен до');
            $table->boolean('is_active')->default(true)->comment('Активен');

            // Системные
            $table->timestamps();
            $table->softDeletes();

            // Индексы
            $table->index(['company_id', 'office_id']);
            $table->index('is_active');
            $table->index('email');
            $table->index('phone');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('employees');
    }
};
