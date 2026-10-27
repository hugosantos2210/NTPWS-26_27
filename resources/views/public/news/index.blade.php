@extends('layouts.app')

@section('title', 'Notícias & Campanhas — PawSafe')

@section('content')
<div class="container" style="padding: 3.5rem 1.5rem;">
    <div style="margin-bottom: 2rem;">
        <span class="badge badge-accent">Novidades do Abrigo</span>
        <h1 style="margin-top: 0.5rem;">Notícias, Eventos & Dicas</h1>
        <p style="color: var(--text-muted); font-size: 1.05rem;">Fique a par das histórias de sucesso, campanhas de vacinação e eventos comunitários.</p>
    </div>

    <!-- Search & Filter Bar -->
    <form action="{{ route('news.index') }}" method="GET" class="filter-bar">
        <div style="flex: 1; min-width: 250px;">
            <input type="text" name="search" value="{{ $search }}" placeholder="Pesquisar por título ou conteúdo..." class="form-control">
        </div>

        <div style="min-width: 200px;">
            <select name="category" class="form-select">
                <option value="">Todas as Categorias</option>
                @foreach($categories as $category)
                    <option value="{{ $category->slug }}" {{ $categorySlug == $category->slug ? 'selected' : '' }}>
                        {{ $category->name }}
                    </option>
                @endforeach
            </select>
        </div>

        <button type="submit" class="btn btn-primary">Filtrar</button>
        @if($search || $categorySlug)
            <a href="{{ route('news.index') }}" class="btn btn-outline">Limpar Filtros</a>
        @endif
    </form>

    <!-- News List -->
    @if($news->count() > 0)
        <div class="card-grid">
            @foreach($news as $article)
                <div class="card">
                    <div class="card-img-wrapper" style="height: 200px;">
                        <img src="{{ $article->image_url }}" alt="{{ $article->title }}" class="card-img">
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
                        <a href="{{ route('news.show', $article->slug) }}" class="btn btn-primary btn-sm" style="width:100%;">Ler Artigo Completo ➔</a>
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
            <h3>Nenhum artigo encontrado</h3>
            <p style="color: var(--text-muted); margin-top: 0.5rem;">Tente ajustar os termos de pesquisa ou remover os filtros aplicados.</p>
            <a href="{{ route('news.index') }}" class="btn btn-primary" style="margin-top: 1rem;">Ver Todos os Artigos</a>
        </div>
    @endif
</div>
@endsection
