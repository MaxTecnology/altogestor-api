# AltoGestor – Environment Setup (Laravel 12 + Docker)

Este documento descreve todo o ambiente de desenvolvimento do projeto **AltoGestor**, baseado em **Laravel 12**, rodando em uma infraestrutura Docker completa e profissional, projetada para suportar um SaaS moderno, escalável e seguro.

---

# 🚀 1. Tecnologias Utilizadas

### **Backend**
- Laravel **12**
- PHP **8.3** (FPM)

### **Infra (Docker)**
- **Nginx** — Servidor HTTP
- **PostgreSQL 16** — Banco principal
- **Redis 7** — Cache, Sessão, Fila
- **Mailpit** — Simulador SMTP
- **MinIO (S3-like)** — Armazenamento de arquivos
- **Queue Worker** — Execução de jobs
- **Scheduler Worker** — Execução contínua do `schedule:work`
- **Horizon** — Dashboard de filas do Laravel

---

# 📂 2. Estrutura de Pastas

altogestor-api/
│
├── docker/
│ ├── app.Dockerfile
│ ├── php.ini
│ └── nginx/
│ └── default.conf
│
├── docker-compose.yml
├── .env
└── docs/
└── ENVIRONMENT.md


---

# 🧱 3. Docker Services

### **app**
Container PHP-FPM com Laravel 12.  
Usado para rotas HTTP, comandos Artisan e migrations.

### **queue**
Executa:


php artisan queue:work

Responsável por jobs pesados, integrações e tarefas assíncronas.

### **scheduler**
Executa:


php artisan schedule:work

Garante que automações rodem continuamente.

### **nginx**
Serviço HTTP servindo `public/`.

Acessível em:  
**http://localhost:8080**

### **postgres**
Banco principal da aplicação.

Acesso local:
- Host: `localhost`
- Porta: `5432`
- Database: `altogestor`
- Username: `altogestor`
- Password: `altogestor`

### **redis**
Usado para:
- Cache
- Sessões
- Filas
- Rate limiting
- Broadcast drivers (futuro)

### **mailpit**
UI em:  
**http://localhost:8025**

SMTP fake para desenvolvimento.

### **minio**
Servidor S3 compatível.

Console:  
**http://localhost:9001**

Credenciais padrão:
- user: `minioadmin`
- pass: `minioadmin123`

---

# ⚙️ 4. Configurações Importantes do `.env`

```env
APP_ENV=local
APP_URL=http://localhost:8080

DB_CONNECTION=pgsql
DB_HOST=postgres
DB_PORT=5432
DB_DATABASE=altogestor
DB_USERNAME=altogestor
DB_PASSWORD=altogestor

CACHE_DRIVER=redis
QUEUE_CONNECTION=redis
SESSION_DRIVER=redis
REDIS_HOST=redis

MAIL_HOST=mailpit
MAIL_PORT=1025

AWS_ACCESS_KEY_ID=minioadmin
AWS_SECRET_ACCESS_KEY=minioadmin123
AWS_BUCKET=altogestor
AWS_ENDPOINT=http://minio:9000
AWS_USE_PATH_STYLE_ENDPOINT=true

🏗️ 5. Subindo o Ambiente
1. Build e start
docker compose up -d --build

2. Acessar container
docker compose exec app bash

3. Instalar dependências e gerar chave
composer install
php artisan key:generate
php artisan migrate
php artisan storage:link

🧪 6. Comandos Úteis
Entrar no container
docker compose exec app bash

Rodar migrations
php artisan migrate

Rodar seeds
php artisan db:seed

Ver status da fila
php artisan horizon

Ver logs
docker compose logs -f app
docker compose logs -f queue
docker compose logs -f scheduler

📡 7. Endpoints de Serviços no Dev
Serviço	URL / Porta
Aplicação	http://localhost:8080

Mailpit UI	http://localhost:8025

MinIO Console	http://localhost:9001

Redis (CLI)	localhost:6379
Postgres	localhost:5432
🔐 8. Segurança (para produção)

Usar APP_KEY único por ambiente

Habilitar HTTPS no Nginx

Revisar permissões de buckets MinIO

Mover Redis e Postgres para servidores dedicados

Habilitar autenticação no Horizon

🧭 9. Fluxo Geral do Ambiente

Nginx recebe a requisição → envia para PHP-FPM (app)

Laravel processa rota / middleware

Dados trafegam via:

Postgres para storage estruturado

Redis para:

Cache

Sessão

Filas

Jobs vão para o Worker (queue)

Automações contínuas → scheduler

Arquivos são enviados/recebidos via MinIO

E-mails são enviados para Mailpit

🎯 10. Pronto Para Desenvolvimento

Com esse ambiente:

Codex já entende a stack

Filas, storage, banco e cache estão prontos

Você já tem base para módulos:

Financeiro

Obrigações Contábeis

Multi-tenant

Logs e auditoria

APIs internas e externas (ASAAS, Pix, SEFAZ, etc.)

Arquitetura está preparada para escalar