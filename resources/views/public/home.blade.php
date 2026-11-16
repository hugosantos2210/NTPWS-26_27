@extends('layouts.app')

@section('title', __('messages.home') . ' — PawSafe')

@section('content')
<!-- Hero Section -->
<section class="hero">
    <div class="container">
        <div class="hero-grid">
            <div>
                <span class="hero-tag">{{ __('messages.hero_badge') }}</span>
                <h1 class="hero-title">{{ __('messages.hero_title') }} <span>{{ __('messages.hero_title_highlight') }}</span>.</h1>
                <p class="hero-subtitle">
                    {{ __('messages.hero_subtitle') }}
                </p>
                <div class="hero-cta">
                    <a href="{{ route('pets.index') }}" class="btn btn-primary btn-lg">{{ __('messages.find_friend_btn') }}</a>
                    <a href="{{ route('news.index') }}" class="btn btn-outline btn-lg">{{ __('messages.read_news_btn') }}</a>
                </div>

                <div class="hero-stats">
                    <div class="stat-item">
                        <span class="stat-number">{{ $stats['total_pets'] ?? 0 }}+</span>
                        <span class="stat-label">{{ __('messages.rescued_animals') }}</span>
                    </div>
                    <div class="stat-item">
                        <span class="stat-number">{{ $stats['adopted'] ?? 0 }}</span>
                        <span class="stat-label">{{ __('messages.adopted_happy') }}</span>
                    </div>
                    <div class="stat-item">
                        <span class="stat-number">{{ $stats['available'] ?? 0 }}</span>
                        <span class="stat-label">{{ __('messages.waiting_home') }}</span>
                    </div>
                </div>
            </div>

            <div>
                <div style="background:linear-gradient(135deg, rgba(13,148,136,0.1), rgba(99,102,241,0.15)); padding:2rem; border-radius:var(--radius-lg); border:1px solid var(--border); text-align:center;">
                    <div style="font-size:4rem; margin-bottom:1rem;">🐶🐱</div>
                    <h3>{{ __('messages.ready_to_adopt') }}</h3>
                    <p style="color:var(--text-muted); font-size:0.95rem; margin-top:0.5rem;">
                        {{ __('messages.ready_to_adopt_desc') }}
                    </p>
                    <a href="{{ route('about') }}" class="btn btn-accent" style="margin-top:1rem;">{{ __('messages.how_it_works_btn') }}</a>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Available Pets Section -->
<section style="padding: 4rem 0;">
    <div class="container">
        <div style="display:flex; justify-content:space-between; align-items:flex-end; margin-bottom:2rem;">
            <div>
                <span class="badge badge-primary">{{ __('messages.pets_badge') }}</span>
                <h2 style="margin-top:0.5rem;">{{ __('messages.looking_for_family') }}</h2>
            </div>
            <a href="{{ route('pets.index') }}" class="btn btn-outline">{{ __('messages.view_all_pets') }}</a>
        </div>

        <div class="card-grid">
            @forelse($availablePets as $pet)
                <div class="card">
                    <div class="card-img-wrapper">
                        <img src="{{ $pet->image_url }}" alt="{{ $pet->name }}" class="card-img" onerror="this.onerror=null;this.src='https://images.unsplash.com/photo-1543466835-00a7907e9de1?auto=format&fit=crop&w=600&q=80';">
                        <div class="card-badge">
                            <span class="badge badge-success">{{ ucfirst($pet->species) }}</span>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="card-meta">
                            <span>{{ __('messages.size') }}: <strong>{{ ucfirst($pet->size) }}</strong></span>
                            <span>•</span>
                            <span>{{ $pet->age_years }} {{ __('messages.years') }}</span>
                            <span>•</span>
                            <span>{{ $pet->gender == 'male' ? __('messages.male') : __('messages.female') }}</span>
                        </div>
                        <h3 class="card-title"><a href="{{ route('pets.show', $pet) }}">{{ $pet->name }}</a></h3>
                        <p class="card-text">{{ Str::limit($pet->description, 100) }}</p>
                    </div>
                    <div class="card-footer">
                        <span style="font-size:0.85rem; font-weight:600; color:var(--primary);">{{ $pet->breed }}</span>
                        <a href="{{ route('pets.show', $pet) }}" class="btn btn-primary btn-sm">{{ __('messages.meet_pet') }}</a>
                    </div>
                </div>
            @empty
                <p>{{ __('messages.no_pets') }}</p>
            @endforelse
        </div>
    </div>
</section>

<!-- Latest News Section -->
<section style="padding: 3rem 0 5rem; background:var(--bg-muted);">
    <div class="container">
        <div style="display:flex; justify-content:space-between; align-items:flex-end; margin-bottom:2rem;">
            <div>
                <span class="badge badge-accent">{{ __('messages.news_badge') }}</span>
                <h2 style="margin-top:0.5rem;">{{ __('messages.latest_news_title') }}</h2>
            </div>
            <a href="{{ route('news.index') }}" class="btn btn-outline">{{ __('messages.view_all_news') }}</a>
        </div>

        <div class="card-grid">
            @forelse($latestNews as $article)
                <div class="card">
                    <div class="card-img-wrapper" style="height:180px;">
                        <img src="{{ $article->image_url }}" alt="{{ $article->title }}" class="card-img" onerror="this.onerror=null;this.src='https://images.unsplash.com/photo-1548767797-d8c844163c4c?auto=format&fit=crop&w=600&q=80';">
                        @if($article->category)
                            <div class="card-badge">
                                <span class="badge badge-primary">{{ $article->category->name }}</span>
                            </div>
                        @endif
                    </div>
                    <div class="card-body">
                        <div class="card-meta">
                            <span>📅 {{ $article->published_at ? $article->published_at->format('d/m/Y') : 'Recente' }}</span>
                            <span>•</span>
                            <span>✍️ {{ $article->author->name ?? 'Equipa' }}</span>
                        </div>
                        <h3 class="card-title"><a href="{{ route('news.show', $article->slug) }}">{{ $article->title }}</a></h3>
                        <p class="card-text">{{ Str::limit($article->summary, 110) }}</p>
                    </div>
                    <div class="card-footer">
                        <a href="{{ route('news.show', $article->slug) }}" style="font-weight:600; font-size:0.9rem;">{{ __('messages.read_more') }}</a>
                    </div>
                </div>
            @empty
                <p>{{ __('messages.no_news') }}</p>
            @endforelse
        </div>
    </div>
</section>
@endsection
