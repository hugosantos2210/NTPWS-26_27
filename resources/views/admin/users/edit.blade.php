@extends('layouts.admin')

@section('title', 'Editar Utilizador — PawSafe CMS')

@section('content')
<div class="admin-header">
    <div>
        <h1>Editar Utilizador: {{ $user->name }}</h1>
        <p style="color: var(--text-muted); font-size: 0.95rem;">Alteração de papel funcional e ativação/desativação de conta</p>
    </div>
    <a href="{{ route('admin.users.index') }}" class="btn btn-outline btn-sm">← Voltar à Lista</a>
</div>

<div style="background: #fff; padding: 2rem; border-radius: var(--radius-lg); border: 1px solid var(--border); box-shadow: var(--shadow-sm); max-width: 640px;">
    <form action="{{ route('admin.users.update', $user) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="form-group">
            <label class="form-label">Nome Completo</label>
            <input type="text" class="form-control" value="{{ $user->name }}" disabled style="background: var(--bg-muted);">
        </div>

        <div class="form-group">
            <label class="form-label">Endereço de Email</label>
            <input type="email" class="form-control" value="{{ $user->email }}" disabled style="background: var(--bg-muted);">
        </div>

        <div class="form-group">
            <label for="role_id" class="form-label">Papel / Nível de Acesso *</label>
            <select name="role_id" id="role_id" class="form-select" required>
                @foreach($roles as $role)
                    <option value="{{ $role->id }}" {{ old('role_id', $user->role_id) == $role->id ? 'selected' : '' }}>
                        {{ ucfirst($role->name) }} — {{ $role->description }}
                    </option>
                @endforeach
            </select>
            @error('role_id') <div class="form-error">{{ $message }}</div> @enderror
        </div>

        <div class="form-group" style="margin-top: 1.5rem; padding: 1.25rem; background: var(--bg-muted); border-radius: var(--radius-md);">
            <label style="display: flex; align-items: center; gap: 0.75rem; cursor: pointer; font-weight: 600;">
                <input type="checkbox" name="is_active" value="1" {{ old('is_active', $user->is_active) ? 'checked' : '' }} style="width: 18px; height: 18px;">
                <span>Conta Ativa</span>
            </label>
            <p style="font-size: 0.85rem; color: var(--text-muted); margin: 0.5rem 0 0 2rem;">
                Se desmarcar esta opção, o utilizador será impedido de iniciar sessão e as suas sessões ativas serão invalidadas.
            </p>
            @error('is_active') <div class="form-error">{{ $message }}</div> @enderror
        </div>

        <div style="display: flex; gap: 1rem; margin-top: 2rem;">
            <button type="submit" class="btn btn-primary btn-lg">Guardar Permissões</button>
            <a href="{{ route('admin.users.index') }}" class="btn btn-outline btn-lg">Cancelar</a>
        </div>
    </form>
</div>
@endsection
