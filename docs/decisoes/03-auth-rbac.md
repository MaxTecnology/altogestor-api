
---

# **03-auth-rbac.md**

```md
# Autenticação & RBAC

## Stack
- Laravel Sanctum (API Tokens)
- Spatie Permissions (roles/permissões)
- Escopo por tenant

## Usuário

Usuário pertence a um tenant, mas pode estar vinculado a muitas empresas:

Tabela: empresa_user
- tenant_id
- empresa_id
- user_id
- perfil (string)

## Roles & Permissões

Spatie Permission usando `tenant_id`.  
Exemplo de roles por tenant: `admin`, `contador`, `colaborador`.

## /me Endpoint

Retorna:
- dados do usuário
- lista de empresas acessíveis
- empresa ativa
- roles/permissões da empresa ativa
- feature flags

## Troca de Empresa
Endpoint `/switch-empresa`

Atualiza empresa ativa no contexto do token.

## Tokens Sanctum

- Nomeados por contexto (web, integração)
- Política de expiração
- Endpoint `/tokens` para listar/revogar
