# Checklist de Desenvolvimento — AltoGestor

Referências principais: `docs/ENVIRONMENT.md`, `docs/c4/README.md`, `docs/domain/*`, `docs/security/*`, `docs/api/*`, `docs/integracoes/README.md`, `docs/roadmap.md`.

## 1. Ambiente e tooling
- [ ] Clonar, copiar `.env.example` → `.env`, ajustar `APP_URL`, timezone, gerar `APP_KEY`.
- [ ] Subir Docker (app/queue/scheduler/nginx/postgres/redis/mailpit/minio) conforme `docs/ENVIRONMENT.md`.
- [ ] Instalar deps (`composer install`, `npm install`), `php artisan migrate`, `php artisan storage:link`.
- [ ] Confirmar serviços expostos: 8080 (HTTP), 5432 (DB), 6379 (Redis), 8025 (Mailpit), 9001 (MinIO).
- [ ] Ativar Pint e PHPStan/Larastan no projeto; adicionar scripts de lint/test (`composer test`, `pint`).

## 2. Arquitetura e multi-tenant
- [ ] Revisar C4 (context/containers/componentes) em `docs/c4/README.md`.
- [ ] Decidir estratégia multi-tenant (coluna `tenant_id` vs schema) e aplicar scoping global.
- [ ] Definir naming de bancos e collation/timezone; padronizar `id` UUID ou bigint.
- [ ] Planejar estratégia de cache/fila/sessão em Redis por tenant (namespaces/prefixos).

## 3. Autenticação, autorização e papéis
- [ ] Escolher stack de auth (Sanctum + Breeze/Jetstream) e tokens (painel, portal, service account).
- [ ] Implementar RBAC conforme `docs/security/permissoes.md` (policies/gates + roles por empresa).
- [ ] Proteger Horizon/Mailpit/MinIO em prod (auth/gate/reverse proxy).
- [ ] Endpoint `/me` retornando permissões por role/empresa para o front.

## 4. Banco de dados e seeds iniciais
- [ ] Derivar modelo das classes/estados em `docs/domain/*` e criar migrations base (empresas, usuarios, roles, vinculos, auditoria).
- [ ] Seeds: empresa demo, socio_admin, cliente_admin, dados ficticios para documentos/guias/tarefas/pedidos.
- [ ] Índices e constraints: unicidade por tenant, chaves estrangeiras com cascade adequado, histórico/auditoria com soft deletes onde fizer sentido.

## 5. Módulos de domínio (seguir `docs/api/*` e `docs/domain/*`)
- [ ] Empresas & Usuários: CRUD, vínculos cliente↔empresa, perfis escritório/cliente.
- [ ] Documentos: modelos, solicitações por competência, uploads, histórico de estado (state machine 04/05).
- [ ] Obrigações & Guias: tipos, configs por empresa, geração de guia, envio e comprovantes (06/07).
- [ ] Tarefas & Agenda: modelos, geração automática por obrigação, workflow e calendário (09).
- [ ] Pedidos & Formulários: modelos dinâmicos, campos, uploads e workflow (10).
- [ ] Notificações: templates, fila, logs estruturados, eventos de negócio; respeitar `docs/api/notificacoes*.md`.
- [ ] Financeiro/ASAAS (fase 3): contas a pagar/receber, cobranças/PIX, conciliação.

## 6. APIs e contratos
- [ ] Versionar `/api/v1`, aplicar rate limit (`API_RATE_LIMIT`), CORS/Sanctum configurados.
- [ ] Padrão de resposta/paginação/erros conforme `docs/api/README.md`; validation via FormRequest/DTO.
- [ ] Documentar OpenAPI/collection (Insomnia/Postman) para os módulos acima; expor webhooks planejados.

## 7. Integrações (ver `docs/integracoes/README.md`)
- [ ] OneDrive/SharePoint ingest: OAuth, monitoramento de pastas, logs `onedrive_*`.
- [ ] ASAAS: criação/consulta/cancelamento de cobranças, webhook `/webhook/asaas`, logs `asaas_*`.
- [ ] SMTP/WhatsApp: enviar via fila, templates, logs `email_*`/`whatsapp_*`.
- [ ] Retries/backoff e storage seguro de tokens/segredos.

## 8. Frontend
- [ ] Escolher stack (Blade+Livewire ou Inertia/SPA) alinhada ao auth.
- [ ] Definir design tokens/componentes base (Tailwind v4 + Vite); rotas protegidas por roles/permissoes do `/me`.
- [ ] Fluxos principais: login, troca de empresa, uploads, validação, agenda/calendário, envio/visualização de guias.

## 9. Observabilidade, segurança e LGPD
- [ ] Logs estruturados com correlação request/job; healthchecks para containers (app/queue/scheduler).
- [ ] Monitoramento de erros (Sentry/Bugsnag) e métricas (Prometheus/StatsD).
- [ ] Auditoria conforme `docs/security/auditoria.md`; mascarar PII; revisitar `docs/security/lgpd.md`.
- [ ] Backups e restore de Postgres/MinIO; política de retenção; rotação de segredos.

## 10. DevOps e entrega contínua
- [ ] CI: lint (Pint), static analysis (PHPStan), testes, build frontend; artefatos de cobertura.
- [ ] CD: build de imagem, migrations seguras (idempotentes/rollback), deploy zero downtime.
- [ ] Playbooks de incidentes e SLO/alertas; smoke tests pós-deploy.
