@extends('layouts.admin')

@section('title', 'Gestão de Notícias — PawSafe CMS')

@section('content')
<div class="admin-header">
    <div>
        <h1>Gestão de Notícias & Artigos</h1>
        <p style="color: var(--text-muted); font-size: 0.95rem;">Criação, edição e moderação do catálogo de publicações</p>
    </div>
    <a href="{{ route('admin.news.create') }}" class="btn btn-primary">➕ Nova Notícia</a>
</div>

<!-- Filters -->
<form action="{{ route('admin.news.index') }}" method="GET" class="filter-bar">
    <div style="flex: 1; min-width: 200px;">
        <input type="text" name="search" value="{{ $search }}" placeholder="Pesquisar por título ou resumo..." class="form-control">
    </div>

    <div style="min-width: 160px;">
        <select name="status" class="form-select">
            <option value="">Todos os Estados</option>
            <option value="published" {{ $status == 'published' ? 'selected' : '' }}>Publicado</option>
            <option value="draft" {{ $status == 'draft' ? 'selected' : '' }}>Rascunho</option>
            <option value="withdrawn" {{ $status == 'withdrawn' ? 'selected' : '' }}>Retirado</option>
        </select>
    </div>

    <button type="submit" class="btn btn-primary btn-sm">Filtrar</button>
    @if($search || $status)
        <a href="{{ route('admin.news.index') }}" class="btn btn-outline btn-sm">Limpar</a>
    @endif
</form>

<div class="table-responsive">
    <table class="table">
        <thead>
            <tr>
                <th>Título</th>
                <th>Categoria</th>
                <th>Autor</th>
                <th>Estado</th>
                <th>Data</th>
                <th style="text-align: right;">Ações</th>
            </tr>
        </thead>
        <tbody>
            @forelse($news as $article)
                <tr>
                    <td style="font-weight: 600;">
                        <a href="{{ route('news.show', $article->slug) }}" target="_blank">{{ $article->title }} ↗</a>
                    </td>
                    <td>{{ $article->category->name ?? 'Sem categoria' }}</td>
                    <td>{{ $article->author->name ?? 'N/A' }}</td>
                    <td>
                        <span class="badge {{ $article->status == 'published' ? 'badge-success' : ($article->status == 'draft' ? 'badge-warning' : 'badge-danger') }}">
                            {{ ucfirst($article->status) }}
                        </span>
                    </td>
                    <td style="font-size: 0.85rem; color: var(--text-muted);">
                        {{ $article->created_at->format('d/m/Y') }}
                    </td>
                    <td style="text-align: right;">
                        <div style="display: inline-flex; gap: 0.35rem;">
                            <a href="{{ route('admin.news.edit', $article) }}" class="btn btn-outline btn-sm">Editar</a>
                            <form action="{{ route('admin.news.destroy', $article) }}" method="POST" onsubmit="return confirm('Tem a certeza que deseja eliminar esta notícia?');" style="display:inline;">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger btn-sm">Apagar</button>
                            </form>
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" style="text-align: center; padding: 2rem; color: var(--text-muted);">
                        Nenhuma notícia encontrada.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="pagination-container">
    {{ $news->withQueryString()->links() }}
</div>
@endsection
