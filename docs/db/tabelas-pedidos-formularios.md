# Banco de Dados – Tabelas de Pedidos e Formulários Parametrizáveis

Este documento descreve todas as tabelas relacionadas ao módulo de **Pedidos do Cliente**, incluindo:

- Modelos de pedidos criados pelo escritório
- Campos e documentos parametrizados
- Pedidos abertos pelo cliente
- Respostas de campos
- Documentos enviados
- Histórico de status

Esse módulo permite que o cliente abra solicitações estruturadas através do portal, enviando dados e documentos conforme cada tipo de pedido.

---

# 1. Tabela `modelos_pedidos`

### Finalidade

Define cada tipo de pedido que o cliente pode abrir.  
Exemplos:

- “Alteração de contrato social”
- “Abertura de empresa”
- “Demissão funcionário”
- “Solicitar relatório financeiro”
- “Cadastro de novo sócio”

### Colunas

| Coluna              | Tipo                   | Nulo? | Descrição |
|---------------------|------------------------|-------|-----------|
| `id`                | BIGINT UNSIGNED PK     | NÃO   | Identificador do modelo. |
| `nome`              | VARCHAR(150)           | NÃO   | Nome do modelo de pedido. |
| `descricao`         | TEXT                   | SIM   | Descrição detalhada para uso interno. |
| `departamento_id`   | BIGINT UNSIGNED FK     | NÃO   | Departamento responsável pelo atendimento. |
| `disponivel_portal` | BOOLEAN                | NÃO   | Se o pedido aparece para o cliente no portal. |
| `ativo`             | BOOLEAN                | NÃO   | Se o modelo está ativo. |

### Índices

- `PRIMARY KEY (id)`
- `FOREIGN KEY (departamento_id)`
- `INDEX (disponivel_portal)`

---

# 2. Tabela `modelos_pedidos_campos`

### Finalidade

Define todos os **campos estruturados** que cada modelo de pedido deve ter.  
Cada campo pode ser:

- texto,
- número,
- data,
- CPF/CNPJ,
- lista de opções,
- etc.

### Colunas

| Coluna             | Tipo                   | Nulo? | Descrição |
|--------------------|------------------------|-------|-----------|
| `id`               | BIGINT UNSIGNED PK     | NÃO   | ID do campo. |
| `modelo_pedido_id` | BIGINT UNSIGNED FK     | NÃO   | Referência ao modelo. |
| `nome_campo`       | VARCHAR(150)           | NÃO   | Nome exibido para o usuário. |
| `tipo_campo`       | VARCHAR(50)            | NÃO   | `texto`, `numero`, `data`, `lista`, `cpf`, etc. |
| `obrigatorio`      | BOOLEAN                | NÃO   | Indica se o preenchimento é obrigatório. |
| `ordem`            | INT                    | NÃO   | Ordem de exibição. |
| `configuracao_extra` | TEXT (JSON)          | SIM   | Opções extras (ex.: lista de valores). |

### Índices

- `PRIMARY KEY (id)`
- `FOREIGN KEY (modelo_pedido_id)`
- `INDEX (modelo_pedido_id, ordem)`

---

# 3. Tabela `modelos_pedidos_documentos`

### Finalidade

Define os **documentos obrigatórios ou opcionais** que o cliente deve enviar ao abrir um pedido.

Exemplos:

- RG do sócio
- Comprovante de residência
- Contrato anterior
- Procuração

### Colunas

| Coluna                | Tipo                   | Nulo? | Descrição |
|-----------------------|------------------------|-------|-----------|
| `id`                  | BIGINT UNSIGNED PK     | NÃO   | Identificador. |
| `modelo_pedido_id`    | BIGINT UNSIGNED FK     | NÃO   | Modelo de pedido. |
| `descricao`           | VARCHAR(200)           | NÃO   | Descrição do documento solicitado. |
| `tipo_arquivo_permitido` | VARCHAR(50)          | NÃO   | `pdf`, `imagem`, `qualquer`. |
| `obrigatorio`         | BOOLEAN                | NÃO   | Se é obrigatório. |
| `ordem`               | INT                    | NÃO   | Ordem. |

### Índices

- `PRIMARY KEY (id)`
- `FOREIGN KEY (modelo_pedido_id)`
- `INDEX (modelo_pedido_id, ordem)`

---

# 4. Tabela `pedidos_clientes`

### Finalidade

Tabela principal do módulo.  
Representa um **pedido concreto** aberto pelo cliente.

### Colunas

| Coluna                | Tipo                   | Nulo? | Descrição |
|-----------------------|------------------------|-------|-----------|
| `id`                  | BIGINT UNSIGNED PK     | NÃO   | ID do pedido. |
| `empresa_id`          | BIGINT UNSIGNED FK     | NÃO   | Empresa solicitante. |
| `modelo_pedido_id`    | BIGINT UNSIGNED FK     | NÃO   | Tipo de pedido. |
| `usuario_cliente_id`  | BIGINT UNSIGNED FK     | NÃO   | Quem abriu o pedido. |
| `usuario_escritorio_responsavel_id` | BIGINT UNSIGNED FK | SIM | Analista responsável. |
| `data_abertura`       | DATETIME               | NÃO   | Data/hora da abertura. |
| `data_fechamento`     | DATETIME               | SIM   | Data/hora da conclusão. |
| `status`              | VARCHAR(30)            | NÃO   | `ABERTO`, `EM_ANALISE`, `AGUARDANDO_CLIENTE`, `CONCLUIDO`, `CANCELADO`. |
| `prioridade`          | VARCHAR(20)            | SIM   | `baixa`, `normal`, `alta`. |
| `canal_origem`        | VARCHAR(30)            | NÃO   | `portal`, `email`, `telefone`, `interno`. |
| `observacoes_internas`| TEXT                   | SIM   | Notas internas. |

### Índices

- `PRIMARY KEY (id)`
- `FOREIGN KEY (empresa_id)`
- `FOREIGN KEY (modelo_pedido_id)`
- `FOREIGN KEY (usuario_cliente_id)`
- `FOREIGN KEY (usuario_escritorio_responsavel_id)`
- Índices:
  - `(empresa_id, status)`
  - `(usuario_escritorio_responsavel_id, status)`
  - `(data_abertura)`

---

# 5. Tabela `pedidos_campos_respostas`

### Finalidade

Armazena os **dados preenchidos** pelo cliente nos campos definidos no modelo.

Um pedido pode ter dezenas de respostas dependendo da complexidade.

### Colunas

| Coluna             | Tipo                   | Nulo? | Descrição |
|--------------------|------------------------|-------|-----------|
| `id`               | BIGINT UNSIGNED PK     | NÃO   | ID da resposta. |
| `pedido_id`        | BIGINT UNSIGNED FK     | NÃO   | Pedido ao qual pertence. |
| `modelo_campo_id`  | BIGINT UNSIGNED FK     | NÃO   | Referência ao campo do modelo. |
| `valor_texto`      | TEXT                   | SIM   | Valor textual. |
| `valor_numero`     | DECIMAL(18,2)          | SIM   | Valor numérico. |
| `valor_data`       | DATETIME               | SIM   | Data. |

> A aplicação deve garantir que **apenas um tipo** seja preenchido com base no `tipo_campo`.

### Índices

- `PRIMARY KEY (id)`
- `INDEX (pedido_id)`
- `INDEX (modelo_campo_id)`

---

# 6. Tabela `pedidos_documentos_enviados`

### Finalidade

Armazena os **arquivos enviados** pelo cliente ao solicitar o pedido.

### Colunas

| Coluna               | Tipo                   | Nulo? | Descrição |
|----------------------|------------------------|-------|-----------|
| `id`                 | BIGINT UNSIGNED PK     | NÃO   | ID. |
| `pedido_id`          | BIGINT UNSIGNED FK     | NÃO   | Pedido relacionado. |
| `modelo_documento_id`| BIGINT UNSIGNED FK     | NÃO   | Documento configurado no modelo. |
| `nome_arquivo`       | VARCHAR(255)           | NÃO   | Nome original do arquivo. |
| `caminho_arquivo`    | VARCHAR(500)           | NÃO   | Caminho/URL no storage. |
| `data_envio`         | DATETIME               | NÃO   | Data de upload. |
| `usuario_cliente_id` | BIGINT UNSIGNED FK     | NÃO   | Usuário que enviou. |

### Índices

- `PRIMARY KEY (id)`
- `FOREIGN KEY (pedido_id)`
- `FOREIGN KEY (modelo_documento_id)`
- `FOREIGN KEY (usuario_cliente_id)`
- `INDEX (pedido_id)`

---

# 7. Tabela `historico_pedidos_clientes`

### Finalidade

Grava todas as alterações de status dos pedidos, para rastreabilidade total.

### Colunas

| Coluna                | Tipo                   | Nulo? | Descrição |
|-----------------------|------------------------|-------|-----------|
| `id`                  | BIGINT UNSIGNED PK     | NÃO   | ID. |
| `pedido_id`           | BIGINT UNSIGNED FK     | NÃO   | Pedido relacionado. |
| `status_anterior`     | VARCHAR(30)            | SIM   | Estado anterior. |
| `status_novo`         | VARCHAR(30)            | NÃO   | Novo estado. |
| `data_alteracao`      | DATETIME               | NÃO   | Data/hora da mudança. |
| `usuario_responsavel_id` | BIGINT UNSIGNED FK  | SIM   | Usuário do escritório que alterou. |
| `motivo`              | TEXT                   | SIM   | Motivo/observações da alteração. |

### Índices

- `PRIMARY KEY (id)`
- `FOREIGN KEY (pedido_id)`
- `INDEX (pedido_id, data_alteracao)`
- `FOREIGN KEY (usuario_responsavel_id)`

---

# 8. Integração com outros módulos

Este módulo depende diretamente das tabelas:

- `empresas`
- `usuarios_cliente`
- `usuarios_escritorio`
- `departamentos`

E pode integrar com:

- documentos enviados para validação,
- solicitações de documentos obrigatórios,
- cronograma de tarefas do escritório.

---

# 9. Recomendações de backend

- Criar services dedicados:
  - `PedidoService`
  - `ModeloPedidoService`
  - `AnexoPedidoService`
- Validar campos com base em `tipo_campo`.
- Gerar eventos internos:
  - `PedidoCriado`
  - `PedidoAtualizado`
  - `PedidoConcluido`
- Implementar **webhooks** internos para automações futuras.

---