<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'PawSafe') — Abrigo e Adoção de Animais</title>
    <meta name="description" content="@yield('meta_description', 'Plataforma de proteção, acolhimento e adoção responsável de animais.')">
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
    @stack('styles')
</head>
<body>
    <!-- Top Navigation -->
    <header class="navbar">
        <div class="container nav-container">
            <a href="{{ route('home') }}" class="logo">
                <span class="logo-icon">🐾</span>
                <span>PawSafe</span>
            </a>

            <nav>
                <ul class="nav-links">
                    <li><a href="{{ route('home') }}" class="nav-link {{ request()->routeIs('home') ? 'active' : '' }}">Início</a></li>
                    <li><a href="{{ route('news.index') }}" class="nav-link {{ request()->routeIs('news.*') ? 'active' : '' }}">Notícias</a></li>
                    <li><a href="{{ route('pets.index') }}" class="nav-link {{ request()->routeIs('pets.*') ? 'active' : '' }}">Animais para Adoção</a></li>
                    <li><a href="{{ route('about') }}" class="nav-link {{ request()->routeIs('about') ? 'active' : '' }}">Sobre o Projeto</a></li>
                    <li><a href="{{ url('/api/v1/news') }}" target="_blank" class="nav-link">REST API ⚡</a></li>
                </ul>
            </nav>

            <div class="nav-actions">
                @auth
                    @if(auth()->user()->isAdmin() || auth()->user()->isEditor())
                        <a href="{{ route('admin.dashboard') }}" class="btn btn-outline btn-sm">⚙️ Painel CMS</a>
                    @endif
                    <a href="{{ route('profile') }}" class="btn btn-outline btn-sm">👤 {{ auth()->user()->name }}</a>
                    <form action="{{ route('logout') }}" method="POST" style="display:inline;">
                        @csrf
                        <button type="submit" class="btn btn-outline btn-sm" style="color:var(--danger)!important;">Sair</button>
                    </form>
                @else
                    <a href="{{ route('login') }}" class="btn btn-outline btn-sm">Entrar</a>
                    <a href="{{ route('register') }}" class="btn btn-primary btn-sm">Registar</a>
                @endauth
            </div>
        </div>
    </header>

    <!-- Flash Messages -->
    <main class="main-content">
        @if(session('success'))
            <div class="container" style="margin-top:1.5rem;">
                <div class="alert alert-success">
                    <span>✅ {{ session('success') }}</span>
                </div>
            </div>
        @endif

        @if(session('error'))
            <div class="container" style="margin-top:1.5rem;">
                <div class="alert alert-error">
                    <span>⚠️ {{ session('error') }}</span>
                </div>
            </div>
        @endif

        @yield('content')
    </main>

    <!-- Footer -->
    <footer class="footer">
        <div class="container">
            <div class="footer-grid">
                <div class="footer-col">
                    <div class="logo" style="color:#fff; margin-bottom:1rem;">
                        <span class="logo-icon">🐾</span>
                        <span>PawSafe Shelter</span>
                    </div>
                    <p>Dedicados ao acolhimento digno, reabilitação e adoção de animais resgatados. Ligamos corações humanos a companheiros para toda a vida.</p>
                </div>
                <div class="footer-col">
                    <h4>Navegação</h4>
                    <ul class="footer-links">
                        <li><a href="{{ route('home') }}">Página Inicial</a></li>
                        <li><a href="{{ route('news.index') }}">Notícias & Campanhas</a></li>
                        <li><a href="{{ route('pets.index') }}">Animais Disponíveis</a></li>
                        <li><a href="{{ route('about') }}">Sobre a Plataforma</a></li>
                    </ul>
                </div>
                <div class="footer-col">
                    <h4>APIs & Integrações</h4>
                    <ul class="footer-links">
                        <li><a href="{{ url('/api/v1/news') }}" target="_blank">API Própria (JSON)</a></li>
                        <li><a href="{{ url('/api/v1/schema') }}" target="_blank">JSON Schema</a></li>
                        <li><a href="https://thedogapi.com" target="_blank" rel="noopener">TheDogAPI (Externa)</a></li>
                        <li><a href="https://thecatapi.com" target="_blank" rel="noopener">TheCatAPI (Externa)</a></li>
                    </ul>
                </div>
                <div class="footer-col">
                    <h4>Contacto</h4>
                    <p style="font-size:0.9rem;">📍 Porto, Portugal<br>📞 +351 912 345 678<br>✉️ contacto@pawsafe.org</p>
                </div>
            </div>

            <div class="footer-bottom">
                <p>&copy; {{ date('Y') }} PawSafe — Projeto Académico de Tecnologias e Programação Web. Todos os direitos reservados.</p>
            </div>
        </div>
    </footer>

    @stack('scripts')
</body>
</html>
