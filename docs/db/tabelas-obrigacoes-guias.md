# Banco de Dados – Tabelas de Obrigações e Guias Fiscais

Este documento descreve todas as tabelas relacionadas ao módulo de **obrigações e guias fiscais**, incluindo:

- tipos de obrigação,
- configurações por empresa,
- guias fiscais,
- comprovantes de pagamento,
- vínculos com documentos enviados pelo cliente (quando aplicável).

Esta documentação serve de base para geração de migrations, models, validações e endpoints de API.

---

# 1. Tabela `tipos_obrigacoes`

### Finalidade
Define os tipos de obrigações fiscais, contábeis e trabalhistas oferecidas/planejadas pela plataforma.

Exemplos:  
- DAS  
- DCTF  
- DEFIS  
- E-SOCIAL (eventos)  
- SPED Fiscal  
- ISSQN mensal

### Colunas

| Coluna          | Tipo              | Nulo? | Descrição |
|-----------------|-------------------|-------|-----------|
| `id`            | BIGINT PK         | NÃO   | Identificador único da obrigação. |
| `nome`          | VARCHAR(150)      | NÃO   | Nome da obrigação (ex.: DAS). |
| `descricao`     | TEXT              | SIM   | Descrição interna. |
| `departamento_id` | BIGINT FK       | NÃO   | Departamento responsável (Fiscal/Contábil/DP). |
| `tipo_imposto`  | VARCHAR(50)       | SIM   | Tipo categórico (ex.: federal, municipal, trabalhista). |
| `periodicidade` | VARCHAR(20)       | NÃO   | `mensal`, `trimestral`, `anual`, `eventual`. |
| `ativo`         | BOOLEAN           | NÃO   | Indica se está em uso. |

### Índices

- `PRIMARY KEY (id)`
- `FOREIGN KEY (departamento_id)`
- Índice recomendado: `(periodicidade)`

---

# 2. Tabela `configuracoes_obrigacao_empresa`

### Finalidade
Cada empresa pode ter configurações específicas para cada tipo de obrigação.

Exemplos:
- dia limite personalizado,
- responsável interno,
- se gera guia automaticamente ou não,
- regras de exceção.

### Colunas

| Coluna                   | Tipo          | Nulo? | Descrição |
|--------------------------|---------------|-------|-----------|
| `id`                     | BIGINT PK     | NÃO   | Identificador. |
| `empresa_id`             | BIGINT FK     | NÃO   | Empresa vinculada. |
| `tipo_obrigacao_id`      | BIGINT FK     | NÃO   | Tipo de obrigação. |
| `dia_limite_padrao`      | INT           | SIM   | Para cálculo de vencimento. |
| `responsavel_departamento_id` | BIGINT FK | SIM | Usuário responsável pela obrigação. |
| `gera_guia_no_sistema`   | BOOLEAN       | NÃO   | Se a guia será gerada internamente. |
| `observacoes`            | TEXT          | SIM   | Notas internas. |
| `ativo`                  | BOOLEAN       | NÃO   | Se a regra está ativa. |

### Índices

- `UNIQUE (empresa_id, tipo_obrigacao_id)` ← **evita duplicidade**
- Índice: `(responsavel_departamento_id)`

---

# 3. Tabela `guias_fiscais`

### Finalidade
Armazena as guias geradas pelo escritório (ou importadas), com valores, vencimento e status.

### Colunas

| Coluna            | Tipo               | Nulo? | Descrição |
|-------------------|--------------------|-------|-----------|
| `id`              | BIGINT PK          | NÃO   | ID da guia. |
| `empresa_id`      | BIGINT FK          | NÃO   | Empresa. |
| `tipo_obrigacao_id` | BIGINT FK        | NÃO   | Tipo de obrigação. |
| `competencia`     | VARCHAR(7)         | NÃO   | Ex.: `2025-10`. |
| `data_vencimento` | DATETIME           | NÃO   | Vencimento da guia. |
| `valor_principal` | DECIMAL(15,2)      | NÃO   | Valor da obrigação. |
| `valor_juros`     | DECIMAL(15,2)      | SIM   | Juros (se houver). |
| `valor_multa`     | DECIMAL(15,2)      | SIM   | Multa (se houver). |
| `valor_total`     | DECIMAL(15,2)      | NÃO   | Valor total final. |
| `status_guia`     | VARCHAR(30)        | NÃO   | Estado (ver state machine). |
| `data_status`     | DATETIME           | SIM   | Atualização do estado. |
| `usuario_responsavel_id` | BIGINT FK    | SIM   | Quem gerou/alterou. |
| `observacoes`     | TEXT               | SIM   | Observações internas. |

### Índices

- `UNIQUE (empresa_id, tipo_obrigacao_id, competencia)` ← **evita guias duplicadas**
- Índice: `(status_guia)`
- Índice: `(data_vencimento)`

---

# 4. Tabela `comprovantes_pagamento`

### Finalidade
Armazena comprovantes enviados pelo cliente ou anexados internamente.

### Colunas

| Coluna           | Tipo               | Nulo? | Descrição |
|------------------|--------------------|-------|-----------|
| `id`             | BIGINT PK          | NÃO   | ID. |
| `guia_fiscal_id` | BIGINT FK          | NÃO   | Guia relacionada. |
| `usuario_cliente_id` | BIGINT FK       | SIM   | Quem enviou (cliente). |
| `data_envio`     | DATETIME           | NÃO   | Data de upload. |
| `nome_arquivo`   | VARCHAR(255)       | NÃO   | Nome original. |
| `caminho_arquivo`| VARCHAR(500)       | NÃO   | Local do arquivo no storage. |
| `observacoes`    | TEXT               | SIM   | Observações internas. |

### Índices

- `INDEX (guia_fiscal_id)`

---

# 5. Tabela `vinculos_guia_solicitacao_documento`

### Finalidade
Permite vincular uma guia fiscal a documentos enviados pelo cliente para fins de auditoria.

### Colunas

| Coluna                     | Tipo           | Nulo? | Descrição |
|----------------------------|----------------|-------|-----------|
| `id`                       | BIGINT PK      | NÃO   | ID. |
| `guia_fiscal_id`           | BIGINT FK      | NÃO   | Guia vinculada. |
| `solicitacao_documento_id` | BIGINT FK      | NÃO   | Solicitação vinculada. |
| `tipo_vinculo`            | VARCHAR(50)    | NÃO   | Ex.: `base_apuracao`, `suporte`, `anexo`. |

### Índices

- `UNIQUE (guia_fiscal_id, solicitacao_documento_id)`
- Índice: `(tipo_vinculo)`

---

# 6. Regras importantes do módulo

- Cada empresa deve ter **no máximo uma** configuração por tipo de obrigação.  
- Cada empresa deve ter **no máximo uma** guia por:
  - tipo de obrigação  
  - + competência  
- Guias podem ser enviadas ao cliente via:
  - portal,  
  - notificações (email/WhatsApp),  
  - API externa no futuro.
- Toda mudança de status deve:
  - atualizar `data_status`,
  - gerar entrada no log (em outro módulo).

---

# 7. Próximos módulos de documentação

Depois deste arquivo, os próximos serão:

1. `tabelas-tarefas-agenda.md`  
2. `tabelas-pedidos-formularios.md`  
3. **API Reference completa**  
   - começando por Documentos  
   - depois Obrigações e Guias  
   - depois Tarefas  
   - depois Pedidos

---

Se quiser, já posso seguir com:

👉 `docs/db/tabelas-tarefas-agenda.md`  
ou  
👉 podemos começar a API (ex.: `/api/v1/documentos`)

Qual prefere continuar agora?
