<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\News;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class ApiNewsController extends Controller
{
    /**
     * GET /api/v1/news
     * List published news with pagination and filters.
     */
    public function index(Request $request)
    {
        $query = News::published()->with(['category', 'author:id,name']);

        // Filter by category
        if ($categoryId = $request->input('category_id')) {
            $query->where('category_id', $categoryId);
        }

        // Search by title or content
        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('summary', 'like', "%{$search}%");
            });
        }

        $perPage = min($request->input('per_page', 10), 50);
        $news = $query->orderByDesc('published_at')->paginate($perPage);

        return response()->json([
            'success' => true,
            'data' => $news->items(),
            'meta' => [
                'current_page' => $news->currentPage(),
                'per_page' => $news->perPage(),
                'total' => $news->total(),
                'last_page' => $news->lastPage(),
            ],
        ], 200);
    }

    /**
     * GET /api/v1/news/{id}
     * Get a single published news article.
     */
    public function show(int $id)
    {
        $news = News::published()
            ->with(['category', 'author:id,name'])
            ->find($id);

        if (!$news) {
            return response()->json([
                'success' => false,
                'error' => 'News article not found or not published.',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $news,
        ], 200);
    }

    /**
     * POST /api/v1/news
     * Create a new news article. Requires editor or admin role.
     */
    public function store(Request $request)
    {
        // Validate against JSON Schema rules
        $validator = Validator::make($request->all(), [
            'title' => ['required', 'string', 'max:255'],
            'summary' => ['required', 'string', 'max:500'],
            'content' => ['required', 'string'],
            'category_id' => ['required', 'integer', 'exists:categories,id'],
            'status' => ['sometimes', 'in:draft,published'],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'error' => 'Validation failed.',
                'details' => $validator->errors(),
            ], 400);
        }

        $validated = $validator->validated();

        $news = News::create([
            'title' => $validated['title'],
            'slug' => Str::slug($validated['title']) . '-' . Str::random(5),
            'summary' => $validated['summary'],
            'content' => strip_tags($validated['content'], '<p><br><strong><em><u><ul><ol><li><a><h1><h2><h3><h4><h5><h6><blockquote><pre><code><img>'),
            'category_id' => $validated['category_id'],
            'author_id' => Auth::id(),
            'status' => $validated['status'] ?? 'draft',
            'published_at' => ($validated['status'] ?? 'draft') === 'published' ? now() : null,
        ]);

        return response()->json([
            'success' => true,
            'data' => $news->load(['category', 'author:id,name']),
            'message' => 'News article created successfully.',
        ], 201);
    }

    /**
     * PATCH /api/v1/news/{id}
     * Update a news article. Editor can only update own articles.
     */
    public function update(Request $request, int $id)
    {
        $news = News::find($id);

        if (!$news) {
            return response()->json([
                'success' => false,
                'error' => 'News article not found.',
            ], 404);
        }

        // Authorization: editors can only edit their own
        $user = Auth::user();
        if ($user->isEditor() && !$user->isAdmin() && $news->author_id !== $user->id) {
            return response()->json([
                'success' => false,
                'error' => 'Forbidden. You can only edit your own articles.',
            ], 403);
        }

        $validator = Validator::make($request->all(), [
            'title' => ['sometimes', 'string', 'max:255'],
            'summary' => ['sometimes', 'string', 'max:500'],
            'content' => ['sometimes', 'string'],
            'category_id' => ['sometimes', 'integer', 'exists:categories,id'],
            'status' => ['sometimes', 'in:draft,published,withdrawn'],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'error' => 'Validation failed.',
                'details' => $validator->errors(),
            ], 400);
        }

        $validated = $validator->validated();

        if (isset($validated['content'])) {
            $validated['content'] = strip_tags($validated['content'], '<p><br><strong><em><u><ul><ol><li><a><h1><h2><h3><h4><h5><h6><blockquote><pre><code><img>');
        }

        if (isset($validated['status']) && $validated['status'] === 'published' && !$news->published_at) {
            $validated['published_at'] = now();
        }

        $news->update($validated);

        return response()->json([
            'success' => true,
            'data' => $news->fresh()->load(['category', 'author:id,name']),
            'message' => 'News article updated successfully.',
        ], 200);
    }

    /**
     * DELETE /api/v1/news/{id}
     * Delete a news article.
     */
    public function destroy(int $id)
    {
        $news = News::find($id);

        if (!$news) {
            return response()->json([
                'success' => false,
                'error' => 'News article not found.',
            ], 404);
        }

        // Authorization: editors can only delete their own
        $user = Auth::user();
        if ($user->isEditor() && !$user->isAdmin() && $news->author_id !== $user->id) {
            return response()->json([
                'success' => false,
                'error' => 'Forbidden. You can only delete your own articles.',
            ], 403);
        }

        $news->delete();

        return response()->noContent(); // 204
    }

    /**
     * GET /api/v1/categories
     * List all categories.
     */
    public function categories()
    {
        $categories = Category::withCount(['news' => fn ($q) => $q->published()])
            ->orderBy('name')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $categories,
        ], 200);
    }
}
