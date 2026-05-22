<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Fortify\TwoFactorAuthenticatable;
use Laravel\Jetstream\HasProfilePhoto;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasApiTokens;

    /** @use HasFactory<UserFactory> */
    use HasFactory;

    use HasProfilePhoto;
    use HasRoles;
    use Notifiable;
    use TwoFactorAuthenticatable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'dni',
        'document_type',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
        'two_factor_recovery_codes',
        'two_factor_secret',
    ];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array<int, string>
     */
    protected $appends = [
        'profile_photo_url',
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

    public function addresses()
    {
        return $this->hasMany(Address::class);
    }

    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    /**
     * Módulos habilitados para este admin/tenant.
     */
    public function tenantModules()
    {
        return $this->hasMany(TenantModule::class);
    }

    /**
     * Verificar si el usuario tiene un módulo habilitado.
     * Los superadmins siempre tienen acceso a todo.
     */
    public function hasModule(string $module): bool
    {
        if ($this->hasRole('superadmin')) {
            return true;
        }

        return TenantModule::isEnabled($this->id, $module);
    }

    /**
     * Métricas habilitadas para este admin/tenant.
     */
    public function tenantMetrics()
    {
        return $this->hasMany(TenantMetric::class);
    }

    /**
     * Verificar si el usuario tiene una métrica habilitada en su dashboard.
     * Los superadmins siempre tienen acceso a todo.
     */
    public function hasMetric(string $metricKey): bool
    {
        if ($this->hasRole('superadmin')) {
            return true;
        }

        return TenantMetric::isEnabled($this->id, $metricKey);
    }
}
