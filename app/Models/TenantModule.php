<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TenantModule extends Model
{
    protected $fillable = [
        'user_id',
        'module',
        'is_enabled',
    ];

    protected $casts = [
        'is_enabled' => 'boolean',
    ];

    /**
     * El admin/tenant dueño de este módulo.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Verificar si un módulo está habilitado para un usuario.
     */
    public static function isEnabled(int $userId, string $module): bool
    {
        return static::where('user_id', $userId)
            ->where('module', $module)
            ->where('is_enabled', true)
            ->exists();
    }

    /**
     * Obtener todos los módulos habilitados para un usuario.
     */
    public static function getEnabledModules(int $userId): array
    {
        return static::where('user_id', $userId)
            ->where('is_enabled', true)
            ->pluck('module')
            ->toArray();
    }
}
