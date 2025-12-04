# Observabilidade & Segurança

## Logs Estruturados

Formato JSON com:
- tenant_id
- empresa_id
- user_id
- request_id
- job_id
- endpoint
- payload reduzido

## Auditoria

Tabela: auditoria_eventos  
Campos:
- tenant_id
- empresa_id
- user_id
- entidade
- entidade_id
- evento
- dados_antes
- dados_depois
- criado_em

## Sentry/Bugsnag

Captura:
- exceções
- contextos
- jobs
- user tagging
- tenant tagging

## Backups

- Postgres diário + retenção configurada
- MinIO snapshots/versões
- Script de restore testado

## Segurança

- Sanitização de logs (remover CPF/CNPJ sensível)
- Rate limiting por token
- Proteção de endpoints internos (Horizon, Mailpit)
- HTTPS obrigatório


## PII e Máscara de Dados

Dados sensíveis (PII) **não devem ser registrados em logs brutos**, em especial:

- CPF / CNPJ
- E-mails
- Telefones
- Endereços
- Nomes completos em contexto sensível

Em logs de aplicação:

- Preferir IDs internos (`id`) e `public_id`.
- Quando necessário registrar parte da informação, aplicar máscara, por exemplo:
  - CPF: `***.456.789-**`
  - CNPJ: `12.345.***.0001-**`
  - Email: `f***@dominio.com`

## Eventos Registrados em `auditoria_eventos`

A tabela `auditoria_eventos` registra mudanças relevantes por módulo:

### Documentos
- `document_created`
- `document_updated`
- `document_deleted`
- `document_uploaded`
- `document_signed`

### Obrigações e Guias
- `obrigacao_criada`
- `guia_gerada`
- `guia_atualizada`
- `guia_paga`
- `guia_cancelada`

### Workflow / Tarefas
- `tarefa_criada`
- `tarefa_atribuida`
- `tarefa_concluida`
- `tarefa_reaberta`

### Forms
- `form_model_created`
- `form_request_created`
- `form_response_submitted`

### Notificações
- `notification_sent`
- `notification_failed`
- `notification_read` (se houver tracking)

Cada registro de auditoria deve conter:

- `tenant_id`, `empresa_id`, `user_id`
- `entidade`, `entidade_id`
- `evento`
- `dados_antes` (JSON)
- `dados_depois` (JSON)
- `timestamp`
