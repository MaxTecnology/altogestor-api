# API v1 — Padrões Oficiais

Base: `/api/v1`

## Formato Geral de Resposta

```json
{
  "data": {...},
  "meta": {...},
  "links": {...}
}

Erros (RFC 7807)
{
  "errors": [
    {
      "status": 400,
      "code": "validation_error",
      "detail": "Campo X é obrigatório",
      "meta": {...}
    }
  ]
}

Paginação

Padrão Laravel (links + meta).

Autenticação

Header:

Authorization: Bearer {token}

Multi-Tenant Header (integrações)

Opcional:

X-Tenant-ID: {tenant_public_id}

Versionamento

Toda nova mudança breaking vai para /v2.

Idempotência

Endpoints críticos recebem:

Idempotency-Key: {chave}


Usada para:

guias

comprovantes

tarefas geradas por competência

Rate Limit

Variáveis .env:

API_RATE_LIMIT

API_BURST_LIMIT