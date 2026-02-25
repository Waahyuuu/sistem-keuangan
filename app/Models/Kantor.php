<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Kantor extends Model
{
    protected $fillable = [
        'name_ktr',
        'code_ktr',
        'type_ktr'
    ];

    public function departemen()
    {
        return $this->hasMany(Departemen::class);
    }

    public function user()
    {
        return $this->hasMany(User::class);
    }
}
