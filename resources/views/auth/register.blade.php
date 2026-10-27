@extends('layouts.app')

@section('title', 'Registar Conta — PawSafe')

@section('content')
<div class="container" style="padding: 4.5rem 1.5rem;">
    <div style="max-width: 480px; margin: 0 auto; background: #fff; padding: 2.5rem; border-radius: var(--radius-lg); border: 1px solid var(--border); box-shadow: var(--shadow-md);">
        <div style="text-align: center; margin-bottom: 2rem;">
            <div style="font-size: 2.5rem; margin-bottom: 0.5rem;">🐾</div>
            <h2>Criar Conta</h2>
            <p style="color: var(--text-muted); font-size: 0.95rem; margin-top: 0.25rem;">Junte-se à nossa comunidade de proteção animal</p>
        </div>

        <form action="{{ route('register') }}" method="POST">
            @csrf

            <div class="form-group">
                <label for="name" class="form-label">Nome Completo *</label>
                <input type="text" name="name" id="name" value="{{ old('name') }}" class="form-control" placeholder="Seu nome" required autofocus>
                @error('name') <div class="form-error">{{ $message }}</div> @enderror
            </div>

            <div class="form-group">
                <label for="email" class="form-label">Endereço de Email *</label>
                <input type="email" name="email" id="email" value="{{ old('email') }}" class="form-control" placeholder="seu@email.com" required>
                @error('email') <div class="form-error">{{ $message }}</div> @enderror
            </div>

            <div class="form-group">
                <label for="phone" class="form-label">Contacto Telefónico</label>
                <input type="text" name="phone" id="phone" value="{{ old('phone') }}" class="form-control" placeholder="+351 900 000 000">
                @error('phone') <div class="form-error">{{ $message }}</div> @enderror
            </div>

            <div class="form-group">
                <label for="password" class="form-label">Password * (mínimo 8 caracteres)</label>
                <input type="password" name="password" id="password" class="form-control" placeholder="••••••••" required>
                @error('password') <div class="form-error">{{ $message }}</div> @enderror
            </div>

            <div class="form-group">
                <label for="password_confirmation" class="form-label">Confirmar Password *</label>
                <input type="password" name="password_confirmation" id="password_confirmation" class="form-control" placeholder="••••••••" required>
            </div>

            <button type="submit" class="btn btn-primary btn-lg" style="width: 100%; margin-top: 0.5rem;">Registar Conta</button>
        </form>

        <div style="text-align: center; margin-top: 2rem; padding-top: 1.5rem; border-top: 1px solid var(--border); font-size: 0.9rem;">
            <span style="color: var(--text-muted);">Já tem uma conta?</span>
            <a href="{{ route('login') }}" style="font-weight: 700; margin-left: 0.35rem;">Iniciar Sessão</a>
        </div>
    </div>
</div>
@endsection
