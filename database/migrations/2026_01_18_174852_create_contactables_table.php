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
        Schema::create('contactables', function (Blueprint $table) {
            $table->id();
            $table->foreignId('contact_id')->constrained()->cascadeOnDelete();
            $table->morphs('contactable'); // contactable_id + contactable_type
            $table->string('role')->nullable(); // 'owner', 'manager', 'representative', etc.
            $table->timestamps();

            $table->unique(['contact_id', 'contactable_id', 'contactable_type'], 'contactables_unique');
        });

        // Migrate existing data from property_contact to contactables
        DB::statement("
            INSERT INTO contactables (contact_id, contactable_id, contactable_type, created_at, updated_at)
            SELECT contact_id, property_id, 'App\\\\Models\\\\Property\\\\Property', created_at, updated_at
            FROM property_contact
        ");

        // Migrate developer contacts (contact_id from developers table)
        DB::statement("
            INSERT INTO contactables (contact_id, contactable_id, contactable_type, role, created_at, updated_at)
            SELECT contact_id, id, 'App\\\\Models\\\\Reference\\\\Developer', 'primary', NOW(), NOW()
            FROM developers
            WHERE contact_id IS NOT NULL
        ");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('contactables');
    }
};
