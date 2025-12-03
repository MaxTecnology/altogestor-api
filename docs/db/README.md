# Banco de Dados – Convenções e Visão Geral

Este diretório concentra a documentação do **modelo físico** do banco de dados da plataforma.

A ideia é ter aqui tudo que precisamos para:

- projetar e revisar o schema,
- gerar migrations,
- alinhar tipos de dados,
- evitar divergência entre módulos,
- usar IA/Codex para gerar código de acesso ao banco com segurança.

---

## 1. Estratégia de Multi-empresa (Tenant)

A plataforma é **multi-empresa** (várias empresas clientes em um único banco).

Estratégia adotada:

- **Uma única base de dados** (um schema) para todas as empresas.
- Tabelas de domínio possuem campo de vínculo com empresa quando necessário:
  - `empresa_id` obrigatório quando o registro pertence a uma empresa específica.
- Algumas tabelas são **globais** (sem `empresa_id`), por exemplo:
  - tabelas de configuração geral,
  - tabelas de auditoria de alto nível (se fizer sentido).

> Nas migrations, sempre avaliar se a tabela é “global” ou “scoped” por empresa.

---

## 2. Convenções de Nome

### 2.1. Tabelas

- Nome em **snake_case**, no plural:
  - `empresas`
  - `usuarios_escritorio`
  - `usuarios_cliente`
  - `solicitacoes_documentos`
  - `guias_fiscais`
- Tabelas de junção:
  - `empresa_usuario_cliente`
  - `empresa_departamento_responsavel`
  - `empresa_grupo_cliente`

### 2.2. Colunas

- Chave primária: `id` (sempre).
- Chave estrangeira: `<nome_tabela_singular>_id`
  - Ex.: `empresa_id`, `usuario_escritorio_id`, `solicitacao_documento_id`.
- Datas:
  - `created_at`, `updated_at` para timestamps padrão.
  - `deleted_at` para **soft delete** (quando aplicável).
  - Campos de negócio:
    - `data_vencimento`, `data_pagamento`, `data_envio`, etc.

### 2.3. Tipos de Dados (sugestão base)

- `id`: `BIGINT UNSIGNED` com auto incremento.
- Flags booleanas: `TINYINT(1)` ou tipo boolean do banco (mapeado para `bool` na aplicação).
- Valores monetários: `DECIMAL(15,2)` (ou `DECIMAL(18,2)` se valores maiores).
- Strings curtas: `VARCHAR(100)` ou `VARCHAR(150)`.
- Texto livre (observações): `TEXT`.
- CNPJ/CPF:
  - no banco, guardar somente dígitos: `CHAR(14)` / `CHAR(11)`,
  - formatação feita na aplicação.

---

## 3. Organização dos Diagramas ER

Os diagramas ER são separados por módulo, seguindo os domains:

- `db-documentos.puml`  
  Módulo de **solicitação e envio de documentos**.

- `db-obrigacoes-guias.puml` *(a criar)*  
  Módulo de **obrigações e guias fiscais**.

- `db-tarefas-agenda.puml` *(a criar)*  
  Módulo de **tarefas recorrentes / calendário**.

- `db-pedidos-formularios.puml` *(a criar)*  
  Módulo de **pedidos com formulários parametrizados**.

Cada diagrama ER é feito em PlantUML e tem foco em:

- tabelas,
- chaves primárias,
- chaves estrangeiras,
- relacionamentos 1:N / N:N.

---

## 4. Uso com Codex / IA

Ao pedir ajuda para gerar migrations ou models, o fluxo sugerido é:

1. Referenciar o diagrama ER relevante (por exemplo, `db-documentos.puml`).
2. Pedir:
   - `Crie migrations para MySQL com base nas tabelas do ER db-documentos.puml.`
   - ou  
   - `Gere models Eloquent com relacionamentos usando o ER db-obrigacoes-guias.puml.`

---

## 5. Próximos passos de documentação de DB

1. Completar os ER por módulo:
   - `db-obrigacoes-guias.puml`
   - `db-tarefas-agenda.puml`
   - `db-pedidos-formularios.puml`
2. Criar arquivos de referência de tabelas:
   - `tabelas-documentos.md`
   - `tabelas-obrigacoes-guias.md`
   etc., descrevendo campo a campo.

