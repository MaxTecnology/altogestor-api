# Ambientes & Deploy – Guia Oficial de Operação

Este documento define como o sistema deve ser instalado, configurado, atualizado e mantido nos diferentes ambientes:

- **DEV (desenvolvimento local)**
- **HOMOLOGAÇÃO**
- **PRODUÇÃO**

Inclui também:
- padrão de deploy,
- versionamento,
- variáveis de ambiente,
- serviços auxiliares,
- migrations,
- jobs/scheduler,
- filas,
- healthcheck.

Este guia é essencial para equipes de DevOps, backend, infraestrutura e para automação do Codex.

---

# 1. Arquitetura Geral de Ambientes

## 1.1. DEV (local)
Ambiente para desenvolvimento individual.

- Utiliza Docker ou Docker Compose
- Banco local ou container (MySQL/PostgreSQL)
- Storage local (`storage/app`)
- Email fake (Mailpit)
- Workers rodando no container
- Migrations rodadas automaticamente

## 1.2. HOMOLOGAÇÃO
Ambiente de testes internos e validação do cliente.

- Banco isolado  
- Storage isolado  
- Integrações em modo sandbox  
- Logs detalhados  
- Workers habilitados  
- Scheduler ativado  
- Deploy automatizado via CI/CD

## 1.3. PRODUÇÃO
Ambiente final, alta segurança e performance.

- Banco independente (externo)  
- Storage S3/Minio  
- Cache Redis clusterizado  
- Workers distribuídos  
- Healthchecks  
- Monitoramento ativo  
- Backups automáticos  
- SSL obrigatório  

---

# 2. Serviços Necessários

Todos os ambientes utilizam:

| Serviço | Função |
|--------|--------|
| **API Backend** | núcleo do sistema |
| **MySQL/PostgreSQL** | banco relacional |
| **Redis** | cache + fila |
| **Queue Worker** | processamento assíncrono |
| **Scheduler** | execução de tarefas recorrentes |
| **Storage** | armazenamento de arquivos |
| **Mail Service** | envio de notificações |
| **WhatsApp/E-mail API** | notificações externas (quando ativado) |

---

# 3. Variáveis de Ambiente (Env)

Exemplo mínimo sugerido:

APP_ENV=production
APP_KEY=

DB_HOST=
DB_DATABASE=
DB_USERNAME=
DB_PASSWORD=

REDIS_HOST=
REDIS_PASSWORD=

QUEUE_CONNECTION=redis
CACHE_DRIVER=redis

FILESYSTEM_DISK=s3

S3_BUCKET=
S3_ACCESS_KEY=
S3_SECRET_KEY=
S3_REGION=

MAIL_HOST=
MAIL_PORT=

WHATSAPP_API_KEY=

LOG_CHANNEL=stack


### Boas práticas
- Nunca commitar `.env`
- Usar variáveis específicas por ambiente
- Rotacionar chaves com frequência
- Usar secrets do GitHub/GitLab para CI/CD

---

# 4. Deploy – Fluxo Oficial

## 4.1. Versionamento (git)

### Branches
- `main` → produção
- `dev` → desenvolvimento
- `homolog` → homologação

### Pull Requests exigidos para:
- migrations de banco
- alterações em jobs
- integrações externas
- modelos de documento ou obrigação

---

## 4.2. Deploy DEV
Via docker-compose:



docker-compose up -d


## 4.3. Deploy HOMOLOGAÇÃO
Via CI/CD (GitHub Actions, GitLab ou outro).

Pipeline sugerida:

1. Rodar testes
2. Rodar lint
3. Build de container
4. Deploy em servidor homolog
5. Rodar migrations
6. Ativar workers
7. Reiniciar scheduler

## 4.4. Deploy PRODUÇÃO
Pipeline igual homolog, mas com:

- backups antes do deploy
- migrações revisadas/manual gate
- monitoramento ativo
- smoke test pós-deploy

---

# 5. Migrations (DB)

## 5.1. Fluxo

**Sempre**
- criar migrations versionadas
- rodar em homolog antes de produção
- nunca alterar migrations já aplicadas
- usar `ALTER TABLE` incrementalmente

---

# 6. Filas (Queue Workers)

## 6.1. Exemplos de Jobs
- Processar upload de XML
- Validar documentos
- Enviar notificações
- Gerar solicitações mensais
- Processar tarefas automáticas
- Enviar guias
- Processar pedidos com anexos

## 6.2. Boas práticas
- Sempre com timeout → 120 segundos
- Retries → 3 tentativas
- Fila separada por criticidade:
  - `default`
  - `notificacoes`
  - `guias`
  - `documentos`

---

# 7. Scheduler (Tarefas Automáticas)

Executado por:



php artisan schedule:work


### Cron tasks principais:

1. Verificar documentos pendentes
2. Gerar solicitações mensais
3. Validar tarefas a vencer
4. Gerar guias (dia configurado)
5. Enviar alertas (dias úteis antes)
6. Limpeza de logs antigos

---

# 8. Storage

## Ambientes

| Ambiente | Storage |
|----------|----------|
| DEV | local (disk local) |
| HOMOLOGAÇÃO | S3 ou Minio interno |
| PRODUÇÃO | S3 com criptografia |

### Regras gerais
- arquivos nunca ficam públicos  
- URLs são assinadas e expiram  
- remoção sempre auditada  

---

# 9. Healthcheck

## 9.1. Endpoint sugerido


GET /api/health


Resposta:

```json
{
  "status": "ok",
  "database": "ok",
  "redis": "ok",
  "queue": "ok",
  "version": "1.0.3"
}

9.2. Infra

Docker HEALTHCHECK

Nginx upstream health

Kubernetes Liveness/Readiness (futuro)

10. Logs & Monitoramento

Integrar com:

Grafana

Loki

Sentry

Prometheus (futuro)

Monitoração crítica:

falha de jobs

atraso na fila

falha de geração de guia

documentos atrasados

API com alta latência

storage indisponível

11. Backup & Recovery (Resumo)

(Detalhes completos em backup-recovery.md)

Backup diário:

banco

storage

logs essenciais

Retenção:

7 anos (obrigação contábil mínima)

Restore:

por data

por empresa (documentos)

por módulo (guias, pedidos, etc.)

12. Checklist de Deploy
Antes de deploy:

migrations revisadas

coverage de testes adequada

validação em homolog

backup completo

Durante:

CI/CD automatizado

pausa temporária em workers críticos

aplicar migrations

reiniciar serviços

Depois:

smoke test

reativar workers

verificar filas

checar storage

monitoramento ativo 30m

13. Conclusão

Este documento descreve de forma completa todo o processo de:

configuração,

deploy,

versionamento,

infraestrutura,

workers,

storage,

e operação da plataforma.

Ele é a referência oficial para desenvolvedores, DevOps e automações via IA.