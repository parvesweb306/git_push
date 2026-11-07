<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Device extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'device_name',
        'pairing_code',
        'device_token',
        'installation_id',
        'is_locked',
        'status',
        'ip_address',

        'unlock_pin',
        'unlock_code_hash',
        'unlock_code_expires_at',

        'last_seen',

        // Device information
        'manufacturer',
        'model',
        'android_version',
        'battery_level',

        // GPS location
        'latitude',
        'longitude',
        'location_accuracy',
        'location_updated_at',

        // IP-based location
        'country',
        'city',
        'region',
        'postal_code',
        'timezone',
    ];

    protected $casts = [
        'is_locked' => 'boolean',

        'last_seen' => 'datetime',
        'unlock_code_expires_at' => 'datetime',
        'location_updated_at' => 'datetime',

        'battery_level' => 'integer',

        'latitude' => 'float',
        'longitude' => 'float',
        'location_accuracy' => 'float',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}