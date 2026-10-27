<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BreedCache extends Model
{
    protected $table = 'breed_cache';

    protected $fillable = [
        'species',
        'breed_name',
        'data_json',
        'image_url',
        'fetched_at',
    ];

    protected function casts(): array
    {
        return [
            'data_json' => 'array',
            'fetched_at' => 'datetime',
        ];
    }

    /**
     * Check if the cached data is stale (older than 24 hours).
     */
    public function isStale(): bool
    {
        return $this->fetched_at->diffInHours(now()) > 24;
    }
}
