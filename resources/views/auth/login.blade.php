@extends('layouts.app')

@section('title', 'Iniciar Sessão — PawSafe')

@section('content')
<div class="container" style="padding: 4.5rem 1.5rem;">
    <div style="max-width: 440px; margin: 0 auto; background: #fff; padding: 2.5rem; border-radius: var(--radius-lg); border: 1px solid var(--border); box-shadow: var(--shadow-md);">
        <div style="text-align: center; margin-bottom: 2rem;">
            <div style="font-size: 2.5rem; margin-bottom: 0.5rem;">🐾</div>
            <h2>Iniciar Sessão</h2>
            <p style="color: var(--text-muted); font-size: 0.95rem; margin-top: 0.25rem;">Bem-vindo(a) de volta ao PawSafe Shelter</p>
        </div>

        <form action="{{ route('login') }}" method="POST">
            @csrf

            <div class="form-group">
                <label for="email" class="form-label">Endereço de Email</label>
                <input type="email" name="email" id="email" value="{{ old('email') }}" class="form-control" placeholder="seu@email.com" required autofocus>
                @error('email') <div class="form-error">{{ $message }}</div> @enderror
            </div>

            <div class="form-group">
                <label for="password" class="form-label">Password</label>
                <input type="password" name="password" id="password" class="form-control" placeholder="••••••••" required>
                @error('password') <div class="form-error">{{ $message }}</div> @enderror
            </div>

            <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 1.5rem; font-size: 0.88rem;">
                <label style="display: flex; align-items: center; gap: 0.5rem; cursor: pointer;">
                    <input type="checkbox" name="remember" id="remember">
                    <span>Lembrar-me</span>
                </label>
            </div>

            <button type="submit" class="btn btn-primary btn-lg" style="width: 100%;">Entrar na Conta</button>
        </form>

        <div style="text-align: center; margin-top: 2rem; padding-top: 1.5rem; border-top: 1px solid var(--border); font-size: 0.9rem;">
            <span style="color: var(--text-muted);">Ainda não tem conta?</span>
            <a href="{{ route('register') }}" style="font-weight: 700; margin-left: 0.35rem;">Criar Conta</a>
        </div>

        <div style="margin-top: 1.5rem; padding: 1rem; background: var(--bg-muted); border-radius: var(--radius-md); font-size: 0.82rem; color: var(--text-muted);">
            <strong>Contas de Demonstração:</strong><br>
            • Admin: <code>admin@pawsafe.com</code> / <code>password123</code><br>
            • Editor: <code>editor@pawsafe.com</code> / <code>password123</code><br>
            • User: <code>user@pawsafe.com</code> / <code>password123</code>
        </div>
    </div>
</div>
@endsection
