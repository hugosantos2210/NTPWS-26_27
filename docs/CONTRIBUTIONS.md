# 👥 Divisão de Trabalho e Contribuições da Equipa — PawSafe

No âmbito do projeto académico de Desenvolvimento Web (NTPWS-26_27), o trabalho foi distribuído de forma equilibrada entre os dois membros da equipa, cobrindo tanto o desenvolvimento backend como frontend, base de dados, segurança, APIs e testes.

---

## Membros da Equipa

| Membro | Função Principal | Área de Foco |
|--------|------------------|--------------|
| **Estudante 1** | Backend & Arquitetura de Dados | Modelos Eloquent, Migrações, RBAC, API REST própria, JSON Schema, Testes Automatizados |
| **Estudante 2** | Frontend & Integrações Externas | Vistas Blade responsivas, CSS, Quill.js, Integração TheDogAPI / TheCatAPI / Pexels, Cache |

---

## Divisão Detalhada de Tarefas

### 1. Base de Dados e Arquitetura (Estudante 1 & Estudante 2)
- Modelação do diagrama Entidade-Relação (ERD) cobrindo utilizadores, papéis, notícias, categorias, animais, pedidos de adoção e cache de raças.
- Criação das migrações e definição de chaves estrangeiras com restrições e regras de eliminação (`restrictOnDelete`, `cascadeOnDelete`).
- Implementação do `DatabaseSeeder` com dados de demonstração cobrindo todos os papéis e estados do ciclo de vida das notícias e animais.

### 2. Autenticação, Controlo de Acesso e Segurança (Estudante 1)
- Implementação do sistema de autenticação próprio (Registo, Login, Logout, Alteração de Password e Perfil).
- Implementação de controlo de acessos baseado em papéis (RBAC) com os papéis `user`, `editor` e `admin`.
- Middlewares `CheckRole` e `CheckActiveUser` garantindo que contas desativadas perdem imediatamente a sessão ativa na base de dados.
- Proteção contra CSRF, hashing de palavras-passe com Bcrypt/Argon2, prevenção de Session Fixation com regeneração de ID de sessão após login.

### 3. Gestão de Notícias e Painel de Administração (Estudante 1 & Estudante 2)
- CRUD completo de notícias no painel administrativo com distinção de privilégios (editores gerem os seus artigos, administradores gerem tudo).
- Gestão de categorias com salvaguarda contra eliminação acidental de categorias com artigos associados.
- Integração do editor de texto formatado **Quill.js** e sanitização rigorosa de HTML no servidor com `strip_tags`.
- Gestão do ciclo de vida editorial: Rascunho (`draft`), Publicado (`published`) e Retirado (`withdrawn`).

### 4. Interface Pública e Experiência do Utilizador (Estudante 2)
- Desenvolvimento das páginas públicas (Home com estatísticas em tempo real, catálogo de Notícias com filtros e pesquisa, detalhe de artigo, página "Sobre o Projeto").
- Módulo específico do tema: catálogo de Animais para adoção e submissão de pedidos de adoção.
- Interface 100% responsiva para desktop e dispositivos móveis com feedback claro de validação e estados vazios.
- Widget dinâmico de notícias que consome diretamente a API REST interna (`/api/v1/news`) via JavaScript assíncrono (`fetch`).

### 5. API REST Própria e Integrações Externas (Estudante 1 & Estudante 2)
- **API Própria (`/api/v1`)**: Endpoints de listagem, pesquisa, paginação, consulta, criação, atualização e eliminação, com respostas HTTP semânticas (200, 201, 204 sem corpo, 400, 401, 403, 404). Validação com JSON Schema (`public/schemas/news-schema.json`).
- **APIs Externas**: Camada de serviços (`BreedService`, `NewsImageService`) para integração com TheDogAPI, TheCatAPI e Pexels, com sistema de cache resiliente a falhas e timeouts configurados (10s).

### 6. Testes Automatizados e Documentação (Estudante 1 & Estudante 2)
- Elaboração de testes de Feature para rotas públicas, fluxos de autenticação, permissões RBAC e endpoints da API REST.
- Redação da documentação da API (`docs/API.md`), diagrama ER (`docs/er-diagram.svg`) e instruções de instalação no `README.md`.

