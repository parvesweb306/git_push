<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
            protected $fillable = [
                'name',
                'email',
                'password',
                'role',
                'created_by',
                'family_invite_code',
                'telegram_chat_id',
                'telegram_link_token_hash',
                'telegram_link_token_expires_at',
            ];

            const ROLE_ADMIN = 'admin';
            const ROLE_USER = 'user';

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'telegram_link_token_expires_at' => 'datetime',
    ];

    public function devices()
{
    return $this->hasMany(Device::class);
}

    public function familyInviteLinks()
    {
        return $this->hasMany(FamilyInviteLink::class);
    }

    /**
     * Sub-users created by this (main admin) account.
     */
    public function subUsers()
    {
        return $this->hasMany(User::class, 'created_by');
    }

    /**
     * The main admin who created this sub-user, if any.
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function isAdmin(): bool
    {
        return $this->role === self::ROLE_ADMIN;
    }

    
}
