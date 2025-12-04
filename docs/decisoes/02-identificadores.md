---

# Identificadores

## Chave primária interna
- `id` bigint autoincrement (PostgreSQL).
- Usado para joins internos e FKs.

## Identificador público (API)
- Campo `public_id` UUIDv7.
- Índice único: `unique(tenant_id, public_id)`.
- Motivos: evitar expor IDs internos, segurança e melhor localidade de índice com UUIDv7.

## Índices padrão
Toda tabela deve ter:

```sql
INDEX(tenant_id, id);
INDEX(tenant_id, public_id);
```

Tabelas grandes (logs, auditoria): considerar particionamento futuro por mês/tenant.

## Geração de UUIDv7
O campo `public_id` será do tipo **UUIDv7**, gerado na aplicação utilizando `symfony/uid` (compatível com Laravel 12).

Exemplo:

```php
use Symfony\Component\Uid\UuidV7;

$publicId = (string) UuidV7::generate();
```

Cada modelo exposto na API deve:
- Gerar `public_id` automaticamente no evento `creating`.
- Garantir unicidade via índice `unique(tenant_id, public_id)`.

Alternativa: qualquer lib com suporte a UUIDv7, respeitando o contrato `string` padrão UUID.

## Logs / auditoria — PII
PII que nunca deve ir “cru” para logs: CPF/CNPJ, e-mail, telefone, endereço, nome completo em contexto sensível.

Estratégia:
- Preferir IDs (`id`, `public_id`) em logs técnicos.
- Quando precisar logar parte da informação, mascarar (ex.: CPF `***.456.789-**`, CNPJ `12.345.***.0001-**`).
- Auditoria detalhada vai para `auditoria_eventos` (dados antes/depois em JSON).
