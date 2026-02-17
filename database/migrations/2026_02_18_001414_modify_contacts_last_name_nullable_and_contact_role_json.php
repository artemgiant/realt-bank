<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Карта старых ENUM значений contact_type на slug в dictionaries
     */
    private array $typeToSlugMap = [
        'owner' => 'owner',
        'agent' => 'agent',
        'developer' => 'developer',
        'developer_representative' => 'developer_representative',
    ];

    /**
     * Run the migrations.
     *
     * 1. last_name -> nullable
     * 2. contact_type (ENUM) -> contact_role (JSON с ID из dictionaries)
     */
    public function up(): void
    {
        // 1. Сделать last_name nullable
        Schema::table('contacts', function (Blueprint $table) {
            $table->string('last_name')->nullable()->change();
        });

        // 2. Добавить новое JSON поле contact_role
        Schema::table('contacts', function (Blueprint $table) {
            $table->json('contact_role')->nullable()->after('email')
                ->comment('Роли контакта: массив ID из dictionaries (type=contact_role)');
        });

        // 3. Миграция данных: contact_type (ENUM) -> contact_role (JSON массив ID)
        $contacts = DB::table('contacts')->whereNotNull('contact_type')->get();

        foreach ($contacts as $contact) {
            $slug = $this->typeToSlugMap[$contact->contact_type] ?? null;

            if ($slug) {
                // Находим ID роли в dictionaries по slug и type
                $dictionary = DB::table('dictionaries')
                    ->where('type', 'contact_role')
                    ->where('slug', $slug)
                    ->first();

                if ($dictionary) {
                    DB::table('contacts')
                        ->where('id', $contact->id)
                        ->update(['contact_role' => json_encode([$dictionary->id])]);
                }
            }
        }

        // 4. Удалить старое поле contact_type (ENUM)
        Schema::table('contacts', function (Blueprint $table) {
            $table->dropColumn('contact_type');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // 1. Вернуть contact_type (ENUM)
        Schema::table('contacts', function (Blueprint $table) {
            $table->enum('contact_type', ['owner', 'agent', 'developer', 'developer_representative'])
                ->nullable()
                ->after('email')
                ->comment('Тип контакта: Владелец/Агент/Девелопер');
        });

        // 2. Миграция данных обратно: contact_role (JSON) -> contact_type (ENUM)
        $contacts = DB::table('contacts')->whereNotNull('contact_role')->get();

        foreach ($contacts as $contact) {
            $roleIds = json_decode($contact->contact_role, true);

            if (!empty($roleIds)) {
                // Берём первый ID и находим slug
                $dictionary = DB::table('dictionaries')
                    ->where('id', $roleIds[0])
                    ->where('type', 'contact_role')
                    ->first();

                if ($dictionary && isset($this->typeToSlugMap[$dictionary->slug])) {
                    DB::table('contacts')
                        ->where('id', $contact->id)
                        ->update(['contact_type' => $dictionary->slug]);
                }
            }
        }

        // 3. Удалить contact_role (JSON)
        Schema::table('contacts', function (Blueprint $table) {
            $table->dropColumn('contact_role');
        });

        // 4. Вернуть last_name как NOT NULL
        Schema::table('contacts', function (Blueprint $table) {
            $table->string('last_name')->nullable(false)->change();
        });
    }
};
