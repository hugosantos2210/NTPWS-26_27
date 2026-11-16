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
                    <li><a href="{{ route('home') }}" class="nav-link {{ request()->routeIs('home') ? 'active' : '' }}">{{ __('messages.home') }}</a></li>
                    <li><a href="{{ route('news.index') }}" class="nav-link {{ request()->routeIs('news.*') ? 'active' : '' }}">{{ __('messages.news') }}</a></li>
                    <li><a href="{{ route('pets.index') }}" class="nav-link {{ request()->routeIs('pets.*') ? 'active' : '' }}">{{ __('messages.pets') }}</a></li>
                    <li><a href="{{ route('about') }}" class="nav-link {{ request()->routeIs('about') ? 'active' : '' }}">{{ __('messages.about') }}</a></li>
                    <li><a href="{{ url('/api/v1/news') }}" target="_blank" class="nav-link">{{ __('messages.rest_api') }}</a></li>
                </ul>
            </nav>

            <div class="nav-actions">
                <!-- Language Switcher (PT / EN / HR) -->
                <div class="lang-switcher" style="display:inline-flex; align-items:center; background: rgba(0,0,0,0.05); padding: 0.2rem; border-radius: var(--radius-sm); border: 1px solid var(--border); font-size: 0.8rem; gap: 0.2rem;">
                    @php $currentLocale = app()->getLocale(); @endphp
                    <a href="{{ route('locale.change', 'pt') }}" title="Português" style="display:inline-flex; align-items:center; gap:0.35rem; padding: 0.25rem 0.55rem; border-radius: 4px; text-decoration: none; font-weight: {{ $currentLocale === 'pt' ? '700' : '500' }}; background: {{ $currentLocale === 'pt' ? 'var(--primary)' : 'transparent' }}; color: {{ $currentLocale === 'pt' ? '#fff' : 'inherit' }};">
                        <img src="{{ asset('images/flags/pt.svg') }}" alt="PT" style="width:18px; height:12px; border-radius:2px; box-shadow:0 1px 2px rgba(0,0,0,0.2); object-fit:cover; display:inline-block;">
                        <span>PT</span>
                    </a>
                    <a href="{{ route('locale.change', 'en') }}" title="English" style="display:inline-flex; align-items:center; gap:0.35rem; padding: 0.25rem 0.55rem; border-radius: 4px; text-decoration: none; font-weight: {{ $currentLocale === 'en' ? '700' : '500' }}; background: {{ $currentLocale === 'en' ? 'var(--primary)' : 'transparent' }}; color: {{ $currentLocale === 'en' ? '#fff' : 'inherit' }};">
                        <img src="{{ asset('images/flags/gb.svg') }}" alt="EN" style="width:18px; height:12px; border-radius:2px; box-shadow:0 1px 2px rgba(0,0,0,0.2); object-fit:cover; display:inline-block;">
                        <span>EN</span>
                    </a>
                    <a href="{{ route('locale.change', 'hr') }}" title="Hrvatski" style="display:inline-flex; align-items:center; gap:0.35rem; padding: 0.25rem 0.55rem; border-radius: 4px; text-decoration: none; font-weight: {{ $currentLocale === 'hr' ? '700' : '500' }}; background: {{ $currentLocale === 'hr' ? 'var(--primary)' : 'transparent' }}; color: {{ $currentLocale === 'hr' ? '#fff' : 'inherit' }};">
                        <img src="{{ asset('images/flags/hr.svg') }}" alt="HR" style="width:18px; height:12px; border-radius:2px; box-shadow:0 1px 2px rgba(0,0,0,0.2); object-fit:cover; display:inline-block;">
                        <span>HR</span>
                    </a>
                </div>

                @auth
                    @if(auth()->user()->isAdmin() || auth()->user()->isEditor())
                        <a href="{{ route('admin.dashboard') }}" class="btn btn-outline btn-sm">{{ __('messages.cms_panel') }}</a>
                    @endif
                    <a href="{{ route('profile') }}" class="btn btn-outline btn-sm">👤 {{ auth()->user()->name }}</a>
                    <form action="{{ route('logout') }}" method="POST" style="display:inline;">
                        @csrf
                        <button type="submit" class="btn btn-outline btn-sm" style="color:var(--danger)!important;">{{ __('messages.logout') }}</button>
                    </form>
                @else
                    <a href="{{ route('login') }}" class="btn btn-outline btn-sm">{{ __('messages.login') }}</a>
                    <a href="{{ route('register') }}" class="btn btn-primary btn-sm">{{ __('messages.register') }}</a>
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
                    <p>{{ __('messages.footer_about') }}</p>
                </div>
                <div class="footer-col">
                    <h4>{{ __('messages.footer_nav') }}</h4>
                    <ul class="footer-links">
                        <li><a href="{{ route('home') }}">{{ __('messages.home') }}</a></li>
                        <li><a href="{{ route('news.index') }}">{{ __('messages.news') }}</a></li>
                        <li><a href="{{ route('pets.index') }}">{{ __('messages.pets') }}</a></li>
                        <li><a href="{{ route('about') }}">{{ __('messages.about') }}</a></li>
                    </ul>
                </div>
                <div class="footer-col">
                    <h4>{{ __('messages.footer_apis') }}</h4>
                    <ul class="footer-links">
                        <li><a href="{{ url('/api/v1/news') }}" target="_blank">{{ __('messages.own_api') }}</a></li>
                        <li><a href="{{ url('/api/v1/schema') }}" target="_blank">{{ __('messages.json_schema') }}</a></li>
                        <li><a href="https://thedogapi.com" target="_blank" rel="noopener">TheDogAPI (Externa)</a></li>
                        <li><a href="https://thecatapi.com" target="_blank" rel="noopener">TheCatAPI (Externa)</a></li>
                    </ul>
                </div>
                <div class="footer-col">
                    <h4>{{ __('messages.footer_contact') }}</h4>
                    <p style="font-size:0.9rem;">📍 Porto, Portugal<br>📞 +351 912 345 678<br>✉️ contacto@pawsafe.org</p>
                </div>
            </div>

            <div class="footer-bottom">
                <p>&copy; {{ date('Y') }} PawSafe — {{ __('messages.rights') }}</p>
            </div>
        </div>
    </footer>

    @stack('scripts')
</body>
</html>
