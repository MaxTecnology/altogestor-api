# Checklist para Migrations

Quando criar uma nova tabela:

## 1. Estrutura básica
- [ ] `id` bigint
- [ ] `public_id` UUID
- [ ] `tenant_id`
- [ ] `created_at`
- [ ] `updated_at`

## 2. Índices padrão
- [ ] `INDEX (tenant_id, id)`
- [ ] `INDEX (tenant_id, public_id)`

## 3. Unicidade
- [ ] Toda unique inclui `tenant_id`

## 4. FKs
- [ ] FK sempre filtrada por tenant no domínio

## 5. Soft deletes?
- [ ] Avaliar caso a caso

## 6. Auditoria
- [ ] Modelo requer histórico? Criar a tabela *entidade_historico*

## 7. Exposição para API
- [ ] Se exposta, precisa `public_id`
