@extends('layouts.admin')

@section('title', 'Painel Geral — PawSafe CMS')

@section('content')
<div class="admin-header">
    <div>
        <h1>Painel de Gestão & Controlo</h1>
        <p style="color: var(--text-muted); font-size: 0.95rem;">Resumo executivo do abrigo PawSafe</p>
    </div>
    <div>
        <a href="{{ route('admin.news.create') }}" class="btn btn-primary btn-sm">➕ Nova Notícia</a>
        @if(auth()->user()->isAdmin())
            <a href="{{ route('admin.pets.create') }}" class="btn btn-accent btn-sm" style="margin-left: 0.5rem;">➕ Novo Animal</a>
        @endif
    </div>
</div>

<!-- KPI Cards Grid -->
<div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 1.5rem; margin-bottom: 2.5rem;">
    <div style="background: #fff; padding: 1.5rem; border-radius: var(--radius-lg); border: 1px solid var(--border); box-shadow: var(--shadow-sm);">
        <div style="font-size: 0.85rem; color: var(--text-muted); font-weight: 600;">TOTAL DE NOTÍCIAS</div>
        <div style="font-size: 2.2rem; font-weight: 800; color: var(--primary); margin: 0.35rem 0;">{{ $stats['total_news'] ?? 0 }}</div>
        <div style="font-size: 0.8rem; color: var(--text-light);">{{ $stats['published_news'] ?? 0 }} publicadas • {{ $stats['draft_news'] ?? 0 }} rascunhos</div>
    </div>

    <div style="background: #fff; padding: 1.5rem; border-radius: var(--radius-lg); border: 1px solid var(--border); box-shadow: var(--shadow-sm);">
        <div style="font-size: 0.85rem; color: var(--text-muted); font-weight: 600;">ANIMAIS NO ABRIGO</div>
        <div style="font-size: 2.2rem; font-weight: 800; color: var(--secondary); margin: 0.35rem 0;">{{ $stats['total_pets'] ?? 0 }}</div>
        <div style="font-size: 0.8rem; color: var(--text-light);">{{ $stats['available_pets'] ?? 0 }} disponíveis para adoção</div>
    </div>

    <div style="background: #fff; padding: 1.5rem; border-radius: var(--radius-lg); border: 1px solid var(--border); box-shadow: var(--shadow-sm);">
        <div style="font-size: 0.85rem; color: var(--text-muted); font-weight: 600;">PEDIDOS DE ADOÇÃO</div>
        <div style="font-size: 2.2rem; font-weight: 800; color: var(--accent); margin: 0.35rem 0;">{{ $stats['total_requests'] ?? 0 }}</div>
        <div style="font-size: 0.8rem; color: #b45309; font-weight: 600;">{{ $stats['pending_requests'] ?? 0 }} pendentes de análise</div>
    </div>

    @if(auth()->user()->isAdmin())
    <div style="background: #fff; padding: 1.5rem; border-radius: var(--radius-lg); border: 1px solid var(--border); box-shadow: var(--shadow-sm);">
        <div style="font-size: 0.85rem; color: var(--text-muted); font-weight: 600;">UTILIZADORES REGISTADOS</div>
        <div style="font-size: 2.2rem; font-weight: 800; color: var(--info); margin: 0.35rem 0;">{{ $stats['total_users'] ?? 0 }}</div>
        <div style="font-size: 0.8rem; color: var(--text-light);">{{ $stats['active_users'] ?? 0 }} utilizadores ativos</div>
    </div>
    @endif
</div>

<!-- Two-column: Recent News & Recent Adoption Requests -->
<div style="display: grid; grid-template-columns: 1fr 1fr; gap: 2rem;">
    <!-- Recent News -->
    <div style="background: #fff; padding: 1.75rem; border-radius: var(--radius-lg); border: 1px solid var(--border); box-shadow: var(--shadow-sm);">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.25rem;">
            <h3>Últimas Notícias Editadas</h3>
            <a href="{{ route('admin.news.index') }}" class="btn btn-outline btn-sm">Ver Todas</a>
        </div>

        @if(isset($recentNews) && $recentNews->count() > 0)
            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Título</th>
                            <th>Categoria</th>
                            <th>Estado</th>
                            <th>Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($recentNews as $article)
                            <tr>
                                <td style="font-weight: 600; max-width: 180px;">{{ Str::limit($article->title, 28) }}</td>
                                <td>{{ $article->category->name ?? 'Geral' }}</td>
                                <td>
                                    <span class="badge {{ $article->status == 'published' ? 'badge-success' : ($article->status == 'draft' ? 'badge-warning' : 'badge-danger') }}">
                                        {{ ucfirst($article->status) }}
                                    </span>
                                </td>
                                <td>
                                    <a href="{{ route('admin.news.edit', $article) }}" class="btn btn-outline btn-sm">Editar</a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <p style="color: var(--text-muted); font-size: 0.9rem;">Sem notícias recentes.</p>
        @endif
    </div>

    <!-- Recent Adoption Requests -->
    <div style="background: #fff; padding: 1.75rem; border-radius: var(--radius-lg); border: 1px solid var(--border); box-shadow: var(--shadow-sm);">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.25rem;">
            <h3>Pedidos de Adoção Recentes</h3>
            @if(auth()->user()->isAdmin())
                <a href="{{ route('admin.pets.index') }}" class="btn btn-outline btn-sm">Gerir Animais</a>
            @endif
        </div>

        @if(isset($recentRequests) && $recentRequests->count() > 0)
            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Animal</th>
                            <th>Candidato</th>
                            <th>Estado</th>
                            <th>Data</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($recentRequests as $request)
                            <tr>
                                <td style="font-weight: 600;">{{ $request->pet->name ?? 'N/A' }}</td>
                                <td>{{ $request->user->name ?? 'N/A' }}</td>
                                <td>
                                    <span class="badge {{ $request->status == 'approved' ? 'badge-success' : ($request->status == 'pending' ? 'badge-warning' : 'badge-danger') }}">
                                        {{ ucfirst($request->status) }}
                                    </span>
                                </td>
                                <td style="font-size: 0.8rem; color: var(--text-muted);">{{ $request->created_at->format('d/m/Y') }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <p style="color: var(--text-muted); font-size: 0.9rem;">Sem pedidos recentes de adoção.</p>
        @endif
    </div>
</div>
@endsection
