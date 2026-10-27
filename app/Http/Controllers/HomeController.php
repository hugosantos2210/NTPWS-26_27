<?php

namespace App\Http\Controllers;

use App\Models\News;
use App\Models\Pet;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    /**
     * Display the home page with featured content.
     */
    public function index()
    {
        $latestNews = News::published()
            ->with(['category', 'author'])
            ->orderByDesc('published_at')
            ->take(3)
            ->get();

        $availablePets = Pet::available()
            ->orderByDesc('created_at')
            ->take(6)
            ->get();

        $stats = [
            'total_pets' => Pet::count(),
            'adopted' => Pet::where('status', 'adopted')->count(),
            'available' => Pet::where('status', 'available')->count(),
        ];

        return view('public.home', compact('latestNews', 'availablePets', 'stats'));
    }

    /**
     * Display the about page.
     */
    public function about()
    {
        return view('public.about');
    }
}
