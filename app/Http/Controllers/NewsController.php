<?php

namespace App\Http\Controllers;

use App\Models\News;
use App\Models\Category;
use Illuminate\Http\Request;

class NewsController extends Controller
{
    /**
     * Display paginated list of published news with search and category filter.
     */
    public function index(Request $request)
    {
        $query = News::published()->with(['category', 'author']);

        // Search by title or content
        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('content', 'like', "%{$search}%")
                  ->orWhere('summary', 'like', "%{$search}%");
            });
        }

        // Filter by category
        if ($categorySlug = $request->input('category')) {
            $category = Category::where('slug', $categorySlug)->first();
            if ($category) {
                $query->where('category_id', $category->id);
            }
        }

        $news = $query->orderByDesc('published_at')->paginate(6)->withQueryString();
        $categories = Category::orderBy('name')->get();

        return view('public.news.index', compact('news', 'categories', 'search', 'categorySlug'));
    }

    /**
     * Display a single published news article.
     */
    public function show(string $slug)
    {
        $article = News::published()
            ->where('slug', $slug)
            ->with(['category', 'author'])
            ->firstOrFail();

        $relatedNews = News::published()
            ->where('category_id', $article->category_id)
            ->where('id', '!=', $article->id)
            ->orderByDesc('published_at')
            ->take(3)
            ->get();

        return view('public.news.show', compact('article', 'relatedNews'));
    }
}
