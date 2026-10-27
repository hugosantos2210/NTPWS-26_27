@extends('layouts.admin')

@section('title', 'Nova Categoria — PawSafe CMS')

@section('content')
<div class="admin-header">
    <div>
        <h1>Nova Categoria</h1>
        <p style="color: var(--text-muted); font-size: 0.95rem;">Adicione uma categoria temática para classificar notícias</p>
    </div>
    <a href="{{ route('admin.categories.index') }}" class="btn btn-outline btn-sm">← Voltar à Lista</a>
</div>

<div style="background: #fff; padding: 2rem; border-radius: var(--radius-lg); border: 1px solid var(--border); box-shadow: var(--shadow-sm); max-width: 600px;">
    <form action="{{ route('admin.categories.store') }}" method="POST">
        @csrf

        <div class="form-group">
            <label for="name" class="form-label">Nome da Categoria *</label>
            <input type="text" name="name" id="name" value="{{ old('name') }}" class="form-control" required placeholder="Ex: Histórias de Adoção">
            @error('name') <div class="form-error">{{ $message }}</div> @enderror
        </div>

        <div class="form-group">
            <label for="description" class="form-label">Descrição (Opcional)</label>
            <textarea name="description" id="description" rows="3" class="form-control" placeholder="Breve explicação da temática desta categoria...">{{ old('description') }}</textarea>
            @error('description') <div class="form-error">{{ $message }}</div> @enderror
        </div>

        <div style="display: flex; gap: 1rem; margin-top: 1.5rem;">
            <button type="submit" class="btn btn-primary">Guardar Categoria</button>
            <a href="{{ route('admin.categories.index') }}" class="btn btn-outline">Cancelar</a>
        </div>
    </form>
</div>
@endsection
