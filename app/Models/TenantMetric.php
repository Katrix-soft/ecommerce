<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TenantMetric extends Model
{
    protected $fillable = [
        'user_id',
        'metric_key',
        'is_enabled',
    ];

    protected $casts = [
        'is_enabled' => 'boolean',
    ];

    /**
     * El admin/tenant dueño de esta métrica.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Verificar si una métrica está habilitada para un usuario.
     */
    public static function isEnabled(int $userId, string $metricKey): bool
    {
        return static::where('user_id', $userId)
            ->where('metric_key', $metricKey)
            ->where('is_enabled', true)
            ->exists();
    }

    /**
     * Obtener todas las métricas habilitadas para un usuario.
     */
    public static function getEnabledMetrics(int $userId): array
    {
        return static::where('user_id', $userId)
            ->where('is_enabled', true)
            ->pluck('metric_key')
            ->toArray();
    }
}
