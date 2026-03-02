<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Program extends Model
{
    protected $fillable = [
        'name_prog',
        'departemen_id'
    ];

    public function departemen()
    {
        return $this->belongsTo(Departemen::class);
    }

    public function users()
    {
        return $this->hasMany(User::class);
    }
}
