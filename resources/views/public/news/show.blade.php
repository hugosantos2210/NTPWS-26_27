@extends('layouts.app')

@section('title', $article->title . ' — PawSafe')
@section('meta_description', $article->summary)

@section('content')
<div class="container" style="padding: 3.5rem 1.5rem;">
    <div style="max-width: 860px; margin: 0 auto;">
        <div style="margin-bottom: 1.5rem;">
            <a href="{{ route('news.index') }}" style="font-weight: 600; font-size: 0.9rem;">← Voltar às Notícias</a>
        </div>

        @if($article->category)
            <span class="badge badge-primary" style="margin-bottom: 0.75rem;">{{ $article->category->name }}</span>
        @endif

        <h1 style="font-size: 2.8rem; margin-bottom: 1rem;">{{ $article->title }}</h1>

        <div class="card-meta" style="font-size: 0.95rem; margin-bottom: 2rem; padding-bottom: 1rem; border-bottom: 1px solid var(--border);">
            <span>📅 Publicado em: <strong>{{ $article->published_at ? $article->published_at->format('d/m/Y \à\s H:i') : 'Data não disponível' }}</strong></span>
            <span>•</span>
            <span>✍️ Autor: <strong>{{ $article->author->name ?? 'Equipa PawSafe' }}</strong></span>
        </div>

        @if($article->image_url)
            <div style="margin-bottom: 2.5rem; border-radius: var(--radius-lg); overflow: hidden; max-height: 440px; box-shadow: var(--shadow-md);">
                <img src="{{ $article->image_url }}" alt="{{ $article->title }}" style="width: 100%; height: 100%; object-fit: cover;">
            </div>
        @endif

        <div style="background: var(--bg-muted); padding: 1.25rem 1.5rem; border-radius: var(--radius-md); border-left: 4px solid var(--primary); font-size: 1.1rem; font-weight: 500; margin-bottom: 2rem;">
            {{ $article->summary }}
        </div>

        <!-- Article Content (Clean HTML) -->
        <div style="font-size: 1.05rem; line-height: 1.8; color: var(--text-main); margin-bottom: 3rem;">
            {!! $article->content !!}
        </div>

        <!-- Related News -->
        @if(isset($relatedNews) && $relatedNews->count() > 0)
            <div style="margin-top: 4rem; padding-top: 2.5rem; border-top: 1px solid var(--border);">
                <h3>Notícias Relacionadas</h3>
                <div class="card-grid" style="margin-top: 1.5rem; grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));">
                    @foreach($relatedNews as $related)
                        <div class="card">
                            <div class="card-body">
                                <span style="font-size: 0.8rem; color: var(--text-muted);">{{ $related->published_at ? $related->published_at->format('d/m/Y') : '' }}</span>
                                <h4 style="margin: 0.35rem 0 0.5rem;"><a href="{{ route('news.show', $related->slug) }}">{{ $related->title }}</a></h4>
                                <p style="font-size: 0.85rem; color: var(--text-muted);">{{ Str::limit($related->summary, 80) }}</p>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif
    </div>
</div>
@endsection
