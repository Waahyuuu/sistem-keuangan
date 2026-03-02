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
        Schema::create('kategoris', function (Blueprint $table) {
            $table->id();
            $table->string('name_ktgr');
            $table->enum('type_ktgr', ['pemasukan', 'pengeluaran']);
            $table->string('color_ktgr', 7)->nullable();

            // Relasi ke Program
            $table->foreignId('program_id')
                ->constrained('programs')
                ->cascadeOnDelete();

            $table->timestamps();
            
            $table->index('program_id');
            $table->unique(['name_ktgr', 'program_id', 'type_ktgr'], 'kategoris_unique_combo');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('kategoris');
    }
};
