# Arquitetura Geral — AltoGestor

Este documento descreve a arquitetura macro do sistema AltoGestor, seguindo boas práticas para SaaS multi-tenant moderno.

## Objetivos

- Criar um sistema contábil multi-tenant altamente escalável.
- Permitir que um escritório (tenant) gerencie várias empresas (clientes).
- Garantir segurança, isolamento lógico, performance e facilidade de manutenção.
- Prover uma API consistente e versionada.
- Adotar padrões profissionais desde o início.

## Stack

- **Backend:** Laravel 12, PHP 8.4
- **Banco:** PostgreSQL
- **Cache/Fila:** Redis
- **Storage:** MinIO (S3)
- **Frontend:** Blade + Livewire / Tailwind / Vite
- **Observabilidade:** Sentry/Bugsnag, logs estruturados
- **CI/CD:** Lint → Static Analysis → Tests → Build → Deploy

## Decisões Centrais

1. Multi-tenancy via `tenant_id` em todas as tabelas + Global Scope.
2. Identificadores internos bigint autoincrement + `public_id` UUID para API.
3. Usuário pode ter acesso a múltiplas empresas do mesmo tenant.
4. RBAC via Spatie Permission, escopado por tenant.
5. Jobs com idempotência e contexto de tenant obrigatório.
6. API v1 100% padronizada (JSON estruturado).
7. Domínios organizados em módulos isolados.
8. Auditoria e logs com contexto: `tenant_id`, `empresa_id`, `user_id`, `request_id`.


## Seed Inicial de Desenvolvimento

Para facilitar o desenvolvimento local, o sistema oferece um seed padrão:

- **Tenant Demo:** `AltoGestor Demo Contabilidade`
- **Empresas Demo:**
  - `Empresa Demo 1` — CNPJ fictício
  - `Empresa Demo 2` — CNPJ fictício
- **Usuário Sócio Admin:**
  - Nome: `Sócio Admin`
  - Email: `socio@demo.local`
  - Senha: `password` (apenas em desenvolvimento)
  - Role: `admin` no tenant demo
  - Acesso às duas empresas demo como `socio_admin`

### Execução

```bash
php artisan migrate --seed
# ou, especificamente:
php artisan db:seed --class=DevSeed


Em produção, o seed de desenvolvimento não deve ser executado.


---

## 3) UUIDv7 – lib / estratégia

### Decisão sugerida

Como você está em Laravel moderno, eu iria de:

- **Symfony UID (`symfony/uid`)** – já integrado com Laravel 12.
- Helper centralizado para gerar `public_id`:

```php
use Symfony\Component\Uid\UuidV7;

UuidV7::generate();


Ou encapsular isso numa trait/helpers de modelo.