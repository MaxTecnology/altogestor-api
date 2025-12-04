# Checklist por Fases — AltoGestor

Fluxo: ao final de cada fase, você valida manualmente e eu preparo/rodo testes de código (unit/feature). Após validação, marcamos a fase como concluída.

## Fase 0 – Infra básica
- [x] Docker ativo, APP_KEY ok, migrations base rodando.
- [ ] Scripts locais para Pint/PHPStan/PHPUnit configurados.
- [x] Seed de dev disponível (tenant/empresas/usuário demo).

## Fase 1 – Multi-tenant + núcleo
- [x] TenantManager, trait HasTenant, middleware de request e jobs aplicando escopo de tenant.
- [x] Prefixo de cache/fila por tenant.
- [x] Migrations núcleo: tenants, empresas, usuários, vínculo empresa_user, auditoria (estrutura).

## Fase 2 – Auth/RBAC
- [x] Sanctum + Breeze API configurados.
- [x] Spatie Permission com `tenant_id`.
- [x] Endpoints `/me`, `/companies` e `/switch-empresa` + validação de tenant.
- [x] Seeds de roles/permissões e usuário sócio admin.

## Fase 3 – Domínio inicial (API + UI Livewire)
- [x] Documentos: migrations/models para modelos, solicitações, documentos, histórico + endpoints (modelos, solicitações, upload).
- [x] Obrigações/Guias: migrations/models (tipos, configs por empresa, guias, comprovantes, histórico).
- [ ] Workflow/Tarefas: modelos, tarefas, histórico + telas (lista/atualização de status).
- [ ] Forms/Pedidos: modelos, campos, pedidos, respostas, anexos + telas de abertura/acompanhar.
- [ ] Notificações: templates, fila, logs + UI de envio/listagem.

## Fase 4 – Observabilidade/Segurança
- [ ] Logs estruturados com tenant/empresa/user/request.
- [ ] Auditoria (`auditoria_eventos`) com eventos por módulo.
- [ ] Sentry/Bugsnag configurado; rate limiting ativo.
- [ ] Backups e proteção de Horizon/Mailpit/MinIO em prod.

## Fase 5 – Frontend + UX núcleo (refino)
- [ ] Layout base Blade/Livewire refinado (painel/portal) com troca de empresa.
- [ ] UX/estados/feedbacks e componentes do design system consolidados.
- [ ] Policies refletidas na UI (ocultação/disable por permissão).

## Fase 6 – CI/CD e testes
- [ ] Pipeline: Pint → PHPStan → PHPUnit → build.
- [ ] Smoke tests/contratos básicos de API.
- [ ] Scripts de deploy com migrations seguras.
