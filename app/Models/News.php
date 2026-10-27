<?php

namespace App\Models;

use App\Services\NewsImageService;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class News extends Model
{
    protected $table = 'news';

    protected $fillable = [
        'title',
        'slug',
        'summary',
        'content',
        'category_id',
        'author_id',
        'image_path',
        'status',
        'published_at',
    ];

    protected function casts(): array
    {
        return [
            'published_at' => 'datetime',
        ];
    }

    /**
     * Get the image URL for this news article.
     * Returns the uploaded image if available, otherwise fetches a unique
     * category-relevant photo from an external API.
     */
    public function getImageUrlAttribute(): string
    {
        // If a local image was uploaded, use it
        if ($this->image_path) {
            // Check if it's already a full URL (external API URL stored directly)
            if (str_starts_with($this->image_path, 'http')) {
                return $this->image_path;
            }
            return Storage::url($this->image_path);
        }

        // Fetch a unique image from external API based on category
        $imageService = app(NewsImageService::class);
        $categorySlug = $this->category?->slug;
        return $imageService->getImageUrl($this->id, $categorySlug, $this->title);
    }

    /**
     * Boot method to auto-generate slug from title.
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($news) {
            if (empty($news->slug)) {
                $news->slug = Str::slug($news->title) . '-' . Str::random(5);
            }
        });
    }

    /**
     * Get the category of this news article.
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * Get the author of this news article.
     */
    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class, 'author_id');
    }

    /**
     * Scope: only published articles.
     */
    public function scopePublished($query)
    {
        return $query->where('status', 'published');
    }

    /**
     * Scope: only drafts.
     */
    public function scopeDraft($query)
    {
        return $query->where('status', 'draft');
    }

    /**
     * Scope: only withdrawn.
     */
    public function scopeWithdrawn($query)
    {
        return $query->where('status', 'withdrawn');
    }

    /**
     * Check if the article is published.
     */
    public function isPublished(): bool
    {
        return $this->status === 'published';
    }

    /**
     * Check if the article is a draft.
     */
    public function isDraft(): bool
    {
        return $this->status === 'draft';
    }

    /**
     * Publish the article.
     */
    public function publish(): void
    {
        $this->update([
            'status' => 'published',
            'published_at' => now(),
        ]);
    }

    /**
     * Withdraw the article from public view.
     */
    public function withdraw(): void
    {
        $this->update([
            'status' => 'withdrawn',
        ]);
    }
}
