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
        Schema::create('blocks', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->foreignId('complex_id')->constrained('complexes')->cascadeOnDelete();
            $table->integer('street_id')->nullable();
            $table->string('building_number')->nullable();
            $table->unsignedSmallInteger('floors_total')->nullable();
            $table->unsignedSmallInteger('year_built')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('street_id')->references('id')->on('streets')->nullOnDelete();

            $table->index('complex_id');
            $table->index('street_id');
            $table->index('is_active');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('blocks');
    }
};
