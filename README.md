# 🐾 PawSafe — Plataforma de Gestão de Abrigo e Adoção de Animais

PawSafe é uma aplicação web desenvolvida em Laravel para apoiar abrigos de animais na gestão de animais disponíveis para adopção, publicação de notícias e processamento de pedidos de adopção. A plataforma oferece uma interface pública para visitantes e adoptantes, bem como um painel de administração para editores e administradores.

---

## 🧰 Stack Tecnológico

| Camada | Tecnologia |
|--------|-----------|
| Backend | PHP 8.4 + Laravel 13 |
| Base de dados (dev) | SQLite |
| Base de dados (prod) | MySQL 8+ |
| Frontend | Blade + Tailwind CSS + Alpine.js |
| API | Laravel Sanctum (token-based) |
| Autenticação | Laravel Auth (formulários) |
| Testes | PHPUnit / `php artisan test` |

---

## ✅ Pré-requisitos

- PHP **8.4+** com extensões: `pdo`, `pdo_sqlite` (dev) ou `pdo_mysql` (prod), `mbstring`, `openssl`, `tokenizer`, `xml`, `ctype`, `fileinfo`
- [Composer](https://getcomposer.org/) 2.x
- Node.js 20+ e npm (para assets front-end, opcional)
- Git

---

## 🚀 Instalação

### 1. Clonar o repositório

```bash
git clone https://github.com/<org>/pawsafe.git
cd pawsafe
```

### 2. Instalar dependências PHP

```bash
composer install
```

### 3. Configurar o ambiente

```bash
cp .env.example .env
php artisan key:generate
```

### 4. Executar migrações e seeders

```bash
php artisan migrate --seed
```

### 5. Criar link simbólico para o storage

```bash
php artisan storage:link
```

### 6. Iniciar o servidor de desenvolvimento

```bash
php artisan serve
```

A aplicação ficará disponível em [http://localhost:8000](http://localhost:8000).

---

## ⚙️ Configuração do `.env`

As principais variáveis de ambiente a configurar:

```dotenv
APP_NAME=PawSafe
APP_ENV=local
APP_DEBUG=true
APP_URL=http://localhost:8000

# Base de dados — SQLite (desenvolvimento)
DB_CONNECTION=sqlite
# DB_DATABASE=/absolute/path/to/database.sqlite   # opcional, usa default se omitido

# Base de dados — MySQL (produção)
# DB_CONNECTION=mysql
# DB_HOST=127.0.0.1
# DB_PORT=3306
# DB_DATABASE=pawsafe
# DB_USERNAME=root
# DB_PASSWORD=secret

# ---------------------------------------------------------
# Chaves de API opcionais (funcionalidades enriquecidas)
# ---------------------------------------------------------

# Dog API — raças e imagens de cães
# Regista em https://thedogapi.com/ e obtém a chave gratuita
DOG_API_KEY=

# Cat API — raças e imagens de gatos
# Regista em https://thecatapi.com/ e obtém a chave gratuita
CAT_API_KEY=

# Pexels API — imagens de stock de alta qualidade
# Regista em https://www.pexels.com/api/ e obtém a chave gratuita
PEXELS_API_KEY=
```

> **Nota:** Sem as chaves de API opcionais, a aplicação funciona normalmente, mas algumas funcionalidades de pesquisa de raças e sugestão de imagens ficam desactivadas.

---

## 🔐 Credenciais de Teste

Após correr `php artisan migrate --seed`, estão disponíveis os seguintes utilizadores de teste:

| E-mail | Palavra-passe | Papel |
|--------|--------------|-------|
| `admin@pawsafe.com` | `password123` | Administrador |
| `editor@pawsafe.com` | `password123` | Editor |
| `user@pawsafe.com` | `password123` | Utilizador |

---

## 👥 Papéis e Permissões

| Papel | Capacidades |
|-------|------------|
| **Visitante** | Ver animais, notícias e informação pública |
| **Utilizador (user)** | Visitante + submeter pedidos de adopção, gerir o próprio perfil |
| **Editor** | Utilizador + criar/editar/publicar notícias no painel de administração |
| **Administrador (admin)** | Editor + gerir utilizadores, animais, categorias e pedidos de adopção; acesso total ao painel |

---

## 🌐 Suporte Multilíngue (Funcionalidade Adicional)

A plataforma suporta três idiomas, com comutador visual acessível no cabeçalho público e no menu administrativo:
- 🇵🇹 **Português (`pt`)**
- 🇬🇧 **Inglês (`en`)**
- 🇭🇷 **Croata (`hr`)**

A preferência é persistida na sessão do utilizador através do middleware `SetLocale`, com rota dedicada `/locale/{locale}`.

---

## 🔐 Autenticação de Dois Fatores — 2FA (Funcionalidade Adicional)

Em conformidade com as funcionalidades opcionais avançadas sugeridas no enunciado, o PawSafe inclui suporte nativo a **Autenticação de Dois Fatores (2FA)** baseada no padrão aberto **TOTP (RFC 6238)**:
- Compatível com **Google Authenticator**, **Microsoft Authenticator**, **Authy** e outras apps TOTP.
- Leitura de **Código QR** ou introdução manual de chave secreta Base32.
- **Códigos de Recuperação de Emergência** de uso único (8 códigos gerados na ativação).
- Desafio intercalado no login (`/two-factor-challenge`) com suporte para código temporário de 6 dígitos ou código de recuperação.
- Gestão completa através da área de perfil (`/profile`), permitindo ativação, desativação protegida por password e regeneração de códigos.

---

## 🗂️ Estrutura de Documentação

| Documento | Descrição |
|-----------|-----------|
| [`docs/er-diagram.svg`](docs/er-diagram.svg) | Diagrama Entidade-Relação da base de dados |
| [`docs/API.md`](docs/API.md) | Documentação completa da API REST |
| [`docs/CONTRIBUTIONS.md`](docs/CONTRIBUTIONS.md) | Divisão de trabalho entre membros da equipa |

---

## 🧪 Correr os Testes

```bash
php artisan test
```

Para correr com cobertura de código (requer Xdebug ou PCOV):

```bash
php artisan test --coverage
```

---

## 🤖 Uso de Ferramentas de IA

Durante o desenvolvimento do PawSafe, foram utilizadas ferramentas de inteligência artificial (nomeadamente assistentes de código como o Google Gemini / Antigravity) para apoio no:

- Planeamento da arquitectura e estrutura do projecto
- Geração de scaffolding de modelos, controllers e rotas
- Debugging de erros e comportamentos inesperados
- Revisão e melhoria de código

Todo o código foi revisto, compreendido e validado pelos membros da equipa.

---

## 👨‍💻 Divisão de Trabalho

Ver [`docs/CONTRIBUTIONS.md`](docs/CONTRIBUTIONS.md)

---

## 📄 Licença

Este projecto foi desenvolvido no âmbito de um trabalho académico (Erasmus NTP). Não destinado a uso comercial.
