<?php

namespace App\Services;

use App\Models\BreedCache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * Service for fetching breed information from TheDogAPI and TheCatAPI.
 * Implements caching to avoid redundant API calls.
 */
class BreedService
{
    private const DOG_API_URL = 'https://api.thedogapi.com/v1';
    private const CAT_API_URL = 'https://api.thecatapi.com/v1';
    private const CACHE_HOURS = 24;
    private const TIMEOUT_SECONDS = 10;

    /**
     * Get breed information for a given species and breed name.
     * Returns cached data if available and fresh, otherwise fetches from API.
     */
    public function getBreedInfo(string $species, string $breedName): ?array
    {
        // Check cache first
        $cached = BreedCache::where('species', $species)
            ->where('breed_name', $breedName)
            ->first();

        if ($cached && !$cached->isStale()) {
            return [
                'data' => $cached->data_json,
                'image_url' => $cached->image_url,
                'fetched_at' => $cached->fetched_at->toDateTimeString(),
                'source' => 'cache',
            ];
        }

        // Fetch from external API
        try {
            $result = $this->fetchFromApi($species, $breedName);

            if ($result) {
                // Update or create cache entry
                BreedCache::updateOrCreate(
                    ['species' => $species, 'breed_name' => $breedName],
                    [
                        'data_json' => $result['data'],
                        'image_url' => $result['image_url'] ?? null,
                        'fetched_at' => now(),
                    ]
                );

                return [
                    'data' => $result['data'],
                    'image_url' => $result['image_url'] ?? null,
                    'fetched_at' => now()->toDateTimeString(),
                    'source' => 'api',
                ];
            }
        } catch (\Exception $e) {
            Log::warning("BreedService API error for {$species}/{$breedName}: " . $e->getMessage());

            // Return stale cache if available
            if ($cached) {
                return [
                    'data' => $cached->data_json,
                    'image_url' => $cached->image_url,
                    'fetched_at' => $cached->fetched_at->toDateTimeString(),
                    'source' => 'cache (stale - API unavailable)',
                ];
            }
        }

        return null;
    }

    /**
     * Fetch breed data from the external API.
     */
    private function fetchFromApi(string $species, string $breedName): ?array
    {
        $baseUrl = $species === 'dog' ? self::DOG_API_URL : self::CAT_API_URL;
        $apiKey = $species === 'dog'
            ? config('services.dog_api.key')
            : config('services.cat_api.key');

        $headers = [];
        if ($apiKey) {
            $headers['x-api-key'] = $apiKey;
        }

        // Search for the breed
        $response = Http::timeout(self::TIMEOUT_SECONDS)
            ->withHeaders($headers)
            ->get("{$baseUrl}/breeds/search", [
                'q' => $breedName,
            ]);

        if (!$response->successful()) {
            throw new \RuntimeException("API returned status {$response->status()}");
        }

        $breeds = $response->json();

        if (empty($breeds) || !is_array($breeds)) {
            return null;
        }

        $breed = $breeds[0];

        // Validate expected structure
        if (!isset($breed['name'])) {
            Log::warning("BreedService: Unexpected response structure for {$species}/{$breedName}");
            return null;
        }

        // Extract relevant data
        $data = [
            'name' => $breed['name'] ?? $breedName,
            'temperament' => $breed['temperament'] ?? 'Unknown',
            'life_span' => $breed['life_span'] ?? 'Unknown',
            'weight' => $breed['weight']['metric'] ?? ($breed['weight']['imperial'] ?? 'Unknown'),
            'height' => $breed['height']['metric'] ?? ($breed['height']['imperial'] ?? null),
            'bred_for' => $breed['bred_for'] ?? null,
            'breed_group' => $breed['breed_group'] ?? null,
            'origin' => $breed['origin'] ?? null,
            'description' => $breed['description'] ?? ($breed['temperament'] ?? null),
        ];

        // Try to get an image
        $imageUrl = null;
        if (isset($breed['reference_image_id'])) {
            try {
                $imgResponse = Http::timeout(self::TIMEOUT_SECONDS)
                    ->withHeaders($headers)
                    ->get("{$baseUrl}/images/{$breed['reference_image_id']}");

                if ($imgResponse->successful()) {
                    $imgData = $imgResponse->json();
                    $imageUrl = $imgData['url'] ?? null;
                }
            } catch (\Exception $e) {
                Log::info("BreedService: Could not fetch breed image: " . $e->getMessage());
            }
        } elseif (isset($breed['image']['url'])) {
            $imageUrl = $breed['image']['url'];
        }

        return [
            'data' => $data,
            'image_url' => $imageUrl,
        ];
    }

    /**
     * Get all cached breeds for a species.
     */
    public function getCachedBreeds(string $species): array
    {
        return BreedCache::where('species', $species)
            ->orderBy('breed_name')
            ->get()
            ->map(fn ($cache) => [
                'breed_name' => $cache->breed_name,
                'data' => $cache->data_json,
                'image_url' => $cache->image_url,
                'fetched_at' => $cache->fetched_at->toDateTimeString(),
                'is_stale' => $cache->isStale(),
            ])
            ->toArray();
    }

    /**
     * Simulate an API failure for testing/demonstration purposes.
     */
    public function simulateFailure(string $species, string $breedName): array
    {
        return [
            'error' => true,
            'message' => 'External API service is currently unavailable.',
            'species' => $species,
            'breed_name' => $breedName,
            'suggestion' => 'Please try again later. Cached data may be available.',
        ];
    }
}
