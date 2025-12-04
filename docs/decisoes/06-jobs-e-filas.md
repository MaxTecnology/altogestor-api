
---

# **06-jobs-e-filas.md**

```md
# Jobs, Filas e Scheduler

## Filas

Filas separadas:
- default
- notifications
- integrations
- heavy-reports

## Horizon Pools

Pools distintas por fila:
- workers diferentes
- concorrência customizada
- TTL para jobs pesados

## Idempotência de Jobs

Chave padrão:

tenant_id + empresa_id + recurso + competencia


## Contexto de Tenant
Todo job deve conter:

```php
public int $tenantId;


Middleware TenantJobMiddleware aplica:

Tenant::setCurrent($this->tenantId)

adiciona metadata aos logs

Scheduler

Usar Laravel schedule:work (Laravel 12).
Tarefas recorrentes:

gerar obrigações por competência

enviar notificações pendentes

varreduras e limpezas

## Filas Oficiais

O sistema utiliza as seguintes filas nomeadas:

- `default` — fila padrão para jobs de negócio em geral.
- `notifications` — envio de e-mails, mensagens, notificações externas.
- `integrations` — chamadas a APIs externas (SEFAZ, parceiros, etc.).
- `heavy-reports` — geração de relatórios pesados, exports, PDFs em lote.

### Uso em Jobs

Todo job deve declarar explicitamente em qual fila será enfileirado, por exemplo:

```php
EnviarNotificacaoJob::dispatch($dados)
    ->onQueue('notifications');

SincronizarComSefazJob::dispatch($dados)
    ->onQueue('integrations');

GerarRelatorioMensalJob::dispatch($dados)
    ->onQueue('heavy-reports');

Configuração no Horizon

No config/horizon.php, as filas serão agrupadas em pools com diferentes números de workers, de acordo com prioridade e consumo esperado:

Pool default → fila default

Pool notifications → fila notifications

Pool integrations → fila integrations

Pool heavy-reports → fila heavy-reports

