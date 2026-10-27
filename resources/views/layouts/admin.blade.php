<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Painel de Administração') — PawSafe CMS</title>
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
    @stack('styles')
</head>
<body>
    <div class="admin-layout">
        <!-- Sidebar -->
        <aside class="admin-sidebar">
            <div class="admin-sidebar-header">
                <a href="{{ route('admin.dashboard') }}" class="logo" style="color:#fff;">
                    <span class="logo-icon">🐾</span>
                    <span>PawSafe CMS</span>
                </a>
            </div>

            <ul class="admin-nav">
                <li class="admin-nav-item">
                    <a href="{{ route('admin.dashboard') }}" class="admin-nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                        <span>📊</span> Painel Geral
                    </a>
                </li>
                <li class="admin-nav-item">
                    <a href="{{ route('admin.news.index') }}" class="admin-nav-link {{ request()->routeIs('admin.news.*') ? 'active' : '' }}">
                        <span>📰</span> Gestão de Notícias
                    </a>
                </li>
                @if(auth()->user()->isAdmin())
                <li class="admin-nav-item">
                    <a href="{{ route('admin.categories.index') }}" class="admin-nav-link {{ request()->routeIs('admin.categories.*') ? 'active' : '' }}">
                        <span>🏷️</span> Categorias
                    </a>
                </li>
                <li class="admin-nav-item">
                    <a href="{{ route('admin.pets.index') }}" class="admin-nav-link {{ request()->routeIs('admin.pets.*') ? 'active' : '' }}">
                        <span>🐕</span> Animais & Adoções
                    </a>
                </li>
                <li class="admin-nav-item">
                    <a href="{{ route('admin.users.index') }}" class="admin-nav-link {{ request()->routeIs('admin.users.*') ? 'active' : '' }}">
                        <span>👥</span> Utilizadores & Papéis
                    </a>
                </li>
                @endif
                <li class="admin-nav-item" style="margin-top:2rem; border-top:1px solid rgba(255,255,255,0.08); padding-top:1rem;">
                    <a href="{{ route('home') }}" class="admin-nav-link" target="_blank">
                        <span>🌐</span> Ver Site Público ↗
                    </a>
                </li>
            </ul>

            <div style="padding:1.25rem; border-top:1px solid rgba(255,255,255,0.08);">
                <div style="font-size:0.85rem; color:#94a3b8; margin-bottom:0.5rem;">Sessão iniciada como:</div>
                <div style="font-weight:700; color:#fff; font-size:0.95rem;">{{ auth()->user()->name }}</div>
                <div style="margin-top:0.35rem;">
                    <span class="badge badge-primary">{{ auth()->user()->role->name }}</span>
                </div>
                <form action="{{ route('logout') }}" method="POST" style="margin-top:1rem;">
                    @csrf
                    <button type="submit" class="btn btn-outline btn-sm" style="width:100%; color:var(--danger)!important;">Terminar Sessão</button>
                </form>
            </div>
        </aside>

        <!-- Main Content Area -->
        <main class="admin-main">
            @if(session('success'))
                <div class="alert alert-success">
                    <span>✅ {{ session('success') }}</span>
                </div>
            @endif

            @if(session('error'))
                <div class="alert alert-error">
                    <span>⚠️ {{ session('error') }}</span>
                </div>
            @endif

            @yield('content')
        </main>
    </div>

    @stack('scripts')
</body>
</html>
