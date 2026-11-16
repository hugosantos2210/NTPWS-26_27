@extends('layouts.app')

@section('title', __('messages.news') . ' — PawSafe')

@section('content')
<div class="container" style="padding: 3.5rem 1.5rem;">
    <div style="margin-bottom: 2rem;">
        <span class="badge badge-accent">{{ __('messages.shelter_updates') }}</span>
        <h1 style="margin-top: 0.5rem;">{{ __('messages.news_events_tips') }}</h1>
        <p style="color: var(--text-muted); font-size: 1.05rem;">{{ __('messages.news_subtitle') }}</p>
    </div>

    <!-- Search & Filter Bar -->
    <form action="{{ route('news.index') }}" method="GET" class="filter-bar">
        <div style="flex: 1; min-width: 250px;">
            <input type="text" name="search" value="{{ $search }}" placeholder="{{ __('messages.search_placeholder') }}" class="form-control">
        </div>

        <div style="min-width: 200px;">
            <select name="category" class="form-select">
                <option value="">{{ __('messages.all_categories') }}</option>
                @foreach($categories as $category)
                    <option value="{{ $category->slug }}" {{ $categorySlug == $category->slug ? 'selected' : '' }}>
                        {{ $category->name }}
                    </option>
                @endforeach
            </select>
        </div>

        <button type="submit" class="btn btn-primary">{{ __('messages.filter_btn') }}</button>
        @if($search || $categorySlug)
            <a href="{{ route('news.index') }}" class="btn btn-outline">{{ __('messages.clear_filters') }}</a>
        @endif
    </form>

    <!-- News List -->
    @if($news->count() > 0)
        <div class="card-grid">
            @foreach($news as $article)
                <div class="card">
                    <div class="card-img-wrapper" style="height: 200px;">
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
                        <p class="card-text">{{ $article->summary }}</p>
                    </div>
                    <div class="card-footer">
                        <a href="{{ route('news.show', $article->slug) }}" class="btn btn-primary btn-sm" style="width:100%;">{{ __('messages.read_full_article') }}</a>
                    </div>
                </div>
            @endforeach
        </div>

        <!-- Pagination -->
        <div class="pagination-container">
            {{ $news->withQueryString()->links() }}
        </div>
    @else
        <div style="text-align: center; padding: 4rem 1rem; background: #fff; border-radius: var(--radius-lg); border: 1px solid var(--border);">
            <div style="font-size: 3rem; margin-bottom: 1rem;">🔍</div>
            <h3>{{ __('messages.no_articles_found') }}</h3>
            <p style="color: var(--text-muted); margin-top: 0.5rem;">{{ __('messages.adjust_search') }}</p>
            <a href="{{ route('news.index') }}" class="btn btn-primary" style="margin-top: 1rem;">{{ __('messages.view_all_articles') }}</a>
        </div>
    @endif
</div>

<!-- API REST Widget -->
<div class="container" style="padding: 0 1.5rem 3.5rem;">
    <div style="background: #fff; border: 1px solid var(--border); border-radius: var(--radius-lg); box-shadow: var(--shadow-sm); overflow: hidden;">
        <div style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); padding: 1.25rem 1.5rem; display: flex; align-items: center; gap: 0.75rem;">
            <span style="background: rgba(255,255,255,0.2); color: #fff; font-size: 0.7rem; font-weight: 700; letter-spacing: 0.08em; padding: 0.2rem 0.6rem; border-radius: 999px; text-transform: uppercase;">{{ __('messages.api_loaded_badge') }}</span>
            <h2 style="color: #fff; margin: 0; font-size: 1.1rem; font-weight: 600;">{{ __('messages.api_highlights') }}</h2>
        </div>
        <div style="padding: 1rem 1.5rem; background: #f8f7ff; border-bottom: 1px solid var(--border);">
            <p style="margin: 0; font-size: 0.85rem; color: var(--text-muted); font-style: italic;">{{ __('messages.api_note') }}</p>
        </div>
        <div id="api-news-widget" style="padding: 1.5rem;">
            <div style="text-align: center; padding: 2rem; color: var(--text-muted);">
                <div style="display: inline-block; width: 32px; height: 32px; border: 3px solid #667eea; border-top-color: transparent; border-radius: 50%; animation: spin 0.8s linear infinite;"></div>
                <p style="margin-top: 0.75rem; font-size: 0.9rem;">{{ __('messages.api_loading') }}</p>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
(function () {
    const widget = document.getElementById('api-news-widget');

    fetch('/api/v1/news?per_page=3')
        .then(function (res) {
            if (!res.ok) throw new Error('HTTP ' + res.status);
            return res.json();
        })
        .then(function (json) {
            const articles = json.data || [];
            if (!articles.length) {
                widget.innerHTML = '<p style="color: var(--text-muted); text-align: center; padding: 1rem;">Nenhuma notícia disponível de momento.</p>';
                return;
            }
            const html = articles.map(function (a) {
                const date = a.published_at
                    ? new Date(a.published_at).toLocaleDateString('pt-PT', { day: '2-digit', month: '2-digit', year: 'numeric' })
                    : '—';
                const category = a.category ? a.category.name : '';
                return '<div style="display: flex; gap: 1rem; padding: 0.9rem 0; border-bottom: 1px solid var(--border);">'
                    + '<div style="flex: 1;">'
                    + '<a href="/news/' + a.slug + '" style="font-weight: 600; color: var(--text-primary); text-decoration: none; font-size: 1rem;">' + a.title + '</a>'
                    + '<div style="display: flex; gap: 0.75rem; margin-top: 0.3rem; font-size: 0.8rem; color: var(--text-muted);">'
                    + (category ? '<span style="background:#ede9fe; color:#6d28d9; padding:0.1rem 0.5rem; border-radius:999px;">' + category + '</span>' : '')
                    + '<span>📅 ' + date + '</span>'
                    + '</div>'
                    + '</div>'
                    + '</div>';
            }).join('');
            widget.innerHTML = '<div style="display: flex; flex-direction: column;">' + html + '</div>'
                + '<div style="text-align: right; margin-top: 1rem;">'
                + '<a href="/news" style="font-size: 0.85rem; color: #667eea; text-decoration: none; font-weight: 500;">Ver todas as notícias →</a>'
                + '</div>';
        })
        .catch(function (err) {
            widget.innerHTML = '<p style="color: #dc2626; text-align: center; padding: 1rem;">⚠️ Não foi possível carregar as notícias via API. (' + err.message + ')</p>';
        });
})();
</script>
<style>
@keyframes spin { to { transform: rotate(360deg); } }
</style>
@endpush

@endsection
