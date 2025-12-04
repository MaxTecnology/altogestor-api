# Multi-tenancy

## Definições

- **Tenant:** Escritório de contabilidade.
- **Empresa:** Empresa cliente atendida pelo tenant.
- **Usuário:** Pode pertencer a um tenant e possuir acesso a múltiplas empresas.

## Modelo Multi-tenant

O sistema adotará multi-tenancy **colunar**, via coluna `tenant_id` em todas as tabelas de domínio.

### Benefícios

- Simplicidade no desenvolvimento.
- Joins rápidos e relatórios fáceis.
- Flexibilidade para futuramente separar alguns tenants em schemas.

## Regras Gerais

### 1. Toda tabela deve ter:
- `tenant_id` (FK)
- índices compostos iniciados por `tenant_id`

### 2. Unicidade sempre com tenant
Exemplo:
```sql
unique(tenant_id, cnpj)
unique(tenant_id, email)

3. Global Scope

Todos os modelos usam a trait HasTenant, aplicando um escopo global where tenant_id = currentTenantId().

4. TenantManager

Classe central responsável por:

obter o tenant atual

setar o tenant atual

limpar contexto

suportar execução de jobs por tenant

5. Contexto em APIs

Todo request deve carregar o tenant via:

autenticação do usuário

empresa selecionada

cabeçalho opcional para integrações: X-Tenant-ID

6. Contexto em Jobs

Todo job deve conter:

public int $tenantId;


Middleware TenantMiddleware será responsável por configurar o tenant no início da execução do job.

7. Contexto em Logs

Logs estruturados devem incluir:

tenant_id

empresa_id

user_id

request_id

## Resolução de Tenant (Ordem de Prioridade)

A resolução do `tenant` segue a ordem abaixo:

1. **Token do usuário (painel / API autenticada)**  
   - O token Sanctum carrega a informação de qual tenant o usuário pertence.  
   - Esse é o modo padrão para o painel e APIs usadas pelo frontend.

2. **Empresa ativa do usuário**  
   - A empresa ativa é sempre uma empresa pertencente ao tenant do usuário.  
   - É usada para escopar ações por empresa (obrigações, tarefas, etc.), mas **não** altera o `tenant_id`.

3. **Header `X-Tenant-ID` (integrações)**  
   - Usado apenas em cenários de integração com tokens de serviço (API tokens sem usuário humano).  
   - O valor do header deve ser o `public_id` do tenant.  
   - Se o header estiver presente, ele será usado para resolver o tenant **desde que o token tenha permissão para esse tenant**.

### Erros em Caso de Falha

- Se não for possível resolver o tenant em uma requisição que exige autenticação de usuário:
  - Retornar **401 Unauthorized** com código `tenant_not_resolved`.

- Se for uma integração com token válido, mas `X-Tenant-ID` estiver ausente ou inválido:
  - Retornar **400 Bad Request** com código `tenant_header_invalid`.
