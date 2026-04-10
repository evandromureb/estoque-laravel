# Controle de Estoque (estoque-laravel)

Aplicação web para **gestão de inventário**: produtos, categorias, armazéns (warehouses), posicionamento físico por localização (corredor/prateleira), quantidades por armazém, imagens, estoque mínimo e **QR codes** por produto. Inclui **painel autenticado**, **API REST v1** protegida com **Laravel Sanctum** e documentação OpenAPI interativa (Scramble).

---

## Funcionalidades principais

- **Autenticação**: registro, login, verificação de e-mail, recuperação de senha (Laravel Breeze).
- **Painel** (`/dashboard`): visão geral após login.
- **CRUD** (área autenticada): usuários, armazéns, categorias, produtos; associação de produtos a locais físicos com quantidades.
- **Imagens de produto**: upload e remoção vinculados ao produto.
- **Tokens de API**: criação e revogação de token Sanctum por usuário (integrações / Postman).
- **QR codes**: geração armazenada em disco público; comando Artisan para (re)gerar em lote.
- **API REST** (`/api/v1/*`): espelha recursos principais; autenticação via sessão (SPA no mesmo domínio) ou **Bearer token**.
- **Documentação da API**: interface em `/docs/api` e especificação JSON em `/docs/api.json` (ambiente de desenvolvimento; acesso restrito por middleware do Scramble).

---

## Stack tecnológica

| Camada | Tecnologia |
|--------|------------|
| Backend | **PHP** `^8.3`, **Laravel** `^13` |
| HTTP / MVC | Laravel Framework |
| API & SPA | **Laravel Sanctum** |
| QR Code | **simplesoftwareio/simple-qrcode** |
| Frontend (painel) | **Blade**, **Vite** `^8`, **Tailwind CSS** `^3`, **Alpine.js** `^3`, **Axios** |
| Testes | **Pest** `^4` + plugin Laravel |
| Qualidade (dev) | **Laravel Pint**, **Larastan**, **Rector** (ecossistema Laravel) |
| Documentação API (dev) | **dedoc/scramble** |
| Localização | **lucascudo/laravel-pt-br-localization** (pt_BR) |

**Infraestrutura típica** (conforme `.env.example` e **Laravel Sail**):

- **MySQL** 8.4 (banco principal)
- **Filas**: driver `database` (tabela `jobs`; em dev o script `composer dev` também sobe `queue:listen`)
- **Sessão e cache**: driver `database` (tabelas dedicadas)
- **E-mail (dev)**: **Mailpit** (SMTP na porta configurada)
- **RabbitMQ** (opcional no `compose.yaml`; filas da app usam `database` por padrão)

---

## Pré-requisitos

- **PHP** 8.3 ou superior, com extensões usuais do Laravel (openssl, pdo, mbstring, tokenizer, xml, ctype, json, fileinfo, etc.)
- **Composer** 2.x
- **Node.js** e **npm** (para Vite / assets)
- **MySQL** 8.x (ou outro SGBD compatível, ajustando `.env`)

Opcional:

- **Docker** + plugin Compose, para subir o ambiente via **Laravel Sail** (`compose.yaml`).

---

## Instalação (passo a passo)

### 1. Clonar o repositório

```bash
git clone <url-do-repositorio> estoque-laravel
cd estoque-laravel
```

### 2. Dependências PHP

```bash
composer install
```

### 3. Ambiente e chave da aplicação

Copie o arquivo de exemplo e gere a chave `APP_KEY`:

```bash
cp .env.example .env
php artisan key:generate
```

Edite o `.env` conforme seu ambiente:

- **`APP_URL`**: URL base (ex.: `http://localhost:8000` se usar `php artisan serve`).
- **Banco de dados**: `DB_CONNECTION`, `DB_HOST`, `DB_PORT`, `DB_DATABASE`, `DB_USERNAME`, `DB_PASSWORD`.  
  - No `.env.example`, `DB_HOST=mysql` corresponde ao **nome do serviço Docker (Sail)**.  
  - Em PHP instalado localmente, use em geral `DB_HOST=127.0.0.1`.

### 4. Dependências JavaScript e build dos assets

```bash
npm install
npm run build
```

Para desenvolvimento com hot reload do Vite:

```bash
npm run dev
```

### 5. Banco de dados

Crie o banco vazio no MySQL (nome igual ao de `DB_DATABASE` no `.env`) e execute:

```bash
php artisan migrate
```

Opcional — dados de demonstração (admin, usuários, armazéns, categorias, produtos, locais, imagens, QR codes):

```bash
php artisan db:seed
```

Credencial padrão do seed (altere em produção):

- E-mail: `admin@example.com`
- Senha: `password`

### 6. Link do storage público

Necessário para servir uploads (imagens de produto, QR codes em `/storage`):

```bash
php artisan storage:link
```

### 7. Subir a aplicação

Em um terminal:

```bash
php artisan serve
```

Em outro (se estiver desenvolvendo o front):

```bash
npm run dev
```

Se usar **filas em background** com `QUEUE_CONNECTION=database`:

```bash
php artisan queue:work
```

---

## Atalho: script Composer `setup`

O projeto inclui um script que automatiza parte da configuração inicial (útil em máquina nova):

```bash
composer run setup
```

Isso executa, em sequência: `composer install`, cópia de `.env.example` → `.env` se não existir, `php artisan key:generate`, `php artisan migrate --force`, `npm install --ignore-scripts` e `npm run build`.

> Ajuste credenciais de banco no `.env` **antes** se o migrate precisar de um MySQL já acessível.

---

## Desenvolvimento com Laravel Sail (Docker)

O arquivo `compose.yaml` define serviços **laravel.test** (PHP 8.5), **mysql**, **mailpit** e **rabbitmq**.

Instale o Sail (se ainda não estiver em `vendor`) e publique os binários, ou use o fluxo padrão da documentação do Laravel Sail. Com Sail disponível:

```bash
./vendor/bin/sail up -d
./vendor/bin/sail artisan migrate
./vendor/bin/sail artisan storage:link
```

Com `.env` alinhado ao Sail (`DB_HOST=mysql`, etc.), o app fica em geral em `http://localhost` (porta configurável via `APP_PORT`).

Ambiente de desenvolvimento “tudo em um” (servidor HTTP, fila, logs Pail e Vite), via Composer:

```bash
composer run dev
```

(Requer dependências já instaladas e `.env` configurado.)

---

## Comandos Artisan úteis

| Comando | Descrição |
|---------|-----------|
| `php artisan inventory:generate-product-qr-codes` | Gera/atualiza QR codes (SVG) dos produtos no disco `public` e campo `qr_code_path`. Opções: `--chunk=100`, `--only-missing` |
| `php artisan migrate` / `migrate:fresh --seed` | Migrações; opcionalmente recria BD com seed |
| `php artisan storage:link` | Symlink `public/storage` → `storage/app/public` |

---

## Testes

Os testes usam **SQLite em memória** (configurado em `phpunit.xml`).

```bash
composer run test
```

Equivalente: `php artisan test`.

---

## API e documentação

- **Prefixo**: `/api/v1/`
- **Autenticação**: Sanctum — sessão (requisições com cookie/CSRF no mesmo site) ou **Authorization: Bearer &lt;token&gt;** (token criado no painel para o usuário).
- **Documentação interativa**: [`/docs/api`](http://localhost:8000/docs/api) (ajuste host/porta).
- **OpenAPI JSON**: `/docs/api.json`

Em produção, revise regras de acesso da documentação (`RestrictedDocsAccess` no Scramble).

---

## Variáveis de ambiente (resumo)

Principais chaves em `.env.example`:

- **App**: `APP_NAME`, `APP_ENV`, `APP_KEY`, `APP_DEBUG`, `APP_URL`, `APP_LOCALE` (`pt_BR`), `APP_TIMEZONE` (`America/Sao_Paulo`)
- **Banco**: `DB_*`
- **Sessão**: `SESSION_DRIVER=database`
- **Filas**: `QUEUE_CONNECTION=database`
- **Cache**: `CACHE_STORE=database`
- **Mail**: `MAIL_*` (Mailpit em ambiente containerizado típico)
- **Vite**: `VITE_APP_NAME`

---

## Estrutura de rotas (referência rápida)

- **Web**: `routes/web.php` — welcome, dashboard, perfil, recursos autenticados (usuários, armazéns, categorias, produtos, locais, imagens).
- **API**: `routes/api.php` — grupo `v1` com `apiResource` equivalente, middleware `auth:sanctum`.
- **Auth**: `routes/auth.php` — fluxo Breeze.

Health check Laravel: `GET /up`.

---

## Licença

O esqueleto base segue a licença **MIT** do Laravel; mantenha a licença do projeto conforme o repositório oficial.
