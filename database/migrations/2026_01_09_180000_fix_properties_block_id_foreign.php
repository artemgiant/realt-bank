<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('properties', function (Blueprint $table) {
            // Check if the incorrect foreign key exists before trying to drop it
            if ($this->foreignKeyExists('properties', 'properties_section_id_foreign')) {
                $table->dropForeign('properties_section_id_foreign');
            }

            // Just in case the column name was still section_id (though previous migration should have renamed it),
            // but we trust the user context that it's block_id now, just with wrong FK.
            // The previous migration rename_section_id_to_block_id_in_properties_table ran, 
            // but if it failed halfway, we might be in weird state. 
            // Assuming successful rename, just wrong FK target or name lingering.

            // Clean up any other potential wrong foreign keys on block_id if they exist
            if ($this->foreignKeyExists('properties', 'properties_block_id_foreign')) {
                $table->dropForeign('properties_block_id_foreign');
            }

            // Add the correct foreign key
            $table->foreign('block_id', 'properties_block_id_foreign')
                ->references('id')
                ->on('blocks')
                ->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('properties', function (Blueprint $table) {
            $table->dropForeign('properties_block_id_foreign');

            // Restore the old one (pointing to sections) - strictly following "reverse" logic
            // though sections table might not be what we want.
            // But to be safe and reversible to previous state:
            if (Schema::hasTable('sections')) {
                $table->foreign('block_id', 'properties_section_id_foreign')
                    ->references('id')
                    ->on('sections')
                    ->nullOnDelete();
            }
        });
    }

    /**
     * Check if a foreign key exists.
     */
    protected function foreignKeyExists(string $table, string $foreignKey): bool
    {
        $exists = DB::select("
            SELECT CONSTRAINT_NAME 
            FROM information_schema.TABLE_CONSTRAINTS 
            WHERE CONSTRAINT_TYPE = 'FOREIGN KEY' 
            AND TABLE_SCHEMA = DATABASE() 
            AND TABLE_NAME = ? 
            AND CONSTRAINT_NAME = ?
        ", [$table, $foreignKey]);

        return !empty($exists);
    }
};
