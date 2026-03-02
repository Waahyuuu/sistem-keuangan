<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Kategori extends Model
{
    protected $fillable = [
        'name_ktgr',
        'type_ktgr',
        'color_ktgr'
    ];

    public function transaksis()
    {
        return $this->belongsToMany(Transaksi::class, 'kategori_transaksi');
    }
}
