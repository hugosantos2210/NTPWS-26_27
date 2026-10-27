<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Role extends Model
{
    protected $fillable = ['name', 'description'];

    /**
     * Role constants for easy reference throughout the application.
     */
    public const USER = 'user';
    public const EDITOR = 'editor';
    public const ADMIN = 'admin';

    /**
     * Get the users that belong to this role.
     */
    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    /**
     * Check if this is the admin role.
     */
    public function isAdmin(): bool
    {
        return $this->name === self::ADMIN;
    }

    /**
     * Check if this is the editor role.
     */
    public function isEditor(): bool
    {
        return $this->name === self::EDITOR;
    }
}
