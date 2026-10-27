@extends('layouts.admin')

@section('title', 'Editar Categoria — PawSafe CMS')

@section('content')
<div class="admin-header">
    <div>
        <h1>Editar Categoria</h1>
        <p style="color: var(--text-muted); font-size: 0.95rem;">A alterar a categoria: <strong>{{ $category->name }}</strong></p>
    </div>
    <a href="{{ route('admin.categories.index') }}" class="btn btn-outline btn-sm">← Voltar à Lista</a>
</div>

<div style="background: #fff; padding: 2rem; border-radius: var(--radius-lg); border: 1px solid var(--border); box-shadow: var(--shadow-sm); max-width: 600px;">
    <form action="{{ route('admin.categories.update', $category) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="form-group">
            <label for="name" class="form-label">Nome da Categoria *</label>
            <input type="text" name="name" id="name" value="{{ old('name', $category->name) }}" class="form-control" required>
            @error('name') <div class="form-error">{{ $message }}</div> @enderror
        </div>

        <div class="form-group">
            <label for="description" class="form-label">Descrição (Opcional)</label>
            <textarea name="description" id="description" rows="3" class="form-control">{{ old('description', $category->description) }}</textarea>
            @error('description') <div class="form-error">{{ $message }}</div> @enderror
        </div>

        <div style="display: flex; gap: 1rem; margin-top: 1.5rem;">
            <button type="submit" class="btn btn-primary">Atualizar Categoria</button>
            <a href="{{ route('admin.categories.index') }}" class="btn btn-outline">Cancelar</a>
        </div>
    </form>
</div>
@endsection
