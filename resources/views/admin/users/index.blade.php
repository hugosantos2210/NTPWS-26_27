@extends('layouts.admin')

@section('title', 'Gestão de Utilizadores — PawSafe CMS')

@section('content')
<div class="admin-header">
    <div>
        <h1>Gestão de Utilizadores & Permissões</h1>
        <p style="color: var(--text-muted); font-size: 0.95rem;">Controlo de contas, papéis (Admin, Editor, Utilizador) e estado de ativação</p>
    </div>
</div>

<!-- Filters -->
<form action="{{ route('admin.users.index') }}" method="GET" class="filter-bar">
    <div style="flex: 1; min-width: 200px;">
        <input type="text" name="search" value="{{ $search }}" placeholder="Pesquisar por nome ou email..." class="form-control">
    </div>

    <div style="min-width: 160px;">
        <select name="role" class="form-select">
            <option value="">Todos os Papéis</option>
            @foreach($roles as $role)
                <option value="{{ $role->name }}" {{ $roleFilter == $role->name ? 'selected' : '' }}>
                    {{ ucfirst($role->name) }}
                </option>
            @endforeach
        </select>
    </div>

    <button type="submit" class="btn btn-primary btn-sm">Filtrar</button>
    @if($search || $roleFilter)
        <a href="{{ route('admin.users.index') }}" class="btn btn-outline btn-sm">Limpar</a>
    @endif
</form>

<div class="table-responsive">
    <table class="table">
        <thead>
            <tr>
                <th>Nome</th>
                <th>Email</th>
                <th>Telefone</th>
                <th>Papel (Role)</th>
                <th>Estado</th>
                <th>Data de Registo</th>
                <th style="text-align: right;">Ações</th>
            </tr>
        </thead>
        <tbody>
            @forelse($users as $user)
                <tr>
                    <td style="font-weight: 700;">{{ $user->name }}</td>
                    <td>{{ $user->email }}</td>
                    <td>{{ $user->phone ?? '—' }}</td>
                    <td>
                        <span class="badge {{ $user->isAdmin() ? 'badge-primary' : ($user->isEditor() ? 'badge-accent' : 'badge-muted') }}">
                            {{ $user->role->name ?? 'user' }}
                        </span>
                    </td>
                    <td>
                        <span class="badge {{ $user->is_active ? 'badge-success' : 'badge-danger' }}">
                            {{ $user->is_active ? 'Ativo' : 'Desativado' }}
                        </span>
                    </td>
                    <td style="font-size: 0.85rem; color: var(--text-muted);">
                        {{ $user->created_at->format('d/m/Y') }}
                    </td>
                    <td style="text-align: right;">
                        <a href="{{ route('admin.users.edit', $user) }}" class="btn btn-outline btn-sm">Editar Permissões</a>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" style="text-align: center; padding: 2rem; color: var(--text-muted);">
                        Nenhum utilizador encontrado.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="pagination-container">
    {{ $users->withQueryString()->links() }}
</div>
@endsection
