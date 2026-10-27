@extends('layouts.admin')

@section('title', 'Novo Animal — PawSafe CMS')

@section('content')
<div class="admin-header">
    <div>
        <h1>Registar Novo Animal</h1>
        <p style="color: var(--text-muted); font-size: 0.95rem;">Adicione uma ficha de cão ou gato acolhido no abrigo</p>
    </div>
    <a href="{{ route('admin.pets.index') }}" class="btn btn-outline btn-sm">← Voltar à Lista</a>
</div>

<div style="background: #fff; padding: 2rem; border-radius: var(--radius-lg); border: 1px solid var(--border); box-shadow: var(--shadow-sm); max-width: 860px;">
    <form action="{{ route('admin.pets.store') }}" method="POST" enctype="multipart/form-data">
        @csrf

        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem;">
            <div class="form-group">
                <label for="name" class="form-label">Nome do Animal *</label>
                <input type="text" name="name" id="name" value="{{ old('name') }}" class="form-control" required placeholder="Ex: Bobi">
                @error('name') <div class="form-error">{{ $message }}</div> @enderror
            </div>

            <div class="form-group">
                <label for="species" class="form-label">Espécie *</label>
                <select name="species" id="species" class="form-select" required>
                    <option value="dog" {{ old('species') == 'dog' ? 'selected' : '' }}>Cão (TheDogAPI)</option>
                    <option value="cat" {{ old('species') == 'cat' ? 'selected' : '' }}>Gato (TheCatAPI)</option>
                </select>
                @error('species') <div class="form-error">{{ $message }}</div> @enderror
            </div>
        </div>

        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem;">
            <div class="form-group">
                <label for="breed" class="form-label">Raça *</label>
                <input type="text" name="breed" id="breed" value="{{ old('breed') }}" class="form-control" required placeholder="Ex: Golden Retriever, Siamese, etc.">
                <small style="color: var(--text-muted); font-size: 0.8rem;">Raças reconhecidas consultarão automaticamente dados enciclopédicos da API externa.</small>
                @error('breed') <div class="form-error">{{ $message }}</div> @enderror
            </div>

            <div class="form-group">
                <label for="gender" class="form-label">Sexo *</label>
                <select name="gender" id="gender" class="form-select" required>
                    <option value="male" {{ old('gender') == 'male' ? 'selected' : '' }}>Macho</option>
                    <option value="female" {{ old('gender') == 'female' ? 'selected' : '' }}>Fêmea</option>
                </select>
                @error('gender') <div class="form-error">{{ $message }}</div> @enderror
            </div>
        </div>

        <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 1.5rem;">
            <div class="form-group">
                <label for="size" class="form-label">Porte *</label>
                <select name="size" id="size" class="form-select" required>
                    <option value="small" {{ old('size') == 'small' ? 'selected' : '' }}>Pequeno</option>
                    <option value="medium" {{ old('size', 'medium') == 'medium' ? 'selected' : '' }}>Médio</option>
                    <option value="large" {{ old('size') == 'large' ? 'selected' : '' }}>Grande</option>
                </select>
                @error('size') <div class="form-error">{{ $message }}</div> @enderror
            </div>

            <div class="form-group">
                <label for="age_years" class="form-label">Idade (Anos) *</label>
                <input type="number" name="age_years" id="age_years" value="{{ old('age_years', 1) }}" min="0" max="30" class="form-control" required>
                @error('age_years') <div class="form-error">{{ $message }}</div> @enderror
            </div>

            <div class="form-group">
                <label for="age_months" class="form-label">Idade (Meses) *</label>
                <input type="number" name="age_months" id="age_months" value="{{ old('age_months', 0) }}" min="0" max="11" class="form-control" required>
                @error('age_months') <div class="form-error">{{ $message }}</div> @enderror
            </div>
        </div>

        <div class="form-group">
            <label for="status" class="form-label">Estado de Adoção *</label>
            <select name="status" id="status" class="form-select" required>
                <option value="available" {{ old('status', 'available') == 'available' ? 'selected' : '' }}>Disponível para Adoção</option>
                <option value="pending" {{ old('status') == 'pending' ? 'selected' : '' }}>Em Processo de Adoção (Pendente)</option>
                <option value="adopted" {{ old('status') == 'adopted' ? 'selected' : '' }}>Adotado (Concluído)</option>
            </select>
            @error('status') <div class="form-error">{{ $message }}</div> @enderror
        </div>

        <div class="form-group">
            <label for="description" class="form-label">Descrição & Temperamento *</label>
            <textarea name="description" id="description" rows="4" class="form-control" required placeholder="História do resgate, personalidade, convivência com outros animais...">{{ old('description') }}</textarea>
            @error('description') <div class="form-error">{{ $message }}</div> @enderror
        </div>

        <div class="form-group">
            <label for="image" class="form-label">Foto do Animal (Upload JPG, PNG, WEBP máx. 2MB)</label>
            <input type="file" name="image" id="image" class="form-control" accept="image/*">
            @error('image') <div class="form-error">{{ $message }}</div> @enderror
        </div>

        <div style="display: flex; gap: 1rem; margin-top: 2rem;">
            <button type="submit" class="btn btn-primary btn-lg">Registar Animal</button>
            <a href="{{ route('admin.pets.index') }}" class="btn btn-outline btn-lg">Cancelar</a>
        </div>
    </form>
</div>
@endsection
