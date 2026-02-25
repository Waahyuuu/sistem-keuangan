<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Rekening extends Model
{
    use HasFactory;

    protected $fillable = [
        'name_rek',
        'no_rek',
        'saldo_awal',
        'is_active'
    ];

    protected $casts = [
        'saldo_awal' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    protected $appends = [
        'saldo_akhir'
    ];

    /*
    |--------------------------------------------------------------------------
    | RELATION
    |--------------------------------------------------------------------------
    */

    public function transaksi()
    {
        return $this->hasMany(Transaksi::class, 'rekening_id');
    }

    public function transferMasuk()
    {
        return $this->hasMany(Transaksi::class, 'rekening_tujuan_id')
            ->where('type_transaksi', Transaksi::TYPE_TRANSFER);
    }

    /*
    |--------------------------------------------------------------------------
    | ACCESSOR
    |--------------------------------------------------------------------------
    */

    public function getSaldoAkhirAttribute()
    {
        return $this->saldo_awal
            + $this->totalMasuk()
            - $this->totalKeluar()
            + $this->totalTransferMasuk()
            - $this->totalTransferKeluar();
    }

    /*
    |--------------------------------------------------------------------------
    | HELPER METHOD
    |--------------------------------------------------------------------------
    */

    public function totalMasuk()
    {
        return $this->transaksi()
            ->whereIn('type_transaksi', [
                Transaksi::TYPE_PEMASUKAN,
                Transaksi::TYPE_PIUTANG
            ])
            ->sum('nominal_transaksi');
    }

    public function totalKeluar()
    {
        return $this->transaksi()
            ->whereIn('type_transaksi', [
                Transaksi::TYPE_PENGELUARAN,
                Transaksi::TYPE_UTANG
            ])
            ->sum('nominal_transaksi');
    }

    public function totalTransferMasuk()
    {
        return $this->transferMasuk()
            ->sum('nominal_transaksi');
    }

    public function totalTransferKeluar()
    {
        return $this->transaksi()
            ->where('type_transaksi', Transaksi::TYPE_TRANSFER)
            ->sum('nominal_transaksi');
    }
}
