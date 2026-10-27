@extends('layouts.admin')

@section('title', 'Criar Notícia — PawSafe CMS')

@section('content')
<div class="admin-header">
    <div>
        <h1>Nova Publicação</h1>
        <p style="color: var(--text-muted); font-size: 0.95rem;">Adicione uma notícia, dica ou anúncio ao abrigo</p>
    </div>
    <a href="{{ route('admin.news.index') }}" class="btn btn-outline btn-sm">← Voltar à Lista</a>
</div>

<div style="background: #fff; padding: 2rem; border-radius: var(--radius-lg); border: 1px solid var(--border); box-shadow: var(--shadow-sm); max-width: 860px;">
    <form action="{{ route('admin.news.store') }}" method="POST" enctype="multipart/form-data">
        @csrf

        <div class="form-group">
            <label for="title" class="form-label">Título da Notícia *</label>
            <input type="text" name="title" id="title" value="{{ old('title') }}" class="form-control" required placeholder="Ex: Campanha de Adoção de Primavera">
            @error('title') <div class="form-error">{{ $message }}</div> @enderror
        </div>

        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem;">
            <div class="form-group">
                <label for="category_id" class="form-label">Categoria *</label>
                <select name="category_id" id="category_id" class="form-select" required>
                    <option value="">Selecione uma categoria</option>
                    @foreach($categories as $category)
                        <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>
                            {{ $category->name }}
                        </option>
                    @endforeach
                </select>
                @error('category_id') <div class="form-error">{{ $message }}</div> @enderror
            </div>

            <div class="form-group">
                <label for="status" class="form-label">Estado de Publicação *</label>
                <select name="status" id="status" class="form-select" required>
                    <option value="draft" {{ old('status') == 'draft' ? 'selected' : '' }}>Rascunho (Privado)</option>
                    <option value="published" {{ old('status', 'published') == 'published' ? 'selected' : '' }}>Publicado (Visível no site)</option>
                    <option value="withdrawn" {{ old('status') == 'withdrawn' ? 'selected' : '' }}>Retirado (Oculto)</option>
                </select>
                @error('status') <div class="form-error">{{ $message }}</div> @enderror
            </div>
        </div>

        <div class="form-group">
            <label for="summary" class="form-label">Resumo / Lead * (máx. 300 caracteres)</label>
            <textarea name="summary" id="summary" rows="2" class="form-control" required placeholder="Breve resumo que surge nas listagens e no topo do artigo...">{{ old('summary') }}</textarea>
            @error('summary') <div class="form-error">{{ $message }}</div> @enderror
        </div>

        <div class="form-group">
            <label for="content" class="form-label">Conteúdo Completo * (HTML suportado com sanitização segura)</label>
            <textarea name="content" id="content" rows="8" class="form-control" required placeholder="Escreva o texto completo do artigo com formatação HTML permitida (parágrafos, negrito, listas)...">{{ old('content') }}</textarea>
            @error('content') <div class="form-error">{{ $message }}</div> @enderror
        </div>

        <div class="form-group">
            <label for="image" class="form-label">Imagem de Capa (Upload opcional - JPG, PNG, WEBP máx. 2MB)</label>
            <input type="file" name="image" id="image" class="form-control" accept="image/*">
            @error('image') <div class="form-error">{{ $message }}</div> @enderror
        </div>

        <div style="display: flex; gap: 1rem; margin-top: 2rem;">
            <button type="submit" class="btn btn-primary btn-lg">Publicar Notícia</button>
            <a href="{{ route('admin.news.index') }}" class="btn btn-outline btn-lg">Cancelar</a>
        </div>
    </form>
</div>
@endsection
