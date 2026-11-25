# 🐾 PawSafe — Documentação Exaustiva da Arquitetura e Código-Fonte

Este documento é a referência técnica integral e detalhada da aplicação **PawSafe**, uma plataforma web desenvolvida na framework **Laravel** concebida para a gestão moderna de abrigos de animais, facilitação de processos de adoção responsável, publicação e difusão de notícias/campanhas de sensibilização, disponibilização de uma API RESTful própria e integração com múltiplos serviços e APIs externas.

---

## 📑 Índice Geral

1. [Visão Geral & Stack Tecnológica](#1-visão-geral--stack-tecnológica)
2. [Estrutura de Diretórios e Mapeamento do Código](#2-estrutura-de-diretórios-e-mapeamento-do-código)
3. [Ciclo de Vida da Requisição HTTP & Mapeamento Completo de Rotas](#3-ciclo-de-vida-da-requisição-http--mapeamento-completo-de-rotas)
4. [Base de Dados, Esquema Relacional e Modelos Eloquent](#4-base-de-dados-esquema-relacional-e-modelos-eloquent)
5. [Camada de Serviços (Service Layer) & APIs Externas](#5-camada-de-serviços-service-layer--apis-externas)
6. [Autenticação, 2FA (TOTP) e Controlo de Acesso Baseado em Papéis (RBAC)](#6-autenticação-2fa-totp-e-controlo-de-acesso-baseado-em-papéis-rbac)
7. [Controladores e Lógica de Negócio (Controllers Breakdown)](#7-controladores-e-lógica-de-negócio-controllers-breakdown)
8. [API RESTful Própria (v1) & JSON Schema](#8-api-restful-própria-v1--json-schema)
9. [Camada de Apresentação (Blade, Quill.js, Vite & Multi-Idioma)](#9-camada-de-apresentação-blade-quilljs-vite--multi-idioma)
10. [Segurança em Profundidade (Defense in Depth)](#10-segurança-em-profundidade-defense-in-depth)
11. [Testes Automatizados & Qualidade de Software](#11-testes-automatizados--qualidade-de-software)
12. [Automação, Vídeo Demonstrativo & Capturas de Ecrã (Playwright)](#12-automação-vídeo-demonstrativo--capturas-de-ecrã-playwright)
13. [Guia de Instalação, Configuração e Execução](#13-guia-de-instalação-configuração-e-execução)
14. [Perguntas e Respostas para Defesa e Avaliação do Projeto](#14-perguntas-e-respostas-para-defesa-e-avaliação-do-projeto)

---

## 1. Visão Geral & Stack Tecnológica

O **PawSafe** resolve o desafio de gestão operacional de um abrigo de animais de companhia em conjunto com um portal público de sensibilização e adoção:

- **Portal Público Multilingue**: Catálogo interativo de animais para adoção com filtros dinâmicos (espécie, porte, género, pesquisa textual), fichas de animais enriquecidas com biografia de raças obtidas de APIs externas, submissão controlada de candidaturas de adoção, módulo de notícias com categorias e paginação, widget dinâmico assíncrono que consome a API própria da aplicação e páginas informativas institucionais.
- **Painel Administrativo CMS (`/admin`)**: Dashboard com indicadores em tempo real (KPIs de animais acolhidos, adotados, pedidos pendentes, artigos publicados e rascunhos), gestão completa de animais com uploads de fotos, fluxo de revisão e decisão de pedidos de adoção em transação atómica, gestão editorial de artigos com editor rich-text (Quill.js v2) e gestão de utilizadores com ativação/desativação de contas e atribuição de papéis.
- **API RESTful Versionada (`/api/v1`)**: Conjunto de endpoints normalizados com suporte a paginação, filtros, respostas semânticas (200, 201, 204, 400, 401, 403, 404), validação formal contra JSON Schema (draft 2020-12) e autorização por cargo.
- **Autenticação Avançada com 2FA (RFC 6238 TOTP)**: Sistema próprio em PHP puro de verificação em dois passos compatível com Google Authenticator, Authy e Microsoft Authenticator, com suporte a códigos de recuperação de emergência de uso único e confirmação obrigatória de ativação.

### Stack Tecnológica e Bibliotecas

| Componente | Tecnologia | Finalidade / Justificação |
|---|---|---|
| **Linguagem Backend** | PHP ^8.3 (execução 8.4) | Tipagem estrita, constructor property promotion, match expressions e performance. |
| **Framework Web** | Laravel ^13.17 | Arquitetura MVC limpa, Eloquent ORM, sistema de migrations, middleware pipeline e rotas declarativas. |
| **Base de Dados** | SQLite (dev) / MySQL / PostgreSQL | Suporte nativo a migrações relacionais com chaves estrangeiras e integridade referencial. |
| **Frontend / Templates** | Blade + Tailwind CSS ^4.0 + Vite ^8.0 | Renderização server-side rápida, compilação de assets otimizada, herança de layouts e proteção XSS. |
| **Editor Rich-Text** | Quill.js v2 (CDN) | Edição WYSIWYG moderna no browser sem necessidade de chaves de API pagas ou registo externo. |
| **Internacionalização** | Laravel Localization (`lang/`) | Suporte integral a 3 idiomas com **334 chaves** cada: Português (`pt`), Inglês (`en`) e Croata (`hr`), no portal público e painel Admin. |
| **APIs Externas** | TheDogAPI & TheCatAPI | Obtenção de fichas biográficas de raças (temperamento, peso, longevidade) e imagens de animais. |
| **API Externa de Imagem**| Pexels API & Unsplash CDN | Seleção de fotografias profissionais temáticas e contextuais para artigos de notícias. |
| **Autenticação 2FA** | RFC 6238 / RFC 4226 (TOTP) | Implementação interna de HMAC-SHA1 de 30 segundos sem dependências externas instáveis. |
| **Suíte de Testes** | PHPUnit ^12.5 / Laravel Testing | **39 testes** (Feature & Unit) cobrindo autenticação, autorização, API REST, 2FA e internacionalização. |
| **Automação & Média** | Playwright + FFmpeg | Scripts automatizados para geração de vídeo walkthrough (`pawsafe-demo.webm`) e 6 capturas de ecrã em inglês. |

---

## 2. Estrutura de Diretórios e Mapeamento do Código

A organização do código segue rigorosamente o padrão MVC com a introdução de uma **Camada de Serviços (Services)** para isolamento de lógica externa:

```
NTPWS-26_27/
├── app/
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── Admin/                      # Controladores da área administrativa
│   │   │   │   ├── AdminCategoryController.php  # CRUD de categorias com proteção de eliminação
│   │   │   │   ├── AdminNewsController.php      # Gestão editorial de notícias com Quill.js e sanitização
│   │   │   │   ├── AdminPetController.php       # Gestão de animais e decisões de adoção atómicas
│   │   │   │   ├── AdminUserController.php      # Gestão de papéis e desativação com expurgo de sessões
│   │   │   │   └── DashboardController.php      # Métricas e KPIs do abrigo em tempo real
│   │   │   ├── Api/
│   │   │   │   └── ApiNewsController.php        # Endpoints RESTful v1 (CRUD, filtros, paginação)
│   │   │   ├── Auth/
│   │   │   │   ├── AuthController.php           # Registo, login seguro e logout com regeneração de sessão
│   │   │   │   └── TwoFactorController.php      # Setup de 2FA, verificação de desafio e códigos de recuperação
│   │   │   ├── HomeController.php               # Página inicial, sobre e comutador de idioma (/locale)
│   │   │   ├── NewsController.php               # Listagem pública e visualização de artigos
│   │   │   ├── PetController.php                # Catálogo público, ficha de raça e submissão de adoção
│   │   │   └── ProfileController.php            # Perfil do utilizador, alteração de senha e avatar
│   │   └── Middleware/
│   │       ├── CheckActiveUser.php              # Desconecta utilizadores inativos em tempo de execução
│   │       ├── CheckRole.php                    # Validação de RBAC variádica (role:admin,editor)
│   │       └── SetLocale.php                    # Injeção global do idioma a partir da sessão HTTP
│   ├── Models/                                  # Entidades Eloquent e relações
│   │   ├── AdoptionRequest.php                  # Pedido de adoção com restrição única de candidatura
│   │   ├── BreedCache.php                       # Cache relacional das respostas da API de raças
│   │   ├── Category.php                         # Categoria com geração automática de slugs
│   │   ├── News.php                             # Artigo com estados editoriais e fallbacks de imagem
│   │   ├── Pet.php                              # Animal com biometria, fotos e estados de adoção
│   │   ├── Role.php                             # Papéis do sistema (admin, editor, user)
│   │   └── User.php                             # Utilizador com 2FA, avatar e métodos de autorização
│   └── Services/                                # Camada de Serviços (Lógica desacoplada)
│       ├── BreedService.php                     # Integração TheDogAPI / TheCatAPI com cache-first
│       ├── NewsImageService.php                 # Obtenção contextual de fotos via Pexels / Unsplash
│       ├── PetImageService.php                  # Resolução de imagens de animais com base na raça
│       └── TotpService.php                      # Algoritmo RFC 6238 TOTP puro em PHP (Base32, HMAC-SHA1)
├── bootstrap/
│   └── app.php                                  # Configuração de rotas, middlewares globais e exceções JSON
├── config/
│   └── services.php                             # Credenciais de APIs de terceiros (Dog, Cat, Pexels)
├── database/
│   ├── migrations/                              # 9 migrações estruturadas com integridade referencial
│   └── seeders/
│       └── DatabaseSeeder.php                   # Dados completos de teste (contas, notícias, animais)
├── lang/
│   ├── pt/messages.php                          # Dicionário de tradução em Português (334 chaves)
│   ├── en/messages.php                          # Dicionário de tradução em Inglês (334 chaves)
│   └── hr/messages.php                          # Dicionário de tradução em Croata (334 chaves)
├── public/
│   ├── css/style.css                            # Folhas de estilo da aplicação
│   ├── demo.html                                # Player interativo com capítulos do vídeo de demonstração
│   ├── images/flags/                            # Bandeiras vetoriais SVG (pt.svg, gb.svg, hr.svg)
│   ├── schemas/
│   │   └── news-schema.json                     # JSON Schema oficial para validação de notícias
│   ├── screenshots/                             # 6 capturas de ecrã em alta resolução (versão pública)
│   └── videos/
│       └── pawsafe-demo.webm                    # Vídeo HD walkthrough de todas as páginas e funcionalidades
├── resources/
│   └── views/                                   # Vistas Blade estruturadas por módulos (public, auth, admin)
├── routes/
│   ├── api.php                                  # Rotas da API REST (/api/v1)
│   └── web.php                                  # Rotas Web (públicas, autenticadas e admin)
├── screenshots/                                 # Cópia direta das 6 capturas de ecrã em inglês
├── scripts/
│   ├── capture_screenshots.cjs                  # Script Playwright para captura automatizada de ecrãs
│   └── record_demo.cjs                          # Script Playwright para gravação do vídeo com HUD
└── tests/
    ├── Feature/                                 # 38 testes de funcionalidade (Auth, API, 2FA, Pages)
    └── Unit/                                    # Testes unitários base
```

---

## 3. Ciclo de Vida da Requisição HTTP & Mapeamento Completo de Rotas

### 3.1. Pipeline de Execução (`bootstrap/app.php`)

Todas as requisições entram por `public/index.php` e são encaminhadas pelo kernel configurado em [bootstrap/app.php](file:///d:/erasmus/ntp/NTPWS-26_27/bootstrap/app.php):

1. **Registo de Rotas**: Carrega `routes/web.php` (grupo `web`), `routes/api.php` (prefixo `/api`, grupo `api`) e o endpoint de healthcheck `/up`.
2. **Aliases de Middleware**:
   - `'role' => CheckRole::class` — Controlo RBAC com parâmetros variádicos (`role:admin,editor`).
   - `'active' => CheckActiveUser::class` — Validação de estado ativo da conta.
3. **Middlewares Globais do Grupo `web`**:
   - `CheckActiveUser` e `SetLocale` são anexados (`append`) a todas as rotas web, garantindo que qualquer página pública ou administrativa respeita o idioma da sessão e desconecta contas desativadas em tempo real.
4. **Tratamento Unificado de Exceções API**:
   - `$exceptions->shouldRenderJsonWhen(fn (Request $request) => $request->is('api/*') || $request->expectsJson())` — assegura que erros de validação, 404 ou 401/403 sob `/api/*` devolvem sempre JSON limpo em vez de páginas HTML de erro.

```mermaid
sequenceDiagram
    actor U as Utilizador / Cliente
    participant B as bootstrap/app.php
    participant MW as Middlewares (CheckActiveUser, SetLocale, CheckRole)
    participant C as Controller
    participant S as Service / Eloquent Model
    participant DB as SQLite / MySQL / API Externa

    U->>B: HTTP Request (Web ou /api/v1/*)
    B->>MW: Executa pipeline de middlewares
    alt Conta desativada
        MW-->>U: Logout imediato + Redirect /login
    else Papel insuficiente
        MW-->>U: 403 Forbidden (HTML ou JSON)
    else Autorizado
        MW->>C: Encaminha para Ação do Controlador
        C->>S: Validação + Lógica de Negócio
        S->>DB: Query Eloquent / Transação / Cache + Http::get()
        DB-->>S: Dados / Resultado
        S-->>C: DTO / Modelos hidratados
        C-->>U: View Blade (HTML escapado) ou JsonResponse
    end
```

### 3.2. Mapeamento Integral de Rotas Web (`routes/web.php`)

#### Rotas Públicas (Sem Autenticação)
| Método | URI | Nome da Rota | Controlador / Ação | Finalidade |
|---|---|---|---|---|
| `GET` | `/` | `home` | `HomeController@index` | Página inicial com KPIs, 3 últimas notícias e 6 animais disponíveis. |
| `GET` | `/about` | `about` | `HomeController@about` | Página institucional sobre a missão, história e equipa do abrigo. |
| `GET` | `/locale/{locale}` | `locale.change` | `HomeController@changeLocale` | Altera o idioma na sessão (`pt`, `en`, `hr`) com `fallback: route('home')`. |
| `GET` | `/news` | `news.index` | `NewsController@index` | Listagem paginada de notícias publicadas, filtros por categoria e pesquisa. |
| `GET` | `/news/{slug}` | `news.show` | `NewsController@show` | Leitura integral de artigo publicado + artigos relacionados. |
| `GET` | `/pets` | `pets.index` | `PetController@index` | Catálogo de animais para adoção com filtros por espécie, porte e género. |
| `GET` | `/pets/{pet}` | `pets.show` | `PetController@show` | Ficha detalhada do animal + dados de raça via `BreedService` (TheDogAPI/TheCatAPI). |

#### Rotas de Autenticação & 2FA (`guest` e `auth`)
| Método | URI | Middleware | Nome da Rota | Controlador / Ação |
|---|---|---|---|---|
| `GET` / `POST` | `/register` | `guest` | `register` | `AuthController@showRegister` / `register` (força sempre `Role::USER`). |
| `GET` / `POST` | `/login` | `guest` | `login` | `AuthController@showLogin` / `login` (interceta para 2FA se ativo). |
| `GET` / `POST` | `/two-factor-challenge` | `guest` | `two-factor.challenge` | `TwoFactorController@showChallenge` / `verifyChallenge` (TOTP ou recovery code). |
| `POST` | `/logout` | `auth` | `logout` | `AuthController@logout` (invalida sessão e regenera CSRF token). |
| `GET` | `/profile` | `auth` | `profile` | `ProfileController@show` (dados, avatar, 2FA status e histórico de adoções). |
| `PUT` | `/profile` | `auth` | `profile.update` | `ProfileController@update` (edita dados pessoais e upload de avatar). |
| `PUT` | `/profile/password` | `auth` | `profile.password` | `ProfileController@changePassword` (verifica senha atual antes de alterar). |
| `GET` | `/two-factor-setup` | `auth` | `two-factor.setup` | `TwoFactorController@showSetup` (gera segredo Base32, QR Code e 8 códigos). |
| `POST` | `/two-factor/confirm` | `auth` | `two-factor.confirm` | `TwoFactorController@confirm` (valida o primeiro código TOTP e ativa 2FA). |
| `POST` | `/two-factor/disable` | `auth` | `two-factor.disable` | `TwoFactorController@disable` (exige palavra-passe atual). |
| `POST` | `/two-factor/recovery-codes` | `auth` | `two-factor.recovery-codes` | `TwoFactorController@regenerateRecoveryCodes` (gera novos 8 códigos). |
| `POST` | `/pets/{pet}/adopt` | `auth` | `pets.adopt` | `PetController@submitAdoption` (submete candidatura única de adoção). |

#### Rotas Administrativas (`/admin` — Middleware `auth`, `role:admin,editor`)
| Prefixo / URI | Middleware Extra | Nome Base | Controlador | Permissões |
|---|---|---|---|---|
| `GET /admin` | `role:admin,editor` | `admin.dashboard` | `DashboardController@index` | Editores veem vista limitada; Admins veem KPIs globais e adoções. |
| `Resource /admin/news` | `role:admin,editor` | `admin.news.*` | `AdminNewsController` | Editores gerem apenas as **suas** notícias; Admins gerem todas. |
| `Resource /admin/categories` | `role:admin` | `admin.categories.*` | `AdminCategoryController` | Exclusivo Admin; impede apagar categorias com notícias. |
| `Resource /admin/pets` | `role:admin` | `admin.pets.*` | `AdminPetController` | Exclusivo Admin; CRUD completo de animais e fotos. |
| `PATCH /admin/adoption-requests/{adoptionRequest}` | `role:admin` | `admin.adoption-requests.update` | `AdminPetController@updateAdoptionRequest` | Exclusivo Admin; aprova/rejeita pedidos em `DB::transaction()`. |
| `GET /admin/users`, `edit`, `update` | `role:admin` | `admin.users.*` | `AdminUserController` | Exclusivo Admin; altera `role_id` e `is_active` com expurgo de sessões. |

---

## 4. Base de Dados, Esquema Relacional e Modelos Eloquent

### 4.1. Esquema Relacional e Diagrama ER

A base de dados é estritamente relacional, cumprindo os padrões de integridade referencial e normalização (3FN).

```mermaid
erDiagram
    ROLES ||--o{ USERS : "atribui cargo a"
    USERS ||--o{ NEWS : "é autor de"
    USERS ||--o{ ADOPTION_REQUESTS : "submete"
    CATEGORIES ||--o{ NEWS : "categoriza"
    PETS ||--o{ ADOPTION_REQUESTS : "é alvo de"
    BREED_CACHE ||--|| PETS : "enriquece dados de raça"

    ROLES {
        bigint id PK
        string name UK "admin, editor, user"
        string description
        timestamps created_at_updated_at
    }

    USERS {
        bigint id PK
        string name
        string email UK
        string password "hashed bcrypt"
        string phone
        string avatar_path
        bigint role_id FK
        boolean is_active "default true"
        text two_factor_secret "Base32"
        text two_factor_recovery_codes "JSON array"
        timestamp two_factor_confirmed_at
        timestamps created_at_updated_at
    }

    CATEGORIES {
        bigint id PK
        string name UK
        string slug UK
        text description
        timestamps created_at_updated_at
    }

    NEWS {
        bigint id PK
        string title
        string slug UK
        text summary
        longtext content "HTML sanitizado"
        bigint category_id FK "restrict on delete"
        bigint author_id FK "cascade on delete"
        string image_path
        enum status "draft, published, withdrawn"
        timestamp published_at
        timestamps created_at_updated_at
    }

    PETS {
        bigint id PK
        string name
        enum species "dog, cat, other"
        string breed
        tinyint age_years
        tinyint age_months
        enum gender "male, female, unknown"
        enum size "small, medium, large, extra_large"
        text description
        string image_path
        enum status "available, adopted, pending"
        timestamps created_at_updated_at
    }

    ADOPTION_REQUESTS {
        bigint id PK
        bigint pet_id FK "cascade on delete"
        bigint user_id FK "cascade on delete"
        text message
        enum status "pending, approved, rejected"
        text admin_notes
        timestamps created_at_updated_at
    }

    BREED_CACHE {
        bigint id PK
        string species "dog, cat"
        string breed_name
        json data_json "características da API"
        string image_url
        timestamp fetched_at
        timestamps created_at_updated_at
    }
```

### 4.2. Análise Detalhada das Migrações (`database/migrations/`)

1. [0001_01_01_000000_create_users_table.php](file:///d:/erasmus/ntp/NTPWS-26_27/database/migrations/0001_01_01_000000_create_users_table.php):
   - Cria a tabela `roles` com índice de unicidade em `name`.
   - Cria a tabela `users` com restrição de chave estrangeira `role_id` referenciando `roles(id)` com regra `restrictOnDelete()`. Isto impede que um papel seja eliminado se existirem utilizadores associados a ele.
   - Cria a tabela `sessions` que armazena `user_id`, `ip_address`, `user_agent`, `payload` e `last_activity`. Essencial para controlo de sessões ativas e invalidação remota.
2. [2024_01_02_000001_create_categories_table.php](file:///d:/erasmus/ntp/NTPWS-26_27/database/migrations/2024_01_02_000001_create_categories_table.php):
   - Define colunas `name` e `slug`, ambas com restrição única (`unique()`).
3. [2024_01_02_000002_create_news_table.php](file:///d:/erasmus/ntp/NTPWS-26_27/database/migrations/2024_01_02_000002_create_news_table.php):
   - `category_id`: Chave estrangeira para `categories` com `restrictOnDelete()`. Se uma categoria possuir notícias, a base de dados recusa a eliminação da categoria, satisfazendo a exigência do enunciado.
   - `author_id`: Chave estrangeira para `users` com `cascadeOnDelete()`.
   - `status`: Campo enumerado (`draft`, `published`, `withdrawn`) com índice dedicado para pesquisas ultra-rápidas.
   - `published_at`: Timestamp com índice para ordenação cronológica decrescente.
4. [2024_01_02_000003_create_pets_table.php](file:///d:/erasmus/ntp/NTPWS-26_27/database/migrations/2024_01_02_000003_create_pets_table.php):
   - Atributos tipados para animais: `species`, `breed`, `age_years`, `age_months`, `gender`, `size` e `status` (`available`, `adopted`, `pending`), com índices em `species` e `status`.
5. [2024_01_02_000004_create_adoption_requests_table.php](file:///d:/erasmus/ntp/NTPWS-26_27/database/migrations/2024_01_02_000004_create_adoption_requests_table.php):
   - **Restrição de Unicidade Composta**: `$table->unique(['pet_id', 'user_id'])`. Impede fisicamente na base de dados que o mesmo utilizador submeta múltiplos pedidos para o mesmo animal.
   - Ambas as chaves estrangeiras (`pet_id` e `user_id`) possuem `cascadeOnDelete()`.
6. [2024_01_02_000005_create_breed_cache_table.php](file:///d:/erasmus/ntp/NTPWS-26_27/database/migrations/2024_01_02_000005_create_breed_cache_table.php):
   - Armazenamento em cache de chamadas a APIs externas. Possui restrição de unicidade em `['species', 'breed_name']` e armazena o corpo estruturado no campo nativo `data_json` (tipo `json`).
7. [2024_01_02_000006_add_two_factor_columns_to_users_table.php](file:///d:/erasmus/ntp/NTPWS-26_27/database/migrations/2024_01_02_000006_add_two_factor_columns_to_users_table.php):
   - Adiciona colunas para 2FA: `two_factor_secret`, `two_factor_recovery_codes` (array em JSON) e `two_factor_confirmed_at`.

### 4.3. Análise Exaustiva dos Modelos Eloquent (`app/Models/`)

#### 1. `User` ([app/Models/User.php](file:///d:/erasmus/ntp/NTPWS-26_27/app/Models/User.php))
- **Herança**: `Illuminate\Foundation\Auth\User as Authenticatable`.
- **Casts**:
  ```php
  'email_verified_at' => 'datetime',
  'password' => 'hashed', // Hashing automático com bcrypt ao definir a password
  'is_active' => 'boolean',
  'two_factor_recovery_codes' => 'array', // Deserialização automática de JSON para array PHP
  'two_factor_confirmed_at' => 'datetime',
  ```
- **Campos Ocultos (`$hidden`)**: `password`, `remember_token`, `two_factor_secret`, `two_factor_recovery_codes`. Garante que segredos nunca sejam expostos em transformações JSON ou respostas de API.
- **Relações**:
  - `role()`: `BelongsTo(Role::class)`
  - `news()`: `HasMany(News::class, 'author_id')`
  - `adoptionRequests()`: `HasMany(AdoptionRequest::class)`
- **Métodos Auxiliares de Autorização**:
  - `isAdmin()`: Avalia se `role->name === Role::ADMIN`.
  - `isEditor()`: Avalia se `role->name === Role::EDITOR`.
  - `isEditorOrAbove()`: Retorna `true` se for Admin ou Editor.
  - `isActive()`: Retorna o booleano `is_active`.
  - `hasTwoFactorEnabled()`: Avalia se `two_factor_secret` e `two_factor_confirmed_at` não são nulos.

#### 2. `Role` ([app/Models/Role.php](file:///d:/erasmus/ntp/NTPWS-26_27/app/Models/Role.php))
- **Constantes de Papel**:
  ```php
  public const ADMIN = 'admin';
  public const EDITOR = 'editor';
  public const USER = 'user';
  ```
- **Relação**: `users()`: `HasMany(User::class)`.
- **Métodos**: `isAdmin()`, `isEditor()`.

#### 3. `Category` ([app/Models/Category.php](file:///d:/erasmus/ntp/NTPWS-26_27/app/Models/Category.php))
- **Geração Automática de Slugs**: Utiliza o hook de ciclo de vida `boot()` do Eloquent. Ao criar ou atualizar, caso o slug esteja vazio ou o nome tenha sido alterado, executa `Str::slug($category->name)` gerando URLs amigáveis (ex: *"Histórias de Sucesso"* vira `historias-de-sucesso`).
- **Relação**: `news()`: `HasMany(News::class)`.
- **Regra de Negócio**: Método `hasNews()` para verificar se há artigos associados antes de autorizar a sua remoção.

#### 4. `News` ([app/Models/News.php](file:///d:/erasmus/ntp/NTPWS-26_27/app/Models/News.php))
- **Ciclo de Vida Editorial**: Estados `draft`, `published` e `withdrawn`.
- **Scopes Locais Eloquent**:
  - `scopePublished($query)`: Filtra por `status = 'published'`.
  - `scopeDraft($query)`: Filtra por `status = 'draft'`.
  - `scopeWithdrawn($query)`: Filtra por `status = 'withdrawn'`.
- **Accessor Inteligente de Imagem (`getImageUrlAttribute`)**:
  - Se o utilizador fez upload de uma imagem local, devolve o caminho público através de `Storage::url($this->image_path)`.
  - Se a imagem guardada for um URL direto (http/https), devolve o próprio URL.
  - Se não houver imagem manual, aciona dinamicamente o `NewsImageService` para ir buscar uma foto contextual à API da Pexels ou ao repositório Unsplash curado.
- **Relações**: `category()` (`BelongsTo`), `author()` (`BelongsTo` referenciando `User` na coluna `author_id`).
- **Métodos de Ação**: `publish()` e `withdraw()`.

#### 5. `Pet` ([app/Models/Pet.php](file:///d:/erasmus/ntp/NTPWS-26_27/app/Models/Pet.php))
- **Accessors Visuais**:
  - `getImageUrlAttribute()`: Resolução de imagem de alta qualidade com fallback para o `PetImageService`.
  - `getFormattedAgeAttribute()`: Converte anos e meses em descrições humanas (ex: *"2 anos e 3 meses"*).
  - `getSpeciesIconAttribute()`: Devolve o ícone emoji correspondente (`🐕` cão, `🐈` gato, `🐾` outro).
- **Relações**: `adoptionRequests()` (`HasMany`).
- **Scopes**: `scopeAvailable($query)` filtrando por `status = 'available'`.

#### 6. `AdoptionRequest` ([app/Models/AdoptionRequest.php](file:///d:/erasmus/ntp/NTPWS-26_27/app/Models/AdoptionRequest.php))
- **Máquina de Estados**: `pending`, `approved`, `rejected`.
- **Relações**: `pet()` (`BelongsTo`), `user()` (`BelongsTo`).
- **Métodos**: `isPending()`, `isApproved()`.

#### 7. `BreedCache` ([app/Models/BreedCache.php](file:///d:/erasmus/ntp/NTPWS-26_27/app/Models/BreedCache.php))
- **Casts**: `data_json => 'array'`, `fetched_at => 'datetime'`.
- **Método `isStale()`**: Compara a data de recolha com o limiar de 24 horas:
  ```php
  public function isStale(): bool
  {
      return $this->fetched_at->diffInHours(now()) >= 24;
  }
  ```

---

## 5. Camada de Serviços (Service Layer) & APIs Externas

Para respeitar o princípio da responsabilidade única (SRP) e manter os controladores limpos, todas as operações complexas e chamadas de rede externas foram abstraídas em serviços dentro de `app/Services/`.

### 5.1. `TotpService` ([app/Services/TotpService.php](file:///d:/erasmus/ntp/NTPWS-26_27/app/Services/TotpService.php))

Implementação pura em PHP dos padrões **RFC 4226 (HOTP)** e **RFC 6238 (TOTP)**, sem recurso a bibliotecas externas pesadas ou instáveis:

1. **Geração de Chave Secreta Base32**:
   - Gera um segredo aleatório de 16 caracteres a partir do alfabeto padrão Base32 (`ABCDEFGHIJKLMNOPQRSTUVWXYZ234567`) usando o gerador criptograficamente seguro `random_int()`.
2. **Cálculo de Código Temporário (`getCode`)**:
   - Divide o timestamp Unix atual em fatias temporais de 30 segundos: `$timeSlice = (int) floor($timestamp / 30)`.
   - Converte o segredo Base32 em binário (`base32Decode`).
   - Empacota o `$timeSlice` num formato binário big-endian de 8 bytes usando `pack('N*', 0, $timeSlice)`.
   - Calcula o hash criptográfico `hash_hmac('sha1', $timeData, $secretKey, true)`.
   - Aplica a **Dynamic Truncation** (RFC 4226): extrai o offset dos últimos 4 bits do hash e gera um inteiro de 31 bits mascarado.
   - Aplica o operador módulo `$binary % 1000000` e formata com zeros à esquerda (`str_pad`), obtendo o código de 6 dígitos.
3. **Verificação com Tolerância a Desvio Temporal (`verify`)**:
   - Aceita uma janela de tolerância de `$window = 1` (verifica o período atual, 30 segundos antes e 30 segundos depois, cobrindo um intervalo de 90 segundos para absorver relógios ligeiramente dessincronizados).
   - Utiliza `hash_equals()` para comparação em tempo constante, prevenindo **ataques de temporização (timing attacks)**.
4. **Códigos de Recuperação de Emergência (`generateRecoveryCodes`)**:
   - Cria 8 códigos em formato legível `XXXX-XXXX` utilizando `bin2hex(random_bytes(2))`.
5. **Integração de QR Code**:
   - Gera a URI padrão `otpauth://totp/PawSafe:email?secret=...&issuer=PawSafe&algorithm=SHA1&digits=6&period=30`.
   - Gera o URL de imagem QR Code através da API pública segura `api.qrserver.com`.

### 5.2. Configuração Centralizada de APIs Externas (`config/services.php`)

Todas as credenciais e URLs base das APIs de terceiros são geridas exclusivamente no servidor através de [config/services.php](file:///d:/erasmus/ntp/NTPWS-26_27/config/services.php), lendo variáveis de ambiente do `.env` (nunca expostas no código cliente JavaScript ou no repositório Git):

| Serviço | Variável `.env` | URL Base | Cabeçalho de Autenticação | Utilizado por |
|---|---|---|---|---|
| **TheDogAPI** | `DOG_API_KEY` | `https://api.thedogapi.com/v1` | `x-api-key` | `BreedService`, `PetImageService` |
| **TheCatAPI** | `CAT_API_KEY` | `https://api.thecatapi.com/v1` | `x-api-key` | `BreedService`, `PetImageService` |
| **Pexels API** | `PEXELS_API_KEY` | `https://api.pexels.com/v1` | `Authorization` | `NewsImageService` |
| **QR Server** | *(Pública)* | `https://api.qrserver.com/v1/create-qr-code/` | — | `TotpService` |

### 5.3. `BreedService` ([app/Services/BreedService.php](file:///d:/erasmus/ntp/NTPWS-26_27/app/Services/BreedService.php))

Responsável por enriquecer a ficha de cada animal (`PetController@show` → `resources/views/public/pets/show.blade.php`) com dados científicos e comportamentais da respetiva raça através das APIs **TheDogAPI** e **TheCatAPI**:

```mermaid
flowchart TD
    A["getBreedInfo(species, breedName)"] --> B{"Existe em breed_cache?"}
    B -- "Sim e < 24h (!isStale)" --> C["Retorna cache ('source' => 'cache')"]
    B -- "Não existe ou >= 24h" --> D["Http::timeout(10)->get('/breeds/search?q=...')"]
    D -- "HTTP 200 & JSON válido" --> E{"Tem reference_image_id?"}
    E -- Sim --> F["Http::timeout(10)->get('/images/{id}')"]
    E -- Não --> G["Usa image.url se existir"]
    F --> H["BreedCache::updateOrCreate()"]
    G --> H
    H --> I["Retorna dados ('source' => 'api')"]
    D -- "Timeout / Erro HTTP / Exception" --> J{"Existe cache expirado?"}
    J -- Sim --> K["Log::warning() + Retorna cache stale ('source' => 'cache (stale - API unavailable)')"]
    J -- Não --> L["Retorna null (Vista exibe mensagem neutra)"]
```

- **Estratégia Cache-First (`CACHE_HOURS = 24`)**:
  1. Consulta `BreedCache::where('species', $species)->where('breed_name', $breedName)->first()`.
  2. Se existir e `fetched_at` tiver menos de 24 horas (`!$cached->isStale()`), devolve imediatamente `data_json`, `image_url`, `fetched_at` e `source: 'cache'`.
- **Comunicação HTTP & Extração Estruturada (`fetchFromApi`)**:
  - Seleciona o endpoint base consoante a espécie (`DOG_API_URL` ou `CAT_API_URL`) e injeta o header `x-api-key` se configurado.
  - Executa `GET /breeds/search?q={breedName}` com `TIMEOUT_SECONDS = 10`.
  - Valida o payload (`isset($breed['name'])`) e normaliza os campos: `name`, `temperament`, `life_span`, `weight` (preferência por métrico), `height`, `bred_for`, `breed_group`, `origin` e `description`.
  - Caso exista `reference_image_id`, realiza uma segunda chamada `GET /images/{reference_image_id}` para obter o URL oficial da foto de referência.
  - Persiste ou atualiza o registo em `breed_cache` com `updateOrCreate()`.
- **Degradação Graciosa & Simulação**:
  - Em caso de falha de rede ou erro HTTP (status não-2xx lança `RuntimeException`), o bloco `catch` regista `Log::warning(...)` e devolve os dados expirados da base de dados com `source: 'cache (stale - API unavailable)'`.
  - Inclui ainda `getCachedBreeds(species)` para consulta de raças já em cache e `simulateFailure(species, breedName)` para demonstração de tratamento de falhas.

### 5.4. `NewsImageService` ([app/Services/NewsImageService.php](file:///d:/erasmus/ntp/NTPWS-26_27/app/Services/NewsImageService.php))

Garante que todos os artigos de notícias sem fotografia manual tenham uma imagem de cabeçalho profissional e contextualmente relevante, com cache de **7 dias (`CACHE_HOURS = 168`)** sob a chave `"news_image_{$articleId}"`:

- **Mapeamento Semântico & Construção de Query (`buildSearchQuery`)**:
  - Se a categoria pertencer ao mapa predefinido, usa palavras-chave otimizadas:
    - `adoption-stories` ➔ `"happy pet family adoption"`
    - `health-care` ➔ `"veterinary pet health care"`
    - `events` ➔ `"pet community event outdoor"`
    - `volunteer` ➔ `"volunteer animal shelter helping"`
    - `tips-training` ➔ `"dog training pet obedience"`
  - Caso contrário, extrai até 3 palavras com $\ge 4$ caracteres do título do artigo e concatena `" pet animal"`, usando `"animal shelter pet care"` como último recurso.
- **Integração Pexels API (`fetchFromPexels`)**:
  - Se `config('services.pexels.key')` estiver definida, envia `GET https://api.pexels.com/v1/search` com header `Authorization: {apiKey}`, `TIMEOUT_SECONDS = 8`, `per_page = 15` e `orientation = landscape`.
- **Variedade Determinística & Fallback Unsplash**:
  - Seleciona a fotografia através de `$articleId % count($photos)`, garantindo que o mesmo artigo mantém sempre a mesma imagem, mas artigos distintos da mesma categoria recebem fotos diferentes.
  - Se a Pexels API não tiver chave configurada, falhar ou não devolver resultados, recorre às coleções curadas de URLs do Unsplash organizadas por categoria (`getFallbackUrl()`).

### 5.5. `PetImageService` ([app/Services/PetImageService.php](file:///d:/erasmus/ntp/NTPWS-26_27/app/Services/PetImageService.php))

Resolve fotografias específicas da raça de cada animal através das APIs **TheDogAPI** / **TheCatAPI**, minimizando chamadas com dois níveis de cache:

1. **Cache de Imagem do Animal (`CACHE_HOURS = 168`, 7 dias)**:
   - Chave `"pet_image_{$petId}_{$species}_" . md5($breed ?? 'unknown')`.
2. **Pipeline de Resolução em 3 Níveis (`fetchFromApi`)**:
   - **Nível 1 (Por Raça):** Invoca `findBreedId()`, que pesquisa `GET /breeds/search?q={breedName}` (com timeout de 8s) e guarda o ID da raça em cache durante **30 dias (`addDays(30)`)** sob a chave `"breed_id_" . md5($baseUrl . $breedName)`. Com o `breedId`, consulta `GET /images/search?breed_ids={id}&limit=10&size=med` e seleciona `$images[$petId % count($images)]['url']`.
   - **Nível 2 (Por Espécie):** Se a raça não for encontrada, consulta `GET /images/search?limit=1&size=med` para obter uma imagem da respetiva espécie.
   - **Nível 3 (Fallback Curado Unsplash):** Em caso de exceção ou indisponibilidade da API, seleciona deterministicamente uma foto de reserva (`$dogPhotos` ou `$catPhotos`) por `$petId % count($photos)`.

---

## 6. Autenticação, 2FA (TOTP) e Controlo de Acesso Baseado em Papéis (RBAC)

O PawSafe implementa um sistema de segurança multicamada (Defense in Depth).

```mermaid
flowchart TD
    A["Tentativa de Acesso"] --> B{"Está Autenticado?"}
    B -- Não --> C["Permitir Acesso Público ou Redirecionar para /login"]
    B -- Sim --> D{"Conta Ativa? (CheckActiveUser)"}
    D -- Inativa --> E["Logout Imediato + Invalidação de Sessão + Erro"]
    D -- Ativa --> F{"Tem 2FA Configurado?"}
    F -- Sim e não verificado --> G["Desafio 2FA (/two-factor-challenge)"]
    F -- Não ou já verificado --> H{"Verificar Papel (CheckRole)"}
    H -- Rota Admin / Gestão --> I{"Papel do Utilizador?"}
    I -- user --> J["403 Forbidden"]
    I -- editor --> K{"Recurso pertence ao editor ou é Notícia?"}
    K -- Sim --> L["200 OK - Acesso Concedido"]
    K -- Não (ex: Categorias, Animais) --> J
    I -- admin --> L
```

### 6.1. Matriz de Controlo de Acessos (RBAC)

O sistema distingue explicitamente 4 níveis de privilégio:

| Funcionalidade / Rota | Visitante (Guest) | Utilizador (`user`) | Editor (`editor`) | Administrador (`admin`) |
|---|:---:|:---:|:---:|:---:|
| Ver Página Inicial, Sobre e Catálogo | Sim | Sim | Sim | Sim |
| Ver Notícias Publicadas e Detalhe | Sim | Sim | Sim | Sim |
| Ver Rascunhos de Notícias | Não | Não | Próprios | Todos |
| Submeter Pedido de Adoção | Não | Sim | Sim | Sim |
| Gerir o Próprio Perfil e Palavra-passe | Não | Sim | Sim | Sim |
| Ativar / Gerir 2FA (TOTP) | Não | Sim | Sim | Sim |
| Aceder ao Painel `/admin` | Não | Não | Sim (Limitado) | Sim (Total) |
| Criar Notícias no CMS / API | Não | Não | Sim | Sim |
| Editar / Eliminar Notícias de Outros | Não | Não | Não (403) | Sim |
| Gerir Categorias de Notícias | Não | Não | Não (403) | Sim |
| Gerir Animais (Criar, Editar, Fotos) | Não | Não | Não (403) | Sim |
| Decidir Pedidos de Adoção | Não | Não | Não (403) | Sim |
| Gerir Utilizadores, Papéis e Ativações | Não | Não | Não (403) | Sim |

### 6.2. Análise dos Middlewares de Segurança

#### 1. `CheckRole` ([app/Http/Middleware/CheckRole.php](file:///d:/erasmus/ntp/NTPWS-26_27/app/Http/Middleware/CheckRole.php))
- **Parâmetros Variádicos**: Aceita múltiplos papéis na declaração de rota: `middleware('role:admin,editor')`.
- **Comportamento em API vs Web**:
  - Se a requisição for API ou esperar JSON (`expectsJson()`), devolve `401 Unauthorized` (caso não autenticado) ou `403 Forbidden` com payload JSON padronizado.
  - Se for requisição web normal, redireciona para o login ou aborta com a página de erro `403`.
- **Verificação de Conta Ativa Integrada**: Antes de verificar o papel, confirma se o utilizador está ativo. Se estiver desativado, efetua logout de imediato e invalida o token de sessão.

#### 2. `CheckActiveUser` ([app/Http/Middleware/CheckActiveUser.php](file:///d:/erasmus/ntp/NTPWS-26_27/app/Http/Middleware/CheckActiveUser.php))
- Registado globalmente no grupo de middlewares `web` em [bootstrap/app.php](file:///d:/erasmus/ntp/NTPWS-26_27/bootstrap/app.php).
- **Invalidação Ativa de Sessões Existentes**: Se um administrador desativar a conta de um utilizador enquanto este estiver com o browser aberto, a próxima requisição que o utilizador fizer aciona este middleware, que termina a autenticação com `auth()->logout()`, invalida a sessão (`session()->invalidate()`), regenera o token CSRF (`regenerateToken()`) e redireciona com mensagem explicativa.

#### 3. `SetLocale` ([app/Http/Middleware/SetLocale.php](file:///d:/erasmus/ntp/NTPWS-26_27/app/Http/Middleware/SetLocale.php))
- Inspeciona a variável de sessão `locale`. Se corresponder a um dos idiomas permitidos (`pt`, `en`, `hr`), define o locale da aplicação através de `App::setLocale($locale)`. Caso contrário, adota o valor por omissão (`pt`).

### 6.3. Fluxo de Registo, Login e 2FA

1. **Registo (`AuthController::register`)**:
   - Valida unicidade de email e força de palavra-passe com confirmação (`confirmed`).
   - **Prevenção de Escalação de Privilégios**: O sistema força programaticamente a atribuição do papel `Role::USER`. O formulário não contém nem aceita qualquer campo que permita ao utilizador escolher outro papel.
   - Aplica `Auth::login($user)` e executa imediatamente `$request->session()->regenerate()` para prevenir **ataques de fixação de sessão (Session Fixation)**.
2. **Login (`AuthController::login`)**:
   - Valida credenciais com `Auth::validate($credentials)`.
   - Verifica se `user->is_active` é falso. Se for, bloqueia antes mesmo de abrir sessão.
   - **Interceção de 2FA**: Se o utilizador tiver a 2FA confirmada (`$user->hasTwoFactorEnabled()`), o sistema **não autentica o utilizador imediatamente**. Em vez disso, armazena temporariamente na sessão os valores `2fa:user_id` e `2fa:remember`, e redireciona o browser para a rota `/two-factor-challenge`.
   - Caso não tenha 2FA, autentica diretamente e regenera a sessão.
3. **Desafio de 2FA (`TwoFactorController::verifyChallenge`)**:
   - Permite a validação através do código de 6 dígitos TOTP ou de um código de recuperação de emergência (`recovery_code`).
   - Se for usado um código de recuperação: normaliza o código (remove espaços e converte para maiúsculas), localiza o código no array `$user->two_factor_recovery_codes`, remove-o do array (invalidação de uso único), grava o utilizador na base de dados e conclui a autenticação.

```mermaid
sequenceDiagram
    actor U as Utilizador
    participant AC as AuthController
    participant TFC as TwoFactorController
    participant TS as TotpService
    participant S as Session / DB

    U->>AC: POST /login (email, password)
    AC->>S: Auth::validate() & verifica is_active
    alt 2FA Ativa
        AC->>S: Guarda 2fa:user_id e 2fa:remember
        AC-->>U: Redirect /two-factor-challenge
        U->>TFC: POST /two-factor-challenge (code ou recovery_code)
        alt Código TOTP 6 dígitos
            TFC->>TS: verify(secret, code, window=1)
            TS-->>TFC: true (hash_equals constante)
        else Código de Recuperação
            TFC->>S: Valida e remove código único de two_factor_recovery_codes
        end
        TFC->>S: Auth::loginUsingId() + session()->regenerate()
        TFC-->>U: Redirect / (Autenticado)
    else Sem 2FA
        AC->>S: Auth::login() + session()->regenerate()
        AC-->>U: Redirect /
    end
```

---

## 7. Controladores e Lógica de Negócio (Controllers Breakdown)

### 7.1. Controladores Públicos

#### `HomeController` ([app/Http/Controllers/HomeController.php](file:///d:/erasmus/ntp/NTPWS-26_27/app/Http/Controllers/HomeController.php))
- `index()`: Carrega as 3 notícias publicadas mais recentes com eager loading das relações (`with(['category', 'author'])`), os 6 animais disponíveis mais recentes e agrega contadores estatísticos em tempo real (total de animais, adotados e disponíveis).
- `about()`: Renderiza a página explicativa do projeto PawSafe.
- `changeLocale(string $locale)`: Valida se `$locale` pertence a `SetLocale::SUPPORTED_LOCALES`. Se sim, guarda na sessão e redireciona de volta.

#### `NewsController` ([app/Http/Controllers/NewsController.php](file:///d:/erasmus/ntp/NTPWS-26_27/app/Http/Controllers/NewsController.php))
- `index(Request $request)`:
  - Consulta apenas notícias com escopo `published()`.
  - Pesquisa dinâmica por texto: `WHERE title LIKE ? OR summary LIKE ? OR content LIKE ?`.
  - Filtro por slug de categoria.
  - Paginação a 6 itens por página, mantendo os parâmetros de URL através de `->withQueryString()`.
- `show(string $slug)`:
  - Procura o artigo pelo `slug` com `firstOrFail()` garantindo que o artigo está no estado `published`.
  - Seleciona até 3 notícias relacionadas da mesma categoria para leitura sugerida.

#### `PetController` ([app/Http/Controllers/PetController.php](file:///d:/erasmus/ntp/NTPWS-26_27/app/Http/Controllers/PetController.php))
- `index(Request $request)`:
  - Filtra animais disponíveis por espécie (`dog`, `cat`), porte (`small`, `medium`, `large`), género e termo de pesquisa livre.
  - Paginação a 9 itens por página.
- `show(Pet $pet, BreedService $breedService)`:
  - Injeta o `BreedService` para pesquisar em tempo real os dados da raça do animal nas APIs externas.
  - Se o utilizador estiver autenticado, verifica se já existe uma candidatura de adoção submetida por ele para aquele animal específico.
- `submitAdoption(Request $request, Pet $pet)`:
  - Requer autenticação (`middleware('auth')`).
  - Valida se o animal ainda se encontra com estado `available`.
  - Valida que o utilizador ainda não submeteu candidatura para aquele animal (`AdoptionRequest::where(...)`).
  - Valida o tamanho mínimo e máximo da mensagem (20 a 1000 caracteres).
  - Cria o registo com estado inicial `pending`.

#### `ProfileController` ([app/Http/Controllers/ProfileController.php](file:///d:/erasmus/ntp/NTPWS-26_27/app/Http/Controllers/ProfileController.php))
- `show()`: Apresenta os dados do utilizador, estado da 2FA e histórico dos seus pedidos de adoção.
- `update()`: Atualização de nome, email e número de telefone. Suporta upload de avatar com validação estrita de imagem (JPEG, PNG, WEBP até 2MB) e eliminação automática do ficheiro de avatar antigo do disco (`Storage::delete`).
- `changePassword()`: Validação da senha atual com `Hash::check` antes de permitir a substituição pela nova senha.

### 7.2. Controladores Administrativos (`app/Http/Controllers/Admin/`)

#### `DashboardController` ([app/Http/Controllers/Admin/DashboardController.php](file:///d:/erasmus/ntp/NTPWS-26_27/app/Http/Controllers/Admin/DashboardController.php))
- Agrega métricas globais para a equipa: utilizadores ativos, total de notícias, rascunhos pendentes, animais adotados, animais aguardando família e pedidos de adoção pendentes de triagem.
- Lista rápida das 5 notícias mais recentes e dos 5 últimos pedidos de adoção submetidos.

#### `AdminNewsController` ([app/Http/Controllers/Admin/AdminNewsController.php](file:///d:/erasmus/ntp/NTPWS-26_27/app/Http/Controllers/Admin/AdminNewsController.php))
- **Filtro de Visibilidade por Papel**:
  - Administradores veem todos os artigos do sistema.
  - Editores veem **estritamente apenas os seus próprios artigos** (`where('author_id', Auth::id())`).
- **Verificação Estrita de Autoria no Servidor**: Nos métodos `edit`, `update` e `destroy`, caso um editor tente aceder a uma notícia cujo `author_id` seja diferente do seu ID, o servidor rejeita imediatamente com `abort(403)`.
- **Sanitização de HTML (`sanitizeHtml`)**:
  - O conteúdo submetido pelo editor através do Quill.js é higienizado no servidor com uma lista autorizada estrita de tags:
    ```php
    private function sanitizeHtml(string $html): string
    {
        $allowed = '<p><br><strong><em><u><s><h1><h2><h3><h4><h5><h6><ul><ol><li><a><img><blockquote><pre><code><table><thead><tbody><tr><th><td><hr><span><div><sub><sup>';
        return strip_tags($html, $allowed);
    }
    ```
- **Gestão de Imagens**: Permite carregar nova fotografia (`image_path`), substituir fotografia existente (apagando a anterior do storage) ou remover a fotografia (`remove_image = true`).

#### `AdminPetController` ([app/Http/Controllers/Admin/AdminPetController.php](file:///d:/erasmus/ntp/NTPWS-26_27/app/Http/Controllers/Admin/AdminPetController.php))
- Protegido exclusivamente para administradores (`middleware('role:admin')`).
- CRUD completo de animais com upload de fotos para `storage/app/public/pets`.
- **Decisão de Adoção em Transação de Base de Dados (`updateAdoptionRequest`)**:
  - Quando um administrador aprova um pedido de adoção (`status = 'approved'`), a ação é encapsulada numa transação atómica (`DB::transaction`):
    1. O pedido em causa passa a `approved`.
    2. O estado do animal é alterado para `pending` (em processo de entrega).
    3. Todos os outros pedidos de adoção concorrentes para aquele mesmo animal são automaticamente marcados como `rejected`, com a nota administrativa explicativa: *"Another adoption request was approved for this pet."*
  - Se ocorrer algum erro durante a operação, a transação reverte todas as alterações (Rollback), garantindo que a base de dados nunca fica em estado inconsistente.

#### `AdminCategoryController` ([app/Http/Controllers/Admin/AdminCategoryController.php](file:///d:/erasmus/ntp/NTPWS-26_27/app/Http/Controllers/Admin/AdminCategoryController.php))
- Protegido para administradores.
- **Proteção Contra Eliminação Órfã**:
  - No método `destroy`, antes de eliminar a categoria, executa:
    ```php
    if ($category->news()->count() > 0) {
        return back()->with('error', 'Cannot delete category that contains news articles. Reassign them first.');
    }
    ```
  - Esta validação a nível de aplicação complementa a regra `restrictOnDelete()` existente na migração da base de dados.

#### `AdminUserController` ([app/Http/Controllers/Admin/AdminUserController.php](file:///d:/erasmus/ntp/NTPWS-26_27/app/Http/Controllers/Admin/AdminUserController.php))
- Protegido para administradores.
- Permite alterar o papel de qualquer utilizador (`role_id`) e ativar/desativar a conta (`is_active`).
- **Expurgo Forçado de Sessões**:
  - Ao desativar uma conta (`is_active = false`), executa uma query direta à tabela `sessions` da base de dados:
    ```php
    DB::table('sessions')->where('user_id', $user->id)->delete();
    ```
  - Isto assegura que qualquer cookie de sessão existente no computador do utilizador perde imediatamente a validade na base de dados.

---

## 8. API RESTful Própria (v1) & JSON Schema

A aplicação expõe uma API RESTful versionada sob o prefixo `/api/v1`, definida em [routes/api.php](file:///d:/erasmus/ntp/NTPWS-26_27/routes/api.php) e implementada em [ApiNewsController.php](file:///d:/erasmus/ntp/NTPWS-26_27/app/Http/Controllers/Api/ApiNewsController.php).

### 8.1. Especificação e Arquitetura dos Endpoints

| Método | Endpoint | Proteção / Middleware | Resposta Sucesso | Códigos de Erro | Regras e Comportamento |
|---|---|---|:---:|:---:|---|
| `GET` | `/api/v1/news` | Pública | `200 OK` | `400` | Lista notícias `published()` ordenadas por `published_at DESC`. Aceita `category_id`, `search` (`title`/`summary`) e `per_page` (máx. `50`, default `10`). |
| `GET` | `/api/v1/news/{id}` | Pública | `200 OK` | `404` | Devolve o detalhe de uma notícia publicada com relações `category` e `author:id,name`. Rascunhos/retiradas devolvem `404`. |
| `GET` | `/api/v1/categories` | Pública | `200 OK` | — | Lista todas as categorias ordenadas por `name`, com `news_count` contando apenas artigos `published()`. |
| `GET` | `/api/v1/schema` | Pública | `200 OK` | — | Serve `public/schemas/news-schema.json` com header `Content-Type: application/schema+json`. |
| `POST` | `/api/v1/news` | `auth` + `role:editor,admin` | `201 Created` | `400, 401, 403` | Valida campos obrigatórios, gera slug (`Str::slug($title).'-'.Str::random(5)`), higieniza HTML via `strip_tags()`, define `author_id` e `published_at`. |
| `PATCH` | `/api/v1/news/{id}` | `auth` + `role:editor,admin` | `200 OK` | `400, 401, 403, 404` | Atualização parcial (`sometimes`). Editores só alteram os seus próprios artigos (BOLA check); suporta `status` `draft`/`published`/`withdrawn`. |
| `DELETE` | `/api/v1/news/{id}` | `auth` + `role:editor,admin` | `204 No Content` | `401, 403, 404` | Verifica autoria (editores só apagam próprios artigos) e devolve `response()->noContent()` sem corpo HTTP. |

### 8.2. Exemplos de Requisições e Semântica HTTP

- **Listagem Paginada (`GET /api/v1/news?category_id=1&per_page=3`) — `200 OK`**:
  ```json
  {
    "success": true,
    "data": [
      {
        "id": 1,
        "title": "Max Found His Forever Home!",
        "slug": "max-found-his-forever-home",
        "summary": "...",
        "status": "published",
        "published_at": "2026-11-24T12:00:00.000000Z",
        "category": { "id": 1, "name": "Adoption Stories", "slug": "adoption-stories" },
        "author": { "id": 2, "name": "Maria Santos" }
      }
    ],
    "meta": {
      "current_page": 1,
      "per_page": 3,
      "total": 6,
      "last_page": 2
    }
  }
  ```
- **Criação de Artigo (`POST /api/v1/news`) — `201 Created`**:
  ```bash
  curl -X POST http://127.0.0.1:8000/api/v1/news \
    -H "Accept: application/json" \
    -H "Content-Type: application/json" \
    -d '{"title":"Campanha de Adoção","summary":"Resumo do evento...","content":"<p>Texto rico</p>","category_id":1,"status":"published"}'
  ```
- **Sucesso Sem Conteúdo (`DELETE /api/v1/news/{id}`) — `204 No Content`**:
  - Produzido por `response()->noContent()`. Sem qualquer cabeçalho de corpo ou bytes transmitidos, em estrita conformidade com a RFC 7231.
- **Erros de Validação (`400 Bad Request`)**:
  - O `ApiNewsController` utiliza `Validator::make()` explícito em vez do `validate()` padrão do Laravel para retornar código **400** (em vez de 422) com o envelope JSON padronizado:
  ```json
  {
    "success": false,
    "error": "Validation failed.",
    "details": {
      "title": ["The title field is required."],
      "category_id": ["The selected category id is invalid."]
    }
  }
  ```
- **Acesso Não Autorizado (`401 Unauthorized` / `403 Forbidden`)**:
  - Se não autenticado, o `bootstrap/app.php` (via `shouldRenderJsonWhen`) e `CheckRole` devolvem `401` em JSON. Se um editor tentar modificar artigo de outro autor, devolve `403`:
  ```json
  {
    "success": false,
    "error": "Forbidden. You can only edit your own articles."
  }
  ```

### 8.3. Especificação JSON Schema (`public/schemas/news-schema.json`)

O ficheiro [news-schema.json](file:///d:/erasmus/ntp/NTPWS-26_27/public/schemas/news-schema.json) define formalmente a estrutura de validação com base no padrão **JSON Schema Draft 2020-12** (incluindo os objetos aninhados opcionais `category` e `author` e `additionalProperties: false`):

```json
{
  "$schema": "https://json-schema.org/draft/2020-12/schema",
  "$id": "https://pawsafe.org/schemas/news-schema.json",
  "title": "PawSafe News Article",
  "type": "object",
  "required": ["id", "title", "slug", "summary", "content", "status", "created_at"],
  "properties": {
    "id": { "type": "integer", "minimum": 1 },
    "title": { "type": "string", "minLength": 5, "maxLength": 255 },
    "slug": { "type": "string", "pattern": "^[a-z0-9]+(?:-[a-z0-9]+)*$" },
    "summary": { "type": "string", "minLength": 10, "maxLength": 300 },
    "content": { "type": "string", "minLength": 20 },
    "category": { "type": ["object", "null"], "required": ["id", "name", "slug"] },
    "author": { "type": ["object", "null"], "required": ["id", "name"] },
    "status": { "type": "string", "enum": ["draft", "published", "withdrawn"] },
    "image_url": { "type": ["string", "null"], "format": "uri" },
    "published_at": { "type": ["string", "null"], "format": "date-time" }
  },
  "additionalProperties": false
}
```

### 8.4. Consumo Real da Própria API na Aplicação

O enunciado exige expressamente que *"pelo menos uma parte da aplicação deve usar a sua própria API"*.

Esta funcionalidade está implementada em [resources/views/public/news/index.blade.php](file:///d:/erasmus/ntp/NTPWS-26_27/resources/views/public/news/index.blade.php), dentro do contentor `#api-news-widget`:

1. **Estado Inicial**: Exibe um spinner animado com a mensagem traduzida `__('messages.api_loading')`.
2. **Requisição Assíncrona**: Um script JavaScript nativo executa `fetch('/api/v1/news?per_page=3')`, verifica `if (!res.ok) throw new Error('HTTP ' + res.status)` e converte a resposta com `res.json()`.
3. **Renderização Dinâmica no DOM**: Itera sobre `json.data`, formata `published_at` com `toLocaleDateString('pt-PT')`, cria ligações para `/news/{slug}` com o badge da categoria e substitui o spinner no DOM.
4. **Tratamento de Erro no Cliente**: O bloco `.catch()` apresenta uma mensagem de erro legível caso a chamada falhe.

---

## 9. Camada de Apresentação (Blade, Quill.js, Vite & Multi-Idioma)

### 9.1. Layouts Mestres

- **Layout Público** ([resources/views/layouts/app.blade.php](file:///d:/erasmus/ntp/NTPWS-26_27/resources/views/layouts/app.blade.php)):
  - Barra de navegação responsiva com links para Início, Notícias, Animais, Sobre e API REST.
  - Seletor de idioma dinâmico exibindo bandeiras SVG de alta definição ([public/images/flags/](file:///d:/erasmus/ntp/NTPWS-26_27/public/images/flags/)).
  - Gestão de estado de autenticação: exibe botão para o Painel CMS caso o utilizador tenha papel de Administrador ou Editor, perfil do utilizador e botão de terminar sessão com proteção de token `@csrf`.
  - Contentor de mensagens flash de sucesso, aviso e erro com animações visuais.
  - Rodapé em 4 colunas com links diretos para a documentação da API e JSON Schema.
- **Layout Administrativo** ([resources/views/layouts/admin.blade.php](file:///d:/erasmus/ntp/NTPWS-26_27/resources/views/layouts/admin.blade.php)):
  - Barra lateral (Sidebar) inteiramente traduzida (PT/EN/HR) com navegação dedicada para Dashboard, Notícias, Categorias, Animais, Utilizadores e atalho para o site público.
  - Seletor de idioma integrado diretamente na barra superior do painel administrativo.
  - Indicação em tempo real do utilizador autenticado com badge visual do seu papel (`admin` a roxo, `editor` a azul).

### 9.2. Integração do Editor Rich-Text (Quill.js v2)

Nos formulários de criação ([resources/views/admin/news/create.blade.php](file:///d:/erasmus/ntp/NTPWS-26_27/resources/views/admin/news/create.blade.php)) e edição de notícias ([resources/views/admin/news/edit.blade.php](file:///d:/erasmus/ntp/NTPWS-26_27/resources/views/admin/news/edit.blade.php)):

- O `<textarea>` simples foi substituído pelo contentor `<div id="quill-editor">` alimentado pelo motor do **Quill.js v2**.
- A barra de ferramentas disponibiliza opções ricas: cabeçalhos `H1-H3`, formatação de texto (`bold`, `italic`, `underline`, `strike`), listas com marcadores e numeração, blocos de citação (`blockquote`), blocos de código (`code-block`), links e imagens.
- A sincronização é feita através de JavaScript no momento do envio (`onsubmit`), preenchendo o campo oculto `#content` com `quill.root.innerHTML` antes da sanitização server-side.

### 9.3. Sistema de Internacionalização (i18n) Trilingue Completo

O PawSafe suporta três idiomas integrais tanto na interface pública como em todas as vistas do painel administrativo:
- **Português (`pt`)** — Idioma principal padrão ([lang/pt/messages.php](file:///d:/erasmus/ntp/NTPWS-26_27/lang/pt/messages.php)).
- **Inglês (`en`)** — Para alcance internacional e Erasmus ([lang/en/messages.php](file:///d:/erasmus/ntp/NTPWS-26_27/lang/en/messages.php)).
- **Croata (`hr`)** — Conforme solicitado nos requisitos ([lang/hr/messages.php](file:///d:/erasmus/ntp/NTPWS-26_27/lang/hr/messages.php)).

Cada ficheiro contém exatamente **334 chaves de tradução** perfeitamente alinhadas, cobrindo:
- Navegação, menus, rodapé e bandeiras vetoriais (`pt.svg`, `gb.svg`, `hr.svg`).
- Páginas públicas: Início, Sobre o Projeto (`about.blade.php`), Notícias e detalhes (`news/show.blade.php`), Catálogo e ficha de animais (`pets/show.blade.php`).
- Autenticação, Perfil e Configuração/Desafio 2FA.
- **Painel Administrativo completo**: `dashboard.blade.php`, `categories/*`, `news/*`, `pets/*`, `users/*` e `layouts/admin.blade.php`.

A comutação é feita através da rota `/locale/{locale}` tratada por `HomeController::changeLocale`, que valida o idioma contra `SetLocale::SUPPORTED_LOCALES`, armazena a escolha na sessão HTTP e redireciona via `redirect()->back(fallback: route('home'))` para evitar loops caso não exista cabeçalho `Referer`.

---

## 10. Segurança em Profundidade (Defense in Depth)

A arquitetura do PawSafe foi concebida segundo o modelo de defesa em profundidade, garantindo que a segurança não depende de uma única barreira:

| Vetor de Risco | Vulnerabilidade Típica | Mecanismo de Defesa Implementado no PawSafe |
|---|---|---|
| **SQL Injection (SQLi)** | Injeção de código SQL em campos de pesquisa e formulários | Uso exclusivo do Eloquent ORM e queries parametrizadas com PDO binding seguro em todas as consultas. |
| **Cross-Site Scripting (XSS)** | Injeção de scripts maliciosos em artigos de notícias | Escaping automático padrão do motor Blade (`{{ }}`); dupla sanitização com `strip_tags()` e whitelist estrita no servidor para conteúdo rich-text. |
| **CSRF Attacks** | Submissão forçada de requisições maliciosas a partir de outros sites | Proteção global com middleware `VerifyCsrfToken` do Laravel; diretiva `@csrf` em todos os formulários e regeneração na alteração de sessão. |
| **Session Fixation** | Fixação de sessão prévia após login | Execução obrigatória de `$request->session()->regenerate()` após login bem-sucedido e confirmação de 2FA. |
| **Credential Stuffing / Brute-Force** | Adivinhação de palavras-passe | Implementação de Autenticação de Dois Fatores (TOTP RFC 6238) com códigos dinâmicos válidos por apenas 30 segundos. |
| **Timing Attacks** | Análise de tempo de resposta para inferência de dados secretos | Comparação de códigos TOTP e hashes com `hash_equals()`, garantindo tempo de execução constante. |
| **Privilege Escalation** | Utilizadores alterarem o seu próprio papel no registo ou perfil | Atribuição fixa do papel `user` no registo; validações estritas no servidor; restrição de rota `/admin/users` a administradores. |
| **Broken Object Level Auth (BOLA)** | Editores alterarem artigos de outros editores através de manipulação de ID | Verificação dupla no servidor (`$news->author_id !== Auth::id()`) no controlador web e na API REST (`ApiNewsController`). |
| **File Upload Execution** | Upload de ficheiros executáveis `.php` disfarçados de imagem | Validação estrita de tipos MIME (`mimes:jpeg,png,jpg,gif,webp`), limite de 5MB, gravação com nomes gerados por hash e armazenamento fora da raiz do código. |
| **Zombie Sessions** | Utilizador desativado continuar com acesso através de cookies antigos | Middleware `CheckActiveUser` verifica a coluna `is_active` em todas as requisições e apaga registos na tabela `sessions`. |

---

## 11. Testes Automatizados & Qualidade de Software

O projeto dispõe de uma suíte completa de **39 testes automatizados (146 asserções)** baseados em PHPUnit 12 e na framework de testes do Laravel com o trait `RefreshDatabase`, garantindo isolamento total da base de dados entre execuções:

```bash
php artisan test
```

### Detalhe das Classes de Teste (`tests/Feature/` e `tests/Unit/`)

1. **`AuthTest.php`** ([tests/Feature/AuthTest.php](file:///d:/erasmus/ntp/NTPWS-26_27/tests/Feature/AuthTest.php)) — **10 testes**:
   - Registo com dados válidos e atribuição obrigatória do papel `user`.
   - Rejeição de registos com emails duplicados ou confirmação de palavra-passe inválida.
   - Verificação de login com credenciais válidas, inválidas e bloqueio imediato de contas com `is_active = false`.
   - Redirecionamento de visitantes para `/login` ao tentar aceder a `/admin`.
   - Resposta `403 Forbidden` quando um utilizador comum tenta aceder a `/admin` ou quando um editor tenta editar artigo de outro autor.
2. **`ApiTest.php`** ([tests/Feature/ApiTest.php](file:///d:/erasmus/ntp/NTPWS-26_27/tests/Feature/ApiTest.php)) — **7 testes**:
   - Garante que `GET /api/v1/news` devolve apenas notícias publicadas e nunca rascunhos.
   - `GET /api/v1/news/{id}` devolve `404 Not Found` para rascunhos.
   - `POST /api/v1/news` exige autenticação (`401`), valida payload (`400`) e permite criação por editor/admin (`201`).
   - `DELETE /api/v1/news/{id}` devolve `204 No Content` sem corpo.
3. **`TwoFactorTest.php`** ([tests/Feature/TwoFactorTest.php](file:///d:/erasmus/ntp/NTPWS-26_27/tests/Feature/TwoFactorTest.php)) — **9 testes**:
   - Acesso ao ecrã de setup e geração de segredo Base32.
   - Confirmação com código TOTP válido gerado pelo `TotpService` e rejeição de códigos inválidos.
   - Redirecionamento obrigatório para `/two-factor-challenge` durante o login de utilizadores com 2FA.
   - Conclusão bem-sucedida do login com código TOTP ou código de recuperação (com **invalidação de uso único**).
   - Desativação protegida de 2FA mediante confirmação da palavra-passe atual.
4. **`PawSafePagesTest.php`** ([tests/Feature/PawSafePagesTest.php](file:///d:/erasmus/ntp/NTPWS-26_27/tests/Feature/PawSafePagesTest.php)) — **11 testes**:
   - Carregamento com sucesso de todas as rotas públicas (`/`, `/about`, `/news`, `/news/{slug}`, `/pets`, `/pets/{pet}`).
   - Respostas da API `/api/v1/news` e do endpoint de JSON Schema `/api/v1/schema`.
   - Comutação dinâmica de idioma para Inglês (`/locale/en`), Croata (`/locale/hr`) e Português (`/locale/pt`), validando strings renderizadas no portal público e no painel admin.
   - Rejeição de locales inválidos (ex: `/locale/de`), mantendo a sessão protegida.
5. **`ExampleTest.php` (`Feature` e `Unit`)** — **2 testes base** de verificação estrutural da aplicação.

---

## 12. Automação, Vídeo Demonstrativo & Capturas de Ecrã (Playwright)

Para validação visual end-to-end e demonstração do sistema completo, o projeto integra scripts de automação baseados em **Playwright** (`scripts/`) operando sobre o servidor local PHP:

### 12.1. Vídeo Walkthrough Completo (`scripts/record_demo.cjs`)
- **Script:** [scripts/record_demo.cjs](file:///d:/erasmus/ntp/NTPWS-26_27/scripts/record_demo.cjs)
- **Vídeo Gerado:** [pawsafe-demo.webm](file:///d:/erasmus/ntp/NTPWS-26_27/pawsafe-demo.webm) (também disponível em [public/videos/pawsafe-demo.webm](file:///d:/erasmus/ntp/NTPWS-26_27/public/videos/pawsafe-demo.webm))
- **Player Interativo:** [public/demo.html](file:///d:/erasmus/ntp/NTPWS-26_27/public/demo.html) — interface web com 15 capítulos clicáveis que permitem saltar diretamente para cada funcionalidade gravada (Portal Público, PT/EN/HR, APIs de Raças, Registo/Login, 2FA QR Code, Submissão de Adoção, Admin Dashboard, Quill.js, RBAC e Admin Multilíngue).

### 12.2. Capturas de Ecrã em Inglês (`scripts/capture_screenshots.cjs`)
- **Script:** [scripts/capture_screenshots.cjs](file:///d:/erasmus/ntp/NTPWS-26_27/scripts/capture_screenshots.cjs)
- **Diretoria de Saída:** [screenshots/](file:///d:/erasmus/ntp/NTPWS-26_27/screenshots/) e [public/screenshots/](file:///d:/erasmus/ntp/NTPWS-26_27/public/screenshots/)
- **Imagens Geradas (1440x900 @ 1.5x DPR):**
  1. `01_home_page.png` — Página Inicial em Inglês
  2. `02_about_page.png` — Página "About Us" em Inglês
  3. `03_news_page.png` — Catálogo de Notícias em Inglês
  4. `04_pets_catalog.png` — Catálogo de Animais com Filtros em Inglês
  5. `05_pet_detail.png` — Detalhe do Animal + Integração Breed API em Inglês
  6. `06_admin_dashboard.png` — Painel de Administração em Inglês

---

## 13. Guia de Instalação, Configuração e Execução

### Pré-requisitos
- PHP 8.2 ou superior com extensões: `bcmath`, `ctype`, `cURL`, `dom`, `fileinfo`, `json`, `mbstring`, `openssl`, `pdo`, `sqlite3` ou `pdo_mysql`.
- Composer 2.x instalado.

### Passo a Passo de Execução Local

```bash
# 1. Clonar o repositório
git clone https://github.com/seu-repositorio/NTPWS-26_27.git
cd NTPWS-26_27

# 2. Instalar as dependências do Composer
composer install

# 3. Configurar as variáveis de ambiente
cp .env.example .env

# 4. Gerar a chave de aplicação
php artisan key:generate

# 5. Criar a base de dados SQLite (ou configurar MySQL no .env)
touch database/database.sqlite

# 6. Executar as migrações e popular a base de dados de demonstração
php artisan migrate:fresh --seed

# 7. Criar o link simbólico do storage público para imagens
php artisan storage:link

# 8. Iniciar o servidor de desenvolvimento
php artisan serve
```

A aplicação ficará disponível em `http://127.0.0.1:8000`.

### Credenciais Pré-configuradas de Demonstração

| Papel | Email | Palavra-passe | Nível de Acesso |
|---|---|---|---|
| **Administrador** | `admin@pawsafe.com` | `password123` | Acesso total ao sistema, gestão de utilizadores e configurações. |
| **Editor** | `editor@pawsafe.com` | `password123` | Criação e gestão das suas próprias notícias no painel e API. |
| **Utilizador Registado** | `user@pawsafe.com` | `password123` | Gestão do próprio perfil, 2FA e submissão de pedidos de adoção. |
| **Utilizador Inativo** | `inactive@pawsafe.com` | `password123` | Conta bloqueada para demonstração da recusa de acesso. |

---

## 14. Perguntas e Respostas para Defesa e Avaliação do Projeto

Esta secção prepara os autores do projeto para responder com segurança a perguntas técnicas colocadas pelo docente ou júri:

### P1: Como é garantido que utilizadores comuns não conseguem atribuir a si próprios privilégios de administrador no registo?
**R:** A proteção baseia-se em dois princípios:
1. No método `AuthController::register`, a atribuição do papel é codificada de forma estrita no servidor através de `$userRole = Role::where('name', Role::USER)->first(); $user->role_id = $userRole->id;`. Mesmo que um atacante intercete o pedido HTTP e injete um campo `role_id=1` ou `role=admin`, o Laravel ignora-o porque o campo só é preenchido pela variável interna de sistema.
2. Apenas os campos definidos no array `$fillable` de `User.php` são atribuíveis, e as rotas administrativas de alteração de papéis em [AdminUserController.php](file:///d:/erasmus/ntp/NTPWS-26_27/app/Http/Controllers/Admin/AdminUserController.php) estão blindadas pelo middleware `role:admin`.

### P2: O que acontece na aplicação se uma API externa (como TheDogAPI ou Pexels) estiver em baixo ou demorar muito tempo a responder?
**R:** O PawSafe adota uma estratégia de **Degradação Graciosa (Graceful Degradation)**:
1. Todas as chamadas HTTP utilizam um timeout estrito (`Http::timeout(10)` no `BreedService` e `timeout(8)` no `NewsImageService`), impedindo que o servidor PHP fique bloqueado à espera da resposta.
2. As chamadas estão protegidas por blocos `try/catch`. Caso a API falhe, o `BreedService` devolve os dados em cache existentes na tabela `breed_cache` (mesmo que tenham mais de 24 horas), marcando o campo `source` como `'cache (stale - API unavailable)'`.
3. O `NewsImageService` e o `PetImageService` recorrem imediatamente a coleções temáticas curadas de fotografias Unsplash locais de reserva. A interface pública nunca quebra e o utilizador não sofre qualquer interrupção de serviço.

### P3: Como é que o sistema impede a eliminação de uma categoria que contenha artigos de notícias associados?
**R:** Através de uma dupla barreira (aplicação + base de dados):
1. **Nível de Aplicação**: No método `AdminCategoryController::destroy`, o controlador verifica se `$category->news()->count() > 0`. Se houver notícias, a eliminação é abortada e é devolvida uma mensagem de erro ao administrador instruindo-o a reatribuir os artigos primeiro.
2. **Nível de Base de Dados**: Na migração `create_news_table.php`, a chave estrangeira foi declarada com `$table->foreignId('category_id')->constrained('categories')->restrictOnDelete()`. Se alguma operação tentasse ignorar o controlador, o próprio motor relacional da base de dados rejeitaria o comando `DELETE` por violação de integridade referencial.

### P4: Como funciona a implementação de Autenticação de Dois Fatores (2FA) e por que razão não utilizaram um pacote externo?
**R:** A 2FA foi desenvolvida do zero em [TotpService.php](file:///d:/erasmus/ntp/NTPWS-26_27/app/Services/TotpService.php) para garantir conformidade estrita com os padrões abertos RFC 4226 e RFC 6238 sem dependências de terceiros que possam conter vulnerabilidades ou quebrar com versões recentes do PHP:
1. Gera um segredo Base32 aleatório e seguro.
2. Calcula o código HMAC-SHA1 truncado a 6 dígitos em blocos temporais de 30 segundos.
3. Permite uma tolerância de $\pm 1$ bloco temporal (90 segundos de janela) para absorver eventuais dessincronizações entre o relógio do telemóvel do utilizador e o servidor.
4. Utiliza `hash_equals()` para prevenir timing attacks.
5. Fornece 8 códigos de emergência de uso único armazenados em formato array JSON na base de dados, onde cada código utilizado é automaticamente eliminado do registo do utilizador.

### P5: Como foi cumprido o requisito de que "pelo menos uma parte da aplicação deve usar a sua própria API"?
**R:** No módulo público de notícias ([resources/views/public/news/index.blade.php](file:///d:/erasmus/ntp/NTPWS-26_27/resources/views/public/news/index.blade.php)), foi introduzido um componente visual assíncrono. Em vez de receber os dados diretamente pelo controlador Blade, esse bloco executa um script JavaScript nativo no browser do cliente (`fetch('/api/v1/news?per_page=3')`). A resposta é processada via JSON e os cartões de notícias são criados e inseridos no DOM dinamicamente pelo cliente, demonstrando de forma prática o funcionamento e consumo real da API RESTful desenvolvida.

### P6: Como funciona a internacionalização dinâmica sem duplicar rotas e como evitar loops de redirecionamento?
**R:** Todas as vistas Blade utilizam a função auxiliar `__('messages.chave')` em conjunto com os dicionários em `lang/{pt,en,hr}/messages.php` (334 chaves alinhadas por idioma). O middleware global `SetLocale` lê `session('locale')` em cada requisição web e define `App::setLocale()`. Quando o utilizador clica numa bandeira, a rota `/locale/{locale}` executa `HomeController::changeLocale()`, que valida o idioma e devolve `redirect()->back(fallback: route('home'))` — o parâmetro `fallback` evita que um acesso direto sem cabeçalho `Referer` entre em ciclo infinito sobre si próprio.

### P7: Como garantem que a aprovação de uma adoção não deixa múltiplos candidatos aprovados para o mesmo animal?
**R:** O método `AdminPetController::updateAdoptionRequest()` encapsula toda a decisão numa transação atómica `DB::transaction()`. Ao aprovar um pedido, altera o estado do pedido selecionado para `approved`, atualiza o estado do animal para `pending` e marca automaticamente todos os outros pedidos concorrentes desse animal como `rejected`. Caso ocorra qualquer falha a meio, a base de dados reverte tudo (rollback). Adicionalmente, a tabela `adoption_requests` tem uma chave única composta `unique(['pet_id', 'user_id'])` impedindo candidaturas duplicadas do mesmo utilizador.

### P8: Como garantem que o HTML produzido pelo editor rich-text Quill.js é seguro contra XSS?
**R:** Embora o Quill.js produza HTML formatado no cliente, o servidor nunca confia no input recebido. Tanto no `AdminNewsController` (interface web) como no `ApiNewsController` (API REST), o conteúdo passa pelo método `sanitizeHtml()`, que aplica `strip_tags()` com uma whitelist estrita de tags estruturais (`<p>`, `<strong>`, `<em>`, `<h1>`-`<h6>`, `<ul>`, `<ol>`, `<li>`, `<a>`, `<img>`, `<blockquote>`, etc.), removendo qualquer tag `<script>`, `<iframe>` ou manipulador perigoso antes da persistência na base de dados.

---

*Ficheiro de documentação técnica exaustiva do projeto PawSafe — Desenvolvido no âmbito da Unidade Curricular de Tecnologias e Programação Web.*
