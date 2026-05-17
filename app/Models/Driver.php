<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Driver extends Model
{
    protected $fillable = [
        'name',
        'dni',
        'phone',
        'license',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    /**
     * Get the shipments assigned to this driver.
     */
    public function shipments(): HasMany
    {
        return $this->hasMany(Shipment::class);
    }
}
