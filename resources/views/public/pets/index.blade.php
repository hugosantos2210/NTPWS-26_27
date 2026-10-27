@extends('layouts.app')

@section('title', 'Animais para Adoção — PawSafe')

@section('content')
<div class="container" style="padding: 3.5rem 1.5rem;">
    <div style="margin-bottom: 2rem;">
        <span class="badge badge-primary">Adoção Responsável</span>
        <h1 style="margin-top: 0.5rem;">Nossos Companheiros para Adoção</h1>
        <p style="color: var(--text-muted); font-size: 1.05rem;">Conheça os cães e gatos que esperam por um lar carinhoso e responsável.</p>
    </div>

    <!-- Filter Bar -->
    <form action="{{ route('pets.index') }}" method="GET" class="filter-bar">
        <div style="flex: 1; min-width: 200px;">
            <input type="text" name="search" value="{{ $search }}" placeholder="Procurar por nome ou raça..." class="form-control">
        </div>

        <div style="min-width: 140px;">
            <select name="species" class="form-select">
                <option value="">Espécie (Todas)</option>
                <option value="dog" {{ $species == 'dog' ? 'selected' : '' }}>Cães 🐶</option>
                <option value="cat" {{ $species == 'cat' ? 'selected' : '' }}>Gatos 🐱</option>
            </select>
        </div>

        <div style="min-width: 130px;">
            <select name="size" class="form-select">
                <option value="">Porte (Todos)</option>
                <option value="small" {{ $size == 'small' ? 'selected' : '' }}>Pequeno</option>
                <option value="medium" {{ $size == 'medium' ? 'selected' : '' }}>Médio</option>
                <option value="large" {{ $size == 'large' ? 'selected' : '' }}>Grande</option>
            </select>
        </div>

        <div style="min-width: 130px;">
            <select name="gender" class="form-select">
                <option value="">Sexo (Todos)</option>
                <option value="male" {{ $gender == 'male' ? 'selected' : '' }}>Macho</option>
                <option value="female" {{ $gender == 'female' ? 'selected' : '' }}>Fêmea</option>
            </select>
        </div>

        <button type="submit" class="btn btn-primary">Filtrar</button>
        @if($search || $species || $size || $gender)
            <a href="{{ route('pets.index') }}" class="btn btn-outline">Limpar Filtros</a>
        @endif
    </form>

    <!-- Pets Grid -->
    @if($pets->count() > 0)
        <div class="card-grid">
            @foreach($pets as $pet)
                <div class="card">
                    <div class="card-img-wrapper">
                        <img src="{{ $pet->image_url }}" alt="{{ $pet->name }}" class="card-img">
                        <div class="card-badge">
                            <span class="badge {{ $pet->status == 'available' ? 'badge-success' : 'badge-warning' }}">
                                {{ $pet->status == 'available' ? 'Disponível' : ucfirst($pet->status) }}
                            </span>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="card-meta">
                            <span>{{ $pet->species == 'dog' ? '🐶 Cão' : '🐱 Gato' }}</span>
                            <span>•</span>
                            <span>{{ $pet->gender == 'male' ? 'Macho' : 'Fêmea' }}</span>
                            <span>•</span>
                            <span>{{ $pet->age_years }} anos</span>
                        </div>
                        <h3 class="card-title"><a href="{{ route('pets.show', $pet) }}">{{ $pet->name }}</a></h3>
                        <p class="card-text">{{ Str::limit($pet->description, 100) }}</p>
                    </div>
                    <div class="card-footer">
                        <span style="font-size: 0.85rem; font-weight: 600; color: var(--primary);">
                            {{ $pet->breed }}
                        </span>
                        <a href="{{ route('pets.show', $pet) }}" class="btn btn-primary btn-sm">Ver Detalhes</a>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="pagination-container">
            {{ $pets->withQueryString()->links() }}
        </div>
    @else
        <div style="text-align: center; padding: 4rem 1rem; background: #fff; border-radius: var(--radius-lg); border: 1px solid var(--border);">
            <div style="font-size: 3rem; margin-bottom: 1rem;">🐾</div>
            <h3>Nenhum animal encontrado com estes filtros</h3>
            <p style="color: var(--text-muted); margin-top: 0.5rem;">Tente ajustar as opções de pesquisa.</p>
            <a href="{{ route('pets.index') }}" class="btn btn-primary" style="margin-top: 1rem;">Ver Todos os Animais</a>
        </div>
    @endif
</div>
@endsection
