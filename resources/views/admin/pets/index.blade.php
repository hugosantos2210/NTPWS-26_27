@extends('layouts.admin')

@section('title', 'Gestão de Animais — PawSafe CMS')

@section('content')
<div class="admin-header">
    <div>
        <h1>Animais & Candidaturas de Adoção</h1>
        <p style="color: var(--text-muted); font-size: 0.95rem;">Catálogo de animais acolhidos e processamento de pedidos de adoção</p>
    </div>
    <a href="{{ route('admin.pets.create') }}" class="btn btn-primary">➕ Novo Animal</a>
</div>

<!-- Filters -->
<form action="{{ route('admin.pets.index') }}" method="GET" class="filter-bar">
    <div style="flex: 1; min-width: 200px;">
        <input type="text" name="search" value="{{ $search }}" placeholder="Pesquisar por nome ou raça..." class="form-control">
    </div>

    <div style="min-width: 140px;">
        <select name="species" class="form-select">
            <option value="">Todas Espécies</option>
            <option value="dog" {{ $species == 'dog' ? 'selected' : '' }}>Cães</option>
            <option value="cat" {{ $species == 'cat' ? 'selected' : '' }}>Gatos</option>
        </select>
    </div>

    <div style="min-width: 140px;">
        <select name="status" class="form-select">
            <option value="">Todos Estados</option>
            <option value="available" {{ $status == 'available' ? 'selected' : '' }}>Disponível</option>
            <option value="pending" {{ $status == 'pending' ? 'selected' : '' }}>Pendente</option>
            <option value="adopted" {{ $status == 'adopted' ? 'selected' : '' }}>Adotado</option>
        </select>
    </div>

    <button type="submit" class="btn btn-primary btn-sm">Filtrar</button>
    @if($search || $species || $status)
        <a href="{{ route('admin.pets.index') }}" class="btn btn-outline btn-sm">Limpar</a>
    @endif
</form>

<div class="table-responsive">
    <table class="table">
        <thead>
            <tr>
                <th>Animal</th>
                <th>Espécie</th>
                <th>Raça</th>
                <th>Idade / Porte</th>
                <th>Estado</th>
                <th>Pedidos Adoção</th>
                <th style="text-align: right;">Ações</th>
            </tr>
        </thead>
        <tbody>
            @forelse($pets as $pet)
                <tr>
                    <td style="font-weight: 700;">
                        <a href="{{ route('pets.show', $pet) }}" target="_blank">{{ $pet->name }} ↗</a>
                    </td>
                    <td>{{ $pet->species == 'dog' ? '🐶 Cão' : '🐱 Gato' }}</td>
                    <td>{{ $pet->breed }}</td>
                    <td>{{ $pet->age_years }}a • {{ ucfirst($pet->size) }}</td>
                    <td>
                        <span class="badge {{ $pet->status == 'available' ? 'badge-success' : ($pet->status == 'pending' ? 'badge-warning' : 'badge-primary') }}">
                            {{ ucfirst($pet->status) }}
                        </span>
                    </td>
                    <td>
                        <span class="badge badge-muted">{{ $pet->adoption_requests_count ?? $pet->adoptionRequests()->count() }} pedidos</span>
                    </td>
                    <td style="text-align: right;">
                        <div style="display: inline-flex; gap: 0.35rem;">
                            <a href="{{ route('admin.pets.edit', $pet) }}" class="btn btn-outline btn-sm">Gerir / Editar</a>
                            <form action="{{ route('admin.pets.destroy', $pet) }}" method="POST" onsubmit="return confirm('Tem a certeza que deseja eliminar este registo?');" style="display:inline;">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger btn-sm">Apagar</button>
                            </form>
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" style="text-align: center; padding: 2rem; color: var(--text-muted);">
                        Nenhum animal encontrado.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="pagination-container">
    {{ $pets->withQueryString()->links() }}
</div>
@endsection
