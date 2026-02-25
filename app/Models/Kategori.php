<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Kategori extends Model
{
    protected $fillable = [
        'name_ktgr',
        'type_ktgr',
        'color_ktgr',
        'program_id'
    ];

    public function program()
    {
        return $this->belongsTo(Program::class);
    }

    public function transaksi()
    {
        return $this->hasMany(Transaksi::class);
    }
}
