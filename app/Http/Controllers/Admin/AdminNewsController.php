<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\News;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class AdminNewsController extends Controller
{
    /**
     * List all news (admin sees all, editor sees own).
     */
    public function index(Request $request)
    {
        $query = News::with(['category', 'author']);

        // Editors can only see their own news
        if (Auth::user()->isEditor() && !Auth::user()->isAdmin()) {
            $query->where('author_id', Auth::id());
        }

        // Search
        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('summary', 'like', "%{$search}%");
            });
        }

        // Filter by status
        if ($status = $request->input('status')) {
            $query->where('status', $status);
        }

        $news = $query->orderByDesc('created_at')->paginate(10)->withQueryString();
        $categories = Category::orderBy('name')->get();

        return view('admin.news.index', compact('news', 'categories', 'search', 'status'));
    }

    /**
     * Show form to create a new news article.
     */
    public function create()
    {
        $categories = Category::orderBy('name')->get();
        return view('admin.news.create', compact('categories'));
    }

    /**
     * Store a new news article.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'summary' => ['required', 'string', 'max:500'],
            'content' => ['required', 'string'],
            'category_id' => ['required', 'exists:categories,id'],
            'image' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif,webp', 'max:5120'],
            'status' => ['required', 'in:draft,published'],
        ]);

        // Sanitize HTML from rich text editor
        $validated['content'] = $this->sanitizeHtml($validated['content']);

        $news = new News();
        $news->title = $validated['title'];
        $news->slug = Str::slug($validated['title']) . '-' . Str::random(5);
        $news->summary = $validated['summary'];
        $news->content = $validated['content'];
        $news->category_id = $validated['category_id'];
        $news->author_id = Auth::id();
        $news->status = $validated['status'];

        if ($validated['status'] === 'published') {
            $news->published_at = now();
        }

        // Handle image upload
        if ($request->hasFile('image')) {
            $news->image_path = $request->file('image')->store('news', 'public');
        }

        $news->save();

        return redirect()->route('admin.news.index')
            ->with('success', 'News article created successfully.');
    }

    /**
     * Show form to edit a news article.
     */
    public function edit(News $news)
    {
        // Authorization: editors can only edit their own
        if (Auth::user()->isEditor() && !Auth::user()->isAdmin() && $news->author_id !== Auth::id()) {
            abort(403, 'You can only edit your own articles.');
        }

        $categories = Category::orderBy('name')->get();
        return view('admin.news.edit', compact('news', 'categories'));
    }

    /**
     * Update a news article.
     */
    public function update(Request $request, News $news)
    {
        // Authorization: editors can only edit their own
        if (Auth::user()->isEditor() && !Auth::user()->isAdmin() && $news->author_id !== Auth::id()) {
            abort(403, 'You can only edit your own articles.');
        }

        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'summary' => ['required', 'string', 'max:500'],
            'content' => ['required', 'string'],
            'category_id' => ['required', 'exists:categories,id'],
            'image' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif,webp', 'max:5120'],
            'status' => ['required', 'in:draft,published,withdrawn'],
            'remove_image' => ['nullable', 'boolean'],
        ]);

        // Sanitize HTML from rich text editor
        $validated['content'] = $this->sanitizeHtml($validated['content']);

        $news->title = $validated['title'];
        $news->summary = $validated['summary'];
        $news->content = $validated['content'];
        $news->category_id = $validated['category_id'];
        $news->status = $validated['status'];

        if ($validated['status'] === 'published' && !$news->published_at) {
            $news->published_at = now();
        }

        // Handle image removal
        if ($request->boolean('remove_image') && $news->image_path) {
            Storage::disk('public')->delete($news->image_path);
            $news->image_path = null;
        }

        // Handle new image upload
        if ($request->hasFile('image')) {
            // Delete old image
            if ($news->image_path) {
                Storage::disk('public')->delete($news->image_path);
            }
            $news->image_path = $request->file('image')->store('news', 'public');
        }

        $news->save();

        return redirect()->route('admin.news.index')
            ->with('success', 'News article updated successfully.');
    }

    /**
     * Delete a news article (with confirmation via JS on frontend).
     */
    public function destroy(News $news)
    {
        // Authorization: editors can only delete their own
        if (Auth::user()->isEditor() && !Auth::user()->isAdmin() && $news->author_id !== Auth::id()) {
            abort(403, 'You can only delete your own articles.');
        }

        if ($news->image_path) {
            Storage::disk('public')->delete($news->image_path);
        }

        $news->delete();

        return redirect()->route('admin.news.index')
            ->with('success', 'News article deleted successfully.');
    }

    /**
     * Sanitize HTML content from the rich text editor.
     * Allows safe HTML tags while stripping dangerous ones.
     */
    private function sanitizeHtml(string $html): string
    {
        $allowed = '<p><br><strong><em><u><s><h1><h2><h3><h4><h5><h6><ul><ol><li><a><img><blockquote><pre><code><table><thead><tbody><tr><th><td><hr><span><div><sub><sup>';
        return strip_tags($html, $allowed);
    }
}
