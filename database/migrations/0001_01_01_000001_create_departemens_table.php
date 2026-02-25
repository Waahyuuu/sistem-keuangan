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
        Schema::create('departemens', function (Blueprint $table) {
            $table->id();
            $table->string('name_dep');
            $table->string('slug')->unique();

            // Relasi ke kantor
            $table->foreignId('kantor_id')
                ->constrained('kantors')
                ->cascadeOnDelete();

            // anak departemen
            $table->foreignId('parent_id')
                ->nullable()
                ->constrained('departemens')
                ->cascadeOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('departemens');
    }
};
