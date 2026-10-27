<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Pet extends Model
{
    protected $fillable = [
        'name',
        'species',
        'breed',
        'age_years',
        'age_months',
        'gender',
        'size',
        'description',
        'image_path',
        'status',
    ];

    /**
     * Get the adoption requests for this pet.
     */
    public function adoptionRequests(): HasMany
    {
        return $this->hasMany(AdoptionRequest::class);
    }

    /**
     * Scope: only available pets.
     */
    public function scopeAvailable($query)
    {
        return $query->where('status', 'available');
    }

    /**
     * Check if the pet is available for adoption.
     */
    public function isAvailable(): bool
    {
        return $this->status === 'available';
    }

    /**
     * Get the formatted age string.
     */
    public function getFormattedAgeAttribute(): string
    {
        $parts = [];
        if ($this->age_years > 0) {
            $parts[] = $this->age_years . ' ' . ($this->age_years === 1 ? 'year' : 'years');
        }
        if ($this->age_months > 0) {
            $parts[] = $this->age_months . ' ' . ($this->age_months === 1 ? 'month' : 'months');
        }
        return implode(', ', $parts) ?: 'Unknown';
    }

    /**
     * Get the species icon.
     */
    public function getSpeciesIconAttribute(): string
    {
        return match ($this->species) {
            'dog' => '🐕',
            'cat' => '🐈',
            default => '🐾',
        };
    }
}
