<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Departemen extends Model
{
    protected $fillable = [
        'name_dep',
        'parent_id',
        'kantor_id',
        'slug'
    ];

    // slug
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($departemen) {
            $departemen->slug = Str::slug($departemen->name_dep);
        });

        static::updating(function ($departemen) {
            $departemen->slug = Str::slug($departemen->name_dep);
        });
    }

    public function getRouteKeyName()
    {
        return 'slug';
    }

    public function kantor()
    {
        return $this->belongsTo(Kantor::class);
    }

    public function program()
    {
        return $this->hasMany(Program::class);
    }

    public function parent()
    {
        return $this->belongsTo(Departemen::class, 'parent_id');
    }

    public function children()
    {
        return $this->hasMany(Departemen::class, 'parent_id');
    }

    public function user()
    {
        return $this->hasMany(User::class);
    }

    public function transaksi()
    {
        return $this->hasMany(Transaksi::class);
    }
}
