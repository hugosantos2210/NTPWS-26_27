@extends('layouts.admin')

@section('title', 'Categorias — PawSafe CMS')

@section('content')
<div class="admin-header">
    <div>
        <h1>Gestão de Categorias</h1>
        <p style="color: var(--text-muted); font-size: 0.95rem;">Organização temática das notícias e artigos do abrigo</p>
    </div>
    <a href="{{ route('admin.categories.create') }}" class="btn btn-primary">➕ Nova Categoria</a>
</div>

<div class="table-responsive">
    <table class="table">
        <thead>
            <tr>
                <th>Nome da Categoria</th>
                <th>Slug</th>
                <th>Descrição</th>
                <th>Nº Artigos</th>
                <th style="text-align: right;">Ações</th>
            </tr>
        </thead>
        <tbody>
            @forelse($categories as $category)
                <tr>
                    <td style="font-weight: 700; color: var(--primary);">{{ $category->name }}</td>
                    <td><code>{{ $category->slug }}</code></td>
                    <td style="color: var(--text-muted); font-size: 0.9rem;">{{ $category->description ?? '—' }}</td>
                    <td>
                        <span class="badge badge-primary">{{ $category->news_count ?? $category->news()->count() }} artigos</span>
                    </td>
                    <td style="text-align: right;">
                        <div style="display: inline-flex; gap: 0.35rem;">
                            <a href="{{ route('admin.categories.edit', $category) }}" class="btn btn-outline btn-sm">Editar</a>
                            <form action="{{ route('admin.categories.destroy', $category) }}" method="POST" onsubmit="return confirm('Tem a certeza que deseja eliminar esta categoria?');" style="display:inline;">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger btn-sm">Apagar</button>
                            </form>
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" style="text-align: center; padding: 2rem; color: var(--text-muted);">
                        Nenhuma categoria registada.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection
