# Biscoiteria

Sistema de gestão para biscoiterias, construído com Laravel + Filament (v5) + PostgreSQL,
seguindo a mesma estrutura inicial do projeto de loterias: multi-tenant (um banco por cliente),
cadastro de empresa/entidade/funcionário com endereços, telefones e e-mails polimórficos,
controle de acesso via grupos de usuário + menus dinâmicos, e log de atividades.

Cadastros e relatórios de negócio (produtos, vendas, estoque, etc.) serão implementados
futuramente — esta é a base inicial funcional.

## Estrutura

- `docker/` — Dockerfile do PHP-FPM, config do Nginx e do pool do FPM
- `docker-compose.yml` — app (PHP-FPM), nginx, postgres
- `src/` — aplicação Laravel

## Painéis

- **Central (`/admin`)** — gestão dos clientes (tenants). Guard `admin`, acesso restrito ao
  e-mail definido em `App\Models\User::canAccessPanel()`.
- **Tenant (`/app`)** — painel de cada cliente (subdomínio), com os cadastros básicos:
  Empresas, Funcionários, Usuários, Grupos de Usuários, Menus e Logs do Sistema.

Multi-tenancy por subdomínio (`stancl/tenancy`): `liana.localhost:8010/app`, por exemplo.
Adicione as entradas necessárias em `/etc/hosts` para cada subdomínio de tenant usado localmente.

## Subindo o ambiente

```bash
docker compose up -d --build
```

- App: http://localhost:8010
- Postgres: localhost:5434 (usuário `postgres` / senha `postgres`, ajustável em `.env`)

## Banco de dados

Mesmo padrão do projeto de loterias (`lotericas` + `lotericas_<cliente>`):

- Banco central: **`biscoiteria`** — tabelas gerais (tenants, domains, users do painel admin).
  É nele que a lista de clientes fica registrada.
- Banco por cliente: **`biscoiteria_<id-do-tenant>`** — tabelas específicas do sistema para
  aquele cliente. Ex.: cliente `liana` → banco `biscoiteria_liana`.

## Primeira configuração

```bash
# Migrations + seed do banco central (cria o usuário admin: koronaldo@gmail.com / 12345678)
docker compose exec app php artisan migrate --force
docker compose exec app php artisan db:seed --force

# Criar um tenant pelo painel /admin, ou via tinker:
docker compose exec app php artisan tinker --execute="App\Models\Tenant::create(['id' => 'liana', 'name' => 'Liana']);"
```

A criação do tenant já dispara automaticamente (via `TenancyServiceProvider`): criação do banco
`biscoiteria_liana`, migração das tabelas do tenant e seed (menus, grupo "Administrador", usuário
inicial `koronaldo@gmail.com` / `12345678`, e a hierarquia geográfica de regiões/estados/cidades
via API do IBGE — esse último passo faz várias chamadas HTTP e pode levar alguns minutos).

Comandos úteis de tenant:

```bash
docker compose exec app php artisan tenants:list
docker compose exec app php artisan tenants:migrate --tenants=liana
docker compose exec app php artisan tenants:seed --tenants=liana --force
```

## Permissões de storage (dev)

O `docker/php/fpm-pool.conf` roda o pool do PHP-FPM com UID/GID `1000` para casar com o dono dos
arquivos montados do host. Se trocar de usuário/host, ajuste esse UID/GID (ou rode
`chmod -R 775 src/storage src/bootstrap/cache`).

Evite rodar `docker compose exec app <comando>` sem `--user`, pois o container abre como `root` e
os arquivos criados (logs, cache) ficam com dono `root`, bloqueando escrita do PHP-FPM depois.
Use `docker compose exec --user 1000:1000 app <comando>` quando precisar rodar algo manualmente.
