<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaksi extends Model
{
    use HasFactory;

    /*
    |--------------------------------------------------------------------------
    | CONSTANT TYPE
    |--------------------------------------------------------------------------
    */

    const TYPE_PEMASUKAN   = 'pemasukan';
    const TYPE_PENGELUARAN = 'pengeluaran';
    const TYPE_TRANSFER    = 'transfer';
    const TYPE_UTANG       = 'utang';
    const TYPE_PIUTANG     = 'piutang';

    protected $fillable = [
        'type_transaksi',
        'nominal_transaksi',
        'keterangan',
        'bukti_nota',
        'tgl_transaksi',
        'user_id',
        'rekening_id',
        'rekening_tujuan_id',
        'departemen_id',
        'program_id',

        'user_nama',
        'rekening_nama',
        'rekening_tujuan_nama',
        'departemen_nama',
        'program_nama',
    ];

    protected $casts = [
        'nominal_transaksi' => 'decimal:2',
        'tgl_transaksi'     => 'datetime'
    ];

    /*
    |--------------------------------------------------------------------------
    | RELATION
    |--------------------------------------------------------------------------
    */

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function rekening()
    {
        return $this->belongsTo(Rekening::class, 'rekening_id');
    }

    public function rekeningTujuan()
    {
        return $this->belongsTo(Rekening::class, 'rekening_tujuan_id');
    }

    public function departemen()
    {
        return $this->belongsTo(Departemen::class);
    }

    public function program()
    {
        return $this->belongsTo(Program::class);
    }

    public function kategoris()
    {
        return $this->belongsToMany(Kategori::class, 'kategori_transaksi')
            ->withPivot('kategori_nama', 'kategori_color')
            ->withTimestamps();
    }

    public function kategoriSnapshots()
    {
        return $this->hasMany(KategoriTransaksi::class);
    }

    /*
    |--------------------------------------------------------------------------
    | HELPER CHECK TYPE
    |--------------------------------------------------------------------------
    */

    public function isTransfer()
    {
        return $this->type_transaksi === self::TYPE_TRANSFER;
    }

    public function isPemasukan()
    {
        return $this->type_transaksi === self::TYPE_PEMASUKAN;
    }

    public function isPengeluaran()
    {
        return $this->type_transaksi === self::TYPE_PENGELUARAN;
    }
}
