<?php

declare(strict_types=1);

namespace App\Models;

use Database\Factories\UserFactory;
use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

/**
 * User Model
 *
 * Represents an authenticated user of the application.
 *
 * Relations:
 * - Order (1:N) - User has many orders
 * - UserAddress (1:N) - User has many saved addresses
 */
class User extends Authenticatable implements FilamentUser
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable, HasRoles;

    /**
     * Mass assignable attributes.
     */
    protected $fillable = [
        'name',
        'first_name',
        'last_name',
        'email',
        'phone',
        'password',
        'is_default',
    ];

    /**
     * Hidden from serialization (security).
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Attribute casting.
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    // =========================================================================
    // RELATIONSHIPS
    // =========================================================================

    /**
     * User has many orders.
     * Relation: 1:N (One user has many orders)
     */
    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }

    /**
     * User has many saved addresses.
     * Relation: 1:N (One user has many addresses)
     */
    public function addresses(): HasMany
    {
        return $this->hasMany(UserAddress::class);
    }

    // =========================================================================
    // ACCESSORS
    // =========================================================================

    /**
     * Full name (first + last, or fallback to username).
     */
    public function getFullNameAttribute(): string
    {
        if (!empty($this->first_name) || !empty($this->last_name)) {
            return trim("{$this->first_name} {$this->last_name}");
        }

        return (string) $this->name;
    }

    /**
     * Default shipping address for the user.
     */
    public function getDefaultShippingAddressAttribute(): ?UserAddress
    {
        return $this->addresses()->shipping()->default()->first()
            ?? $this->addresses()->shipping()->first();
    }

    /**
     * Default billing address for the user.
     */
    public function getDefaultBillingAddressAttribute(): ?UserAddress
    {
        return $this->addresses()->billing()->default()->first()
            ?? $this->addresses()->billing()->first();
    }

    // =========================================================================
    // FILAMENT & AUTHORIZATION
    // =========================================================================

    /**
     * Determine if user can access Filament admin panel.
     */
    public function canAccessPanel(Panel $panel): bool
    {
        return $this->hasAnyRole(['designer', 'employee', 'admin']);
    }

    /**
     * Check if user has admin role.
     */
    public function isAdmin(): bool
    {
        return $this->hasRole('admin');
    }

    /**
     * Check if user has employee role.
     */
    public function isEmployee(): bool
    {
        return $this->hasRole('employee');
    }

    /**
     * Check if user has designer role.
     */
    public function isDesigner(): bool
    {
        return $this->hasRole('designer');
    }

    /**
     * Check if user has customer role.
     */
    public function isCustomer(): bool
    {
        return $this->hasRole('customer');
    }
}
