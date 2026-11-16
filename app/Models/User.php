<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'phone',
        'avatar_path',
        'role_id',
        'is_active',
        'two_factor_secret',
        'two_factor_recovery_codes',
        'two_factor_confirmed_at',
    ];

    /**
     * The attributes that should be hidden for serialization.
     */
    protected $hidden = [
        'password',
        'remember_token',
        'two_factor_secret',
        'two_factor_recovery_codes',
    ];

    /**
     * Get the attributes that should be cast.
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_active' => 'boolean',
            'two_factor_recovery_codes' => 'array',
            'two_factor_confirmed_at' => 'datetime',
        ];
    }

    /**
     * Get the role that this user belongs to.
     */
    public function role(): BelongsTo
    {
        return $this->belongsTo(Role::class);
    }

    /**
     * Get the news articles written by this user.
     */
    public function news(): HasMany
    {
        return $this->hasMany(News::class, 'author_id');
    }

    /**
     * Get the adoption requests submitted by this user.
     */
    public function adoptionRequests(): HasMany
    {
        return $this->hasMany(AdoptionRequest::class);
    }

    /**
     * Check if the user has admin role.
     */
    public function isAdmin(): bool
    {
        return $this->role && $this->role->name === Role::ADMIN;
    }

    /**
     * Check if the user has editor role.
     */
    public function isEditor(): bool
    {
        return $this->role && $this->role->name === Role::EDITOR;
    }

    /**
     * Check if the user has at least editor privileges.
     */
    public function isEditorOrAbove(): bool
    {
        return $this->isAdmin() || $this->isEditor();
    }

    /**
     * Check if the user account is active.
     */
    public function isActive(): bool
    {
        return $this->is_active;
    }

    /**
     * Check if the user has two-factor authentication enabled and confirmed.
     */
    public function hasTwoFactorEnabled(): bool
    {
        return !is_null($this->two_factor_secret) && !is_null($this->two_factor_confirmed_at);
    }
}
