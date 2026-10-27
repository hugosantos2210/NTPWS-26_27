<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * Service for fetching pet images from TheDogAPI and TheCatAPI.
 * Each pet gets a unique, breed-appropriate photo from the external API.
 * Results are cached to minimize API calls.
 */
class PetImageService
{
    private const DOG_API_URL = 'https://api.thedogapi.com/v1';
    private const CAT_API_URL = 'https://api.thecatapi.com/v1';
    private const TIMEOUT_SECONDS = 8;
    private const CACHE_HOURS = 168; // 7 days

    /**
     * Get a unique image URL for a pet based on its species, breed, and ID.
     * Uses the external API to find breed-specific images.
     */
    public function getImageUrl(int $petId, string $species, ?string $breed): string
    {
        $cacheKey = "pet_image_{$petId}_{$species}_" . md5($breed ?? 'unknown');

        return Cache::remember($cacheKey, now()->addHours(self::CACHE_HOURS), function () use ($petId, $species, $breed) {
            try {
                return $this->fetchFromApi($petId, $species, $breed);
            } catch (\Exception $e) {
                Log::info("PetImageService: Could not fetch image for pet {$petId}: " . $e->getMessage());
                return $this->getFallbackUrl($petId, $species);
            }
        });
    }

    /**
     * Fetch a breed-specific image from TheDogAPI or TheCatAPI.
     */
    private function fetchFromApi(int $petId, string $species, ?string $breed): string
    {
        $baseUrl = $species === 'dog' ? self::DOG_API_URL : self::CAT_API_URL;
        $apiKey = $species === 'dog'
            ? config('services.dog_api.key')
            : config('services.cat_api.key');

        $headers = [];
        if ($apiKey) {
            $headers['x-api-key'] = $apiKey;
        }

        // First, try to find the breed ID
        if ($breed) {
            $breedId = $this->findBreedId($baseUrl, $headers, $breed);

            if ($breedId) {
                // Get images for this specific breed
                $response = Http::timeout(self::TIMEOUT_SECONDS)
                    ->withHeaders($headers)
                    ->get("{$baseUrl}/images/search", [
                        'breed_ids' => $breedId,
                        'limit' => 10,
                        'size' => 'med',
                    ]);

                if ($response->successful()) {
                    $images = $response->json();
                    if (!empty($images)) {
                        // Use pet ID to pick a consistent but unique image
                        $index = $petId % count($images);
                        return $images[$index]['url'];
                    }
                }
            }
        }

        // Fallback: get a random image for this species
        $response = Http::timeout(self::TIMEOUT_SECONDS)
            ->withHeaders($headers)
            ->get("{$baseUrl}/images/search", [
                'limit' => 1,
                'size' => 'med',
            ]);

        if ($response->successful()) {
            $images = $response->json();
            if (!empty($images)) {
                return $images[0]['url'];
            }
        }

        return $this->getFallbackUrl($petId, $species);
    }

    /**
     * Search for a breed ID by name.
     */
    private function findBreedId(string $baseUrl, array $headers, string $breedName): ?string
    {
        $cacheKey = "breed_id_" . md5($baseUrl . $breedName);

        return Cache::remember($cacheKey, now()->addDays(30), function () use ($baseUrl, $headers, $breedName) {
            try {
                $response = Http::timeout(self::TIMEOUT_SECONDS)
                    ->withHeaders($headers)
                    ->get("{$baseUrl}/breeds/search", [
                        'q' => $breedName,
                    ]);

                if ($response->successful()) {
                    $breeds = $response->json();
                    if (!empty($breeds) && isset($breeds[0]['id'])) {
                        return (string) $breeds[0]['id'];
                    }
                }
            } catch (\Exception $e) {
                Log::info("PetImageService: Breed search failed for {$breedName}: " . $e->getMessage());
            }

            return null;
        });
    }

    /**
     * Get a fallback image URL using different Unsplash photos per pet.
     * Each pet gets a unique photo based on its ID.
     */
    private function getFallbackUrl(int $petId, string $species): string
    {
        // Different Unsplash photo IDs for dogs and cats
        $dogPhotos = [
            'https://images.unsplash.com/photo-1543466835-00a7907e9de1?auto=format&fit=crop&w=600&q=80',
            'https://images.unsplash.com/photo-1587300003388-59208cc962cb?auto=format&fit=crop&w=600&q=80',
            'https://images.unsplash.com/photo-1561037404-61cd46aa615b?auto=format&fit=crop&w=600&q=80',
            'https://images.unsplash.com/photo-1517849845537-4d257902454a?auto=format&fit=crop&w=600&q=80',
            'https://images.unsplash.com/photo-1534361960057-19889db9621e?auto=format&fit=crop&w=600&q=80',
            'https://images.unsplash.com/photo-1548199973-03cce0bbc87b?auto=format&fit=crop&w=600&q=80',
            'https://images.unsplash.com/photo-1583511655857-d19b40a7a54e?auto=format&fit=crop&w=600&q=80',
            'https://images.unsplash.com/photo-1477884213360-7e9d7dcc8f9b?auto=format&fit=crop&w=600&q=80',
        ];

        $catPhotos = [
            'https://images.unsplash.com/photo-1514888286974-6c03e2ca1dba?auto=format&fit=crop&w=600&q=80',
            'https://images.unsplash.com/photo-1574158622682-e40e69881006?auto=format&fit=crop&w=600&q=80',
            'https://images.unsplash.com/photo-1526336024174-e58f5cdd8e13?auto=format&fit=crop&w=600&q=80',
            'https://images.unsplash.com/photo-1495360010541-f48722b34f7d?auto=format&fit=crop&w=600&q=80',
            'https://images.unsplash.com/photo-1573865526739-10659fec78a5?auto=format&fit=crop&w=600&q=80',
            'https://images.unsplash.com/photo-1533738363-b7f9aef128ce?auto=format&fit=crop&w=600&q=80',
            'https://images.unsplash.com/photo-1518791841217-8f162f1e1131?auto=format&fit=crop&w=600&q=80',
            'https://images.unsplash.com/photo-1543852786-1cf6624b9987?auto=format&fit=crop&w=600&q=80',
        ];

        $photos = $species === 'cat' ? $catPhotos : $dogPhotos;
        $index = $petId % count($photos);

        return $photos[$index];
    }
}
