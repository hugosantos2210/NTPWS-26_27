@extends('layouts.app')

@section('title', 'Sobre o Projeto — PawSafe')

@section('content')
<div class="container" style="padding: 4rem 1.5rem;">
    <div style="max-width: 860px; margin: 0 auto;">
        <span class="badge badge-primary">Sobre a Plataforma</span>
        <h1 style="margin: 0.75rem 0 1.5rem;">Missão PawSafe & Especificação Técnica</h1>
        <p style="font-size: 1.15rem; color: var(--text-muted); margin-bottom: 2.5rem;">
            O <strong>PawSafe</strong> é uma solução completa desenvolvida para a gestão e divulgação de um abrigo animal, integrando uma interface pública intuitiva, gestão de conteúdos (CMS), controlo de utilizadores baseado em papéis (RBAC), uma API REST própria e ligação a serviços web externos.
        </p>

        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 2rem; margin-bottom: 3rem;">
            <div style="background: #fff; padding: 2rem; border-radius: var(--radius-lg); border: 1px solid var(--border); box-shadow: var(--shadow-sm);">
                <div style="font-size: 2rem; margin-bottom: 0.5rem;">🎯</div>
                <h3>Objetivos do Sistema</h3>
                <ul style="margin-top: 1rem; padding-left: 1.25rem; color: var(--text-muted); font-size: 0.95rem; line-height: 1.8;">
                    <li>Centralizar o catálogo de animais resgatados para adoção.</li>
                    <li>Permitir pedidos de adoção online por utilizadores registados.</li>
                    <li>Módulo de Notícias com paginação, pesquisa e categorias dinâmicas.</li>
                    <li>Workflow editorial de notícias (Rascunho, Publicado, Retirado).</li>
                </ul>
            </div>

            <div style="background: #fff; padding: 2rem; border-radius: var(--radius-lg); border: 1px solid var(--border); box-shadow: var(--shadow-sm);">
                <div style="font-size: 2rem; margin-bottom: 0.5rem;">⚡</div>
                <h3>Arquitetura & Tecnologias</h3>
                <ul style="margin-top: 1rem; padding-left: 1.25rem; color: var(--text-muted); font-size: 0.95rem; line-height: 1.8;">
                    <li><strong>Backend:</strong> PHP com Laravel Framework.</li>
                    <li><strong>Base de Dados:</strong> SQL Relacional com migrações e seeders.</li>
                    <li><strong>Segurança:</strong> CSRF protection, hash de passwords (bcrypt), validação rigorosa e sanitização HTML contra XSS.</li>
                    <li><strong>Frontend:</strong> Blade Views responsivas com CSS moderno.</li>
                </ul>
            </div>
        </div>

        <div style="background: #fff; padding: 2rem; border-radius: var(--radius-lg); border: 1px solid var(--border); box-shadow: var(--shadow-sm); margin-bottom: 3rem;">
            <h3>🌐 Integração com Serviços Web (APIs)</h3>
            <p style="margin-top: 0.75rem; color: var(--text-muted);">
                A plataforma cumpre o requisito de troca de dados via serviços web em formato JSON:
            </p>
            <div style="margin-top: 1.5rem; display: flex; flex-direction: column; gap: 1rem;">
                <div style="padding: 1rem; border-radius: var(--radius-md); background: var(--bg-muted); border-left: 4px solid var(--primary);">
                    <strong>1. API REST Própria (PawSafe API v1)</strong>
                    <p style="font-size: 0.9rem; color: var(--text-muted); margin: 0.25rem 0 0.5rem;">
                        Endpoints REST para consulta de notícias com paginação, filtros por categoria, pesquisa e validação por <a href="{{ url('/api/v1/schema') }}" target="_blank">JSON Schema</a>.
                    </p>
                    <a href="{{ url('/api/v1/news') }}" target="_blank" class="btn btn-outline btn-sm">Testar Endpoint GET /api/v1/news ↗</a>
                </div>

                <div style="padding: 1rem; border-radius: var(--radius-md); background: var(--bg-muted); border-left: 4px solid var(--accent);">
                    <strong>2. API Externa (TheDogAPI & TheCatAPI)</strong>
                    <p style="font-size: 0.9rem; color: var(--text-muted); margin: 0.25rem 0 0.5rem;">
                        Ao consultar os detalhes de um cão ou gato, o sistema consome em tempo real dados enciclopédicos sobre a raça (temperamento, esperança de vida, peso e características) com mecanismo de cache local na tabela <code>breed_cache</code> para otimização de performance.
                    </p>
                    <a href="{{ route('pets.index') }}" class="btn btn-outline btn-sm">Ver Demonstração na Ficha de um Animal ➔</a>
                </div>
            </div>
        </div>

        <div style="text-align: center; margin-top: 2rem;">
            <a href="{{ route('pets.index') }}" class="btn btn-primary btn-lg">Explorar Animais para Adoção</a>
        </div>
    </div>
</div>
@endsection
