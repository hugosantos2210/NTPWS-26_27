@extends('layouts.admin')

@section('title', 'Editar Notícia — PawSafe CMS')

@section('content')
<div class="admin-header">
    <div>
        <h1>Editar Notícia</h1>
        <p style="color: var(--text-muted); font-size: 0.95rem;">A modificar a publicação: <strong>{{ $news->title }}</strong></p>
    </div>
    <a href="{{ route('admin.news.index') }}" class="btn btn-outline btn-sm">← Voltar à Lista</a>
</div>

<div style="background: #fff; padding: 2rem; border-radius: var(--radius-lg); border: 1px solid var(--border); box-shadow: var(--shadow-sm); max-width: 860px;">
    <form action="{{ route('admin.news.update', $news) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        <div class="form-group">
            <label for="title" class="form-label">Título da Notícia *</label>
            <input type="text" name="title" id="title" value="{{ old('title', $news->title) }}" class="form-control" required>
            @error('title') <div class="form-error">{{ $message }}</div> @enderror
        </div>

        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem;">
            <div class="form-group">
                <label for="category_id" class="form-label">Categoria *</label>
                <select name="category_id" id="category_id" class="form-select" required>
                    <option value="">Selecione uma categoria</option>
                    @foreach($categories as $category)
                        <option value="{{ $category->id }}" {{ old('category_id', $news->category_id) == $category->id ? 'selected' : '' }}>
                            {{ $category->name }}
                        </option>
                    @endforeach
                </select>
                @error('category_id') <div class="form-error">{{ $message }}</div> @enderror
            </div>

            <div class="form-group">
                <label for="status" class="form-label">Estado de Publicação *</label>
                <select name="status" id="status" class="form-select" required>
                    <option value="draft" {{ old('status', $news->status) == 'draft' ? 'selected' : '' }}>Rascunho (Privado)</option>
                    <option value="published" {{ old('status', $news->status) == 'published' ? 'selected' : '' }}>Publicado (Visível no site)</option>
                    <option value="withdrawn" {{ old('status', $news->status) == 'withdrawn' ? 'selected' : '' }}>Retirado (Oculto)</option>
                </select>
                @error('status') <div class="form-error">{{ $message }}</div> @enderror
            </div>
        </div>

        <div class="form-group">
            <label for="summary" class="form-label">Resumo / Lead *</label>
            <textarea name="summary" id="summary" rows="2" class="form-control" required>{{ old('summary', $news->summary) }}</textarea>
            @error('summary') <div class="form-error">{{ $message }}</div> @enderror
        </div>

        <div class="form-group">
            <label for="content" class="form-label">Conteúdo Completo * (HTML suportado com sanitização segura)</label>
            <textarea name="content" id="content" rows="8" class="form-control" required>{{ old('content', $news->content) }}</textarea>
            @error('content') <div class="form-error">{{ $message }}</div> @enderror
        </div>

        <div class="form-group">
            <label for="image" class="form-label">Substituir Imagem de Capa (Opcional)</label>
            @if($news->image_url)
                <div style="margin-bottom: 0.75rem;">
                    <img src="{{ $news->image_url }}" alt="Capa atual" style="height: 100px; border-radius: var(--radius-sm); object-fit: cover;">
                </div>
            @endif
            <input type="file" name="image" id="image" class="form-control" accept="image/*">
            @error('image') <div class="form-error">{{ $message }}</div> @enderror
        </div>

        <div style="display: flex; gap: 1rem; margin-top: 2rem;">
            <button type="submit" class="btn btn-primary btn-lg">Atualizar Notícia</button>
            <a href="{{ route('admin.news.index') }}" class="btn btn-outline btn-lg">Cancelar</a>
        </div>
    </form>
</div>
@endsection
