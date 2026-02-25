<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\Transaksi;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('transaksis', function (Blueprint $table) {
            $table->id();

            // TYPE TRANSAKSI
            $table->enum('type_transaksi', [
                'pemasukan',
                'pengeluaran',
                'transfer',
                'utang',
                'piutang'
            ]);

            // NOMINAL
            $table->decimal('nominal_transaksi', 15, 2);

            // DESKRIPSI
            $table->text('keterangan')->nullable();

            // FILE BUKTI
            $table->string('bukti_nota')->nullable();

            // TANGGAL TRANSAKSI
            $table->date('tgl_transaksi');

            /*
            |--------------------------------------------------------------------------
            | RELASI
            |--------------------------------------------------------------------------
            */

            $table->foreignId('user_id')
                ->constrained()
                ->cascadeOnDelete();

            // Rekening asal
            $table->foreignId('rekening_id')
                ->constrained('rekenings')
                ->cascadeOnDelete();

            // Rekening tujuan (hanya untuk transfer)
            $table->foreignId('rekening_tujuan_id')
                ->nullable()
                ->constrained('rekenings')
                ->nullOnDelete();

            $table->foreignId('departemen_id')
                ->nullable()
                ->constrained('departemens')
                ->nullOnDelete();

            $table->foreignId('program_id')
                ->nullable()
                ->constrained('programs')
                ->nullOnDelete();

            $table->foreignId('kategori_id')
                ->nullable()
                ->constrained('kategoris')
                ->nullOnDelete();

            $table->timestamps();

            /*
            |--------------------------------------------------------------------------
            | INDEX UNTUK PERFORMA LAPORAN
            |--------------------------------------------------------------------------
            */

            $table->index('type_transaksi');
            $table->index('tgl_transaksi');
            $table->index('rekening_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transaksis');
    }
};
