<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\News;
use App\Models\Pet;
use App\Models\User;
use App\Models\AdoptionRequest;
use App\Models\Category;

class DashboardController extends Controller
{
    /**
     * Display the admin dashboard with statistics.
     */
    public function index()
    {
        $stats = [
            'total_users' => User::count(),
            'active_users' => User::where('is_active', true)->count(),
            'total_news' => News::count(),
            'published_news' => News::published()->count(),
            'draft_news' => News::draft()->count(),
            'total_pets' => Pet::count(),
            'available_pets' => Pet::available()->count(),
            'adopted_pets' => Pet::where('status', 'adopted')->count(),
            'pending_requests' => AdoptionRequest::where('status', 'pending')->count(),
            'categories' => Category::count(),
        ];

        $recentNews = News::with(['author', 'category'])
            ->orderByDesc('created_at')
            ->take(5)
            ->get();

        $recentRequests = AdoptionRequest::with(['pet', 'user'])
            ->where('status', 'pending')
            ->orderByDesc('created_at')
            ->take(5)
            ->get();

        return view('admin.dashboard', compact('stats', 'recentNews', 'recentRequests'));
    }
}
