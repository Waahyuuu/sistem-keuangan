<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */

    // Role
    const ROLE_ADMIN    = 'admin';
    const ROLE_OPERATOR = 'operator';
    const ROLE_USER     = 'user';
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'kantor_id',
        'departemen_id',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    // ================= RELATION =================

    public function kantor()
    {
        return $this->belongsTo(Kantor::class);
    }

    public function departemen()
    {
        return $this->belongsTo(Departemen::class);
    }

    public function transaksi()
    {
        return $this->hasMany(Transaksi::class);
    }

    public function rekening()
    {
        return $this->belongsToMany(Rekening::class, 'user_rekening');
    }

    // helper role
    public function isAdmin()
    {
        return $this->role === self::ROLE_ADMIN;
    }

    public function isOperator()
    {
        return $this->role === self::ROLE_OPERATOR;
    }

    public function isUser()
    {
        return $this->role === self::ROLE_USER;
    }
}
