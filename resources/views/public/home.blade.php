@extends('layouts.app')

@section('title', 'Início — PawSafe Abrigo e Adoção')

@section('content')
<!-- Hero Section -->
<section class="hero">
    <div class="container">
        <div class="hero-grid">
            <div>
                <span class="hero-tag">✨ Conectamos Vidas, Criamos Famílias</span>
                <h1 class="hero-title">Dê uma segunda oportunidade a quem só sabe <span>amar</span>.</h1>
                <p class="hero-subtitle">
                    O PawSafe é um refúgio e plataforma moderna dedicada ao resgate, cuidado e encaminhamento responsável de cães e gatos para famílias amorosas.
                </p>
                <div class="hero-cta">
                    <a href="{{ route('pets.index') }}" class="btn btn-primary btn-lg">🐾 Encontrar um Amigo</a>
                    <a href="{{ route('news.index') }}" class="btn btn-outline btn-lg">📰 Ler Notícias & Campanhas</a>
                </div>

                <div class="hero-stats">
                    <div class="stat-item">
                        <span class="stat-number">{{ $stats['total_pets'] ?? 0 }}+</span>
                        <span class="stat-label">Animais Resgatados</span>
                    </div>
                    <div class="stat-item">
                        <span class="stat-number">{{ $stats['adopted'] ?? 0 }}</span>
                        <span class="stat-label">Adotados & Felizes</span>
                    </div>
                    <div class="stat-item">
                        <span class="stat-number">{{ $stats['available'] ?? 0 }}</span>
                        <span class="stat-label">À Espera de Lar</span>
                    </div>
                </div>
            </div>

            <div>
                <div style="background:linear-gradient(135deg, rgba(13,148,136,0.1), rgba(99,102,241,0.15)); padding:2rem; border-radius:var(--radius-lg); border:1px solid var(--border); text-align:center;">
                    <div style="font-size:4rem; margin-bottom:1rem;">🐶🐱</div>
                    <h3>Pronto para Adotar?</h3>
                    <p style="color:var(--text-muted); font-size:0.95rem; margin-top:0.5rem;">
                        O processo é transparente e seguro. Escolha o seu animal, leia os detalhes e faça o seu pedido de adoção online.
                    </p>
                    <a href="{{ route('about') }}" class="btn btn-accent" style="margin-top:1rem;">Como Funciona o Processo</a>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Available Pets Section -->
<section style="padding: 4rem 0;">
    <div class="container">
        <div style="display:flex; justify-content:space-between; align-items:flex-end; margin-bottom:2rem;">
            <div>
                <span class="badge badge-primary">Amigos de Quatro Patas</span>
                <h2 style="margin-top:0.5rem;">À Procura de uma Família</h2>
            </div>
            <a href="{{ route('pets.index') }}" class="btn btn-outline">Ver Todos os Animais ➔</a>
        </div>

        <div class="card-grid">
            @forelse($availablePets as $pet)
                <div class="card">
                    <div class="card-img-wrapper">
                        <img src="{{ $pet->image_url }}" alt="{{ $pet->name }}" class="card-img">
                        <div class="card-badge">
                            <span class="badge badge-success">{{ ucfirst($pet->species) }}</span>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="card-meta">
                            <span>Porte: <strong>{{ ucfirst($pet->size) }}</strong></span>
                            <span>•</span>
                            <span>{{ $pet->age_years }} anos</span>
                            <span>•</span>
                            <span>{{ $pet->gender == 'male' ? 'Macho' : 'Fêmea' }}</span>
                        </div>
                        <h3 class="card-title"><a href="{{ route('pets.show', $pet) }}">{{ $pet->name }}</a></h3>
                        <p class="card-text">{{ Str::limit($pet->description, 100) }}</p>
                    </div>
                    <div class="card-footer">
                        <span style="font-size:0.85rem; font-weight:600; color:var(--primary);">{{ $pet->breed }}</span>
                        <a href="{{ route('pets.show', $pet) }}" class="btn btn-primary btn-sm">Conhecer</a>
                    </div>
                </div>
            @empty
                <p>Nenhum animal disponível no momento.</p>
            @endforelse
        </div>
    </div>
</section>

<!-- Latest News Section -->
<section style="padding: 3rem 0 5rem; background:var(--bg-muted);">
    <div class="container">
        <div style="display:flex; justify-content:space-between; align-items:flex-end; margin-bottom:2rem;">
            <div>
                <span class="badge badge-accent">Novidades & Campanhas</span>
                <h2 style="margin-top:0.5rem;">Últimas Notícias do Abrigo</h2>
            </div>
            <a href="{{ route('news.index') }}" class="btn btn-outline">Ver Todas as Notícias ➔</a>
        </div>

        <div class="card-grid">
            @forelse($latestNews as $article)
                <div class="card">
                    <div class="card-img-wrapper" style="height:180px;">
                        <img src="{{ $article->image_url }}" alt="{{ $article->title }}" class="card-img">
                        @if($article->category)
                            <div class="card-badge">
                                <span class="badge badge-primary">{{ $article->category->name }}</span>
                            </div>
                        @endif
                    </div>
                    <div class="card-body">
                        <div class="card-meta">
                            <span>📅 {{ $article->published_at ? $article->published_at->format('d/m/Y') : 'Recente' }}</span>
                            <span>•</span>
                            <span>✍️ {{ $article->author->name ?? 'Equipa' }}</span>
                        </div>
                        <h3 class="card-title"><a href="{{ route('news.show', $article->slug) }}">{{ $article->title }}</a></h3>
                        <p class="card-text">{{ Str::limit($article->summary, 110) }}</p>
                    </div>
                    <div class="card-footer">
                        <a href="{{ route('news.show', $article->slug) }}" style="font-weight:600; font-size:0.9rem;">Ler artigo completo ➔</a>
                    </div>
                </div>
            @empty
                <p>Nenhuma notícia publicada ainda.</p>
            @endforelse
        </div>
    </div>
</section>
@endsection
