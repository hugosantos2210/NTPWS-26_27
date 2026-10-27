<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * Service for fetching news article images from external APIs.
 * Uses Pexels API for keyword-based image search.
 * Falls back to curated Unsplash photos categorized by topic.
 */
class NewsImageService
{
    private const PEXELS_API_URL = 'https://api.pexels.com/v1';
    private const TIMEOUT_SECONDS = 8;
    private const CACHE_HOURS = 168; // 7 days

    /**
     * Category-to-keyword mapping for relevant image searches.
     */
    private const CATEGORY_KEYWORDS = [
        'adoption-stories' => 'happy pet family adoption',
        'health-care' => 'veterinary pet health care',
        'events' => 'pet community event outdoor',
        'volunteer' => 'volunteer animal shelter helping',
        'tips-training' => 'dog training pet obedience',
    ];

    /**
     * Get a unique image URL for a news article.
     */
    public function getImageUrl(int $articleId, ?string $categorySlug, ?string $title): string
    {
        $cacheKey = "news_image_{$articleId}";

        return Cache::remember($cacheKey, now()->addHours(self::CACHE_HOURS), function () use ($articleId, $categorySlug, $title) {
            // Try Pexels API first (if API key is configured)
            $pexelsKey = config('services.pexels.key');
            if ($pexelsKey) {
                try {
                    return $this->fetchFromPexels($articleId, $categorySlug, $title, $pexelsKey);
                } catch (\Exception $e) {
                    Log::info("NewsImageService: Pexels API failed for article {$articleId}: " . $e->getMessage());
                }
            }

            // Fallback to curated Unsplash photos
            return $this->getFallbackUrl($articleId, $categorySlug);
        });
    }

    /**
     * Fetch an image from the Pexels API based on category/title keywords.
     */
    private function fetchFromPexels(int $articleId, ?string $categorySlug, ?string $title, string $apiKey): string
    {
        $query = $this->buildSearchQuery($categorySlug, $title);

        $response = Http::timeout(self::TIMEOUT_SECONDS)
            ->withHeaders(['Authorization' => $apiKey])
            ->get(self::PEXELS_API_URL . '/search', [
                'query' => $query,
                'per_page' => 15,
                'orientation' => 'landscape',
            ]);

        if ($response->successful()) {
            $data = $response->json();
            $photos = $data['photos'] ?? [];

            if (!empty($photos)) {
                // Use article ID to pick a unique but consistent image
                $index = $articleId % count($photos);
                return $photos[$index]['src']['medium'] ?? $photos[$index]['src']['original'];
            }
        }

        return $this->getFallbackUrl($articleId, $categorySlug);
    }

    /**
     * Build a search query string from category and title.
     */
    private function buildSearchQuery(?string $categorySlug, ?string $title): string
    {
        // Try category-specific keywords first
        if ($categorySlug && isset(self::CATEGORY_KEYWORDS[$categorySlug])) {
            return self::CATEGORY_KEYWORDS[$categorySlug];
        }

        // Extract keywords from title
        if ($title) {
            // Get meaningful words from title (4+ characters)
            $words = array_filter(
                explode(' ', strtolower($title)),
                fn($w) => strlen($w) >= 4
            );
            $keywords = implode(' ', array_slice(array_values($words), 0, 3));
            if ($keywords) {
                return $keywords . ' pet animal';
            }
        }

        return 'animal shelter pet care';
    }

    /**
     * Get a fallback image URL using curated Unsplash photos per category.
     * Each article gets a unique photo based on its ID.
     */
    private function getFallbackUrl(int $articleId, ?string $categorySlug): string
    {
        // Curated photos organized by category
        $categoryPhotos = [
            'adoption-stories' => [
                'https://images.unsplash.com/photo-1601758228041-f3b2795255f1?auto=format&fit=crop&w=600&q=80',
                'https://images.unsplash.com/photo-1548199973-03cce0bbc87b?auto=format&fit=crop&w=600&q=80',
                'https://images.unsplash.com/photo-1450778869180-41d0601e046e?auto=format&fit=crop&w=600&q=80',
                'https://images.unsplash.com/photo-1587300003388-59208cc962cb?auto=format&fit=crop&w=600&q=80',
            ],
            'health-care' => [
                'https://images.unsplash.com/photo-1628009368231-7bb7cfcb0def?auto=format&fit=crop&w=600&q=80',
                'https://images.unsplash.com/photo-1612531386530-97286d97c2d2?auto=format&fit=crop&w=600&q=80',
                'https://images.unsplash.com/photo-1548767797-d8c844163c4c?auto=format&fit=crop&w=600&q=80',
                'https://images.unsplash.com/photo-1576201836106-db1758fd1c97?auto=format&fit=crop&w=600&q=80',
            ],
            'events' => [
                'https://images.unsplash.com/photo-1527525443983-6e60c75fff46?auto=format&fit=crop&w=600&q=80',
                'https://images.unsplash.com/photo-1544568100-847a948585b9?auto=format&fit=crop&w=600&q=80',
                'https://images.unsplash.com/photo-1530281700549-e82e7bf110d6?auto=format&fit=crop&w=600&q=80',
                'https://images.unsplash.com/photo-1558618666-fcd25c85f82e?auto=format&fit=crop&w=600&q=80',
            ],
            'volunteer' => [
                'https://images.unsplash.com/photo-1415369629372-26f2fe60c467?auto=format&fit=crop&w=600&q=80',
                'https://images.unsplash.com/photo-1586671267731-da2cf3ceeb80?auto=format&fit=crop&w=600&q=80',
                'https://images.unsplash.com/photo-1601758174114-e711c0cbaa69?auto=format&fit=crop&w=600&q=80',
                'https://images.unsplash.com/photo-1494947665470-20322015e3a8?auto=format&fit=crop&w=600&q=80',
            ],
            'tips-training' => [
                'https://images.unsplash.com/photo-1587300003388-59208cc962cb?auto=format&fit=crop&w=600&q=80',
                'https://images.unsplash.com/photo-1522276498395-f4f68f7f8571?auto=format&fit=crop&w=600&q=80',
                'https://images.unsplash.com/photo-1535930749574-1399327ce78f?auto=format&fit=crop&w=600&q=80',
                'https://images.unsplash.com/photo-1504595403791-c3c3f3e1cf30?auto=format&fit=crop&w=600&q=80',
            ],
        ];

        // Default photos for unknown/no category
        $defaultPhotos = [
            'https://images.unsplash.com/photo-1548767797-d8c844163c4c?auto=format&fit=crop&w=600&q=80',
            'https://images.unsplash.com/photo-1450778869180-41d0601e046e?auto=format&fit=crop&w=600&q=80',
            'https://images.unsplash.com/photo-1601758228041-f3b2795255f1?auto=format&fit=crop&w=600&q=80',
            'https://images.unsplash.com/photo-1583337130417-13104dec14a8?auto=format&fit=crop&w=600&q=80',
            'https://images.unsplash.com/photo-1544568100-847a948585b9?auto=format&fit=crop&w=600&q=80',
            'https://images.unsplash.com/photo-1415369629372-26f2fe60c467?auto=format&fit=crop&w=600&q=80',
            'https://images.unsplash.com/photo-1587300003388-59208cc962cb?auto=format&fit=crop&w=600&q=80',
            'https://images.unsplash.com/photo-1522276498395-f4f68f7f8571?auto=format&fit=crop&w=600&q=80',
        ];

        $photos = $categoryPhotos[$categorySlug] ?? $defaultPhotos;
        $index = $articleId % count($photos);

        return $photos[$index];
    }
}
