# Banco de Dados – Tabelas de Tarefas e Agenda (Obrigações)

Este documento descreve as tabelas relacionadas ao módulo de **tarefas de obrigação** e **agenda** do escritório, incluindo:

- Modelos de tarefa (configuração recorrente, estilo Gestta);
- Tarefas geradas por competência/período;
- Histórico de mudanças de status.

Essas tabelas se conectam diretamente com:

- `tipos_obrigacoes` (módulo de Obrigações/Guias),
- `empresas`,
- `usuarios_escritorio`,
- e são usadas pelo motor de **Geração Periódica**.

---

## 1. Tabela `modelos_tarefas_obrigacao`

### Finalidade

Define o **molde** das tarefas recorrentes que serão geradas para o calendário do escritório.

Exemplos:

- “Apurar DAS ME”
- “Conferir folha de pagamento”
- “Enviar relatório mensal para o cliente”

Cada modelo está vinculado a:

- um **departamento**,
- um **tipo de obrigação**,
- regras de **data meta** (competência, vencimento, dia fixo),
- comportamento de antecipar/postergar para dia útil.

### Colunas

| Coluna                  | Tipo sugerido      | Nulo? | Descrição |
|-------------------------|--------------------|-------|-----------|
| `id`                    | BIGINT UNSIGNED PK | NÃO   | Identificador único do modelo de tarefa. |
| `nome`                  | VARCHAR(150)       | NÃO   | Nome do modelo (ex.: “Apurar DAS ME”). |
| `descricao`             | TEXT               | SIM   | Descrição detalhada da tarefa (uso interno). |
| `departamento_id`       | BIGINT UNSIGNED FK | NÃO   | Departamento responsável (Fiscal, Contábil, DP etc.). |
| `tipo_obrigacao_id`     | BIGINT UNSIGNED FK | NÃO   | Tipo de obrigação ao qual essa tarefa está associada. |
| `frequencia`            | VARCHAR(20)        | NÃO   | Frequência: `mensal`, `trimestral`, `anual`, `unica`. |
| `tipo_referencia_data`  | VARCHAR(30)        | NÃO   | Origem da data meta: `vencimento_guia`, `competencia`, `dia_fixo`. |
| `dia_fixo`              | INT                | SIM   | Dia fixo do mês (1–31), usado quando `tipo_referencia_data = dia_fixo`. |
| `offset_dias`           | INT                | SIM   | Offset em dias (pode ser negativo para antecipar). |
| `usar_dia_util`         | TINYINT(1)/BOOLEAN | NÃO   | Se `true`, ajusta para o próximo dia útil quando cair em fim de semana/feriado (dependendo da implementação). |
| `concluir_automaticamente` | TINYINT(1)/BOOLEAN | NÃO | Se a tarefa pode ser concluída automaticamente por algum processo. |
| `gerar_multa`           | TINYINT(1)/BOOLEAN | NÃO   | Indica se atraso deve gerar “multa interna”/penalidade/pontuação. |
| `cliente_pode_ver`      | TINYINT(1)/BOOLEAN | NÃO   | Se o cliente vê essa tarefa em algum painel/relatório. |
| `pontuacao_padrao`      | INT                | SIM   | Peso da tarefa (para SLA, métricas de produtividade). |
| `ativo`                 | TINYINT(1)/BOOLEAN | NÃO   | Indica se o modelo está ativo para novas gerações. |

### Índices e chaves

- `PRIMARY KEY (id)`
- `FOREIGN KEY (departamento_id) REFERENCES departamentos(id)`
- `FOREIGN KEY (tipo_obrigacao_id) REFERENCES tipos_obrigacoes(id)`
- Índices recomendados:
  - `INDEX (departamento_id)`
  - `INDEX (tipo_obrigacao_id)`

---

## 2. Tabela `tarefas_obrigacao`

### Finalidade

Representa a **tarefa concreta** que aparece no calendário do colaborador.

Ela é gerada:

- pelo **Serviço de Geração Periódica** (job automático), ou
- manualmente (por fluxo interno, se desejado).

Cada tarefa:

- pertence a uma **empresa**,
- referencia um **modelo de tarefa**,
- está vinculada a um **tipo de obrigação**,
- tem **competência**, **data meta**, **status** e **responsável**.

### Colunas

| Coluna                    | Tipo sugerido      | Nulo? | Descrição |
|---------------------------|--------------------|-------|-----------|
| `id`                      | BIGINT UNSIGNED PK | NÃO   | Identificador único da tarefa. |
| `empresa_id`              | BIGINT UNSIGNED FK | NÃO   | Empresa à qual a tarefa se refere. |
| `modelo_tarefa_id`        | BIGINT UNSIGNED FK | NÃO   | Referência ao modelo de tarefa (`modelos_tarefas_obrigacao`). |
| `tipo_obrigacao_id`       | BIGINT UNSIGNED FK | NÃO   | Tipo de obrigação associado. |
| `competencia`             | VARCHAR(7)         | NÃO   | Competência da obrigação (ex.: `2025-10`). |
| `data_meta_calculada`     | DATETIME           | NÃO   | Data meta já calculada (considerando regra de offset/dia útil). |
| `data_conclusao`          | DATETIME           | SIM   | Data em que a tarefa foi concluída. |
| `status`                  | VARCHAR(30)        | NÃO   | Status da tarefa (`EM_ABERTO`, `EM_ANDAMENTO`, `CONCLUIDA`, `ATRASADA`, `CANCELADA`, etc.). |
| `responsavel_escritorio_id` | BIGINT UNSIGNED FK | SIM | Usuário interno responsável pela tarefa. |
| `pontuacao`               | INT                | SIM   | Pontuação efetiva (pode herdar do modelo ou ser ajustada). |
| `observacoes`             | TEXT               | SIM   | Comentários e anotações internas. |

### Índices e chaves

- `PRIMARY KEY (id)`
- `FOREIGN KEY (empresa_id) REFERENCES empresas(id)`
- `FOREIGN KEY (modelo_tarefa_id) REFERENCES modelos_tarefas_obrigacao(id)`
- `FOREIGN KEY (tipo_obrigacao_id) REFERENCES tipos_obrigacoes(id)`
- `FOREIGN KEY (responsavel_escritorio_id) REFERENCES usuarios_escritorio(id)`
- Índices recomendados:
  - `INDEX (empresa_id, competencia)`
  - `INDEX (responsavel_escritorio_id, status, data_meta_calculada)`
  - `INDEX (status, data_meta_calculada)`

> Esses índices ajudam bastante no calendário e nas listas de “tarefas vencidas/para hoje/para semana”.

---

## 3. Tabela `historico_tarefas_obrigacao`

### Finalidade

Mantém o **histórico de mudanças de status** das tarefas de obrigação, permitindo:

- auditoria,
- acompanhamento de SLA,
- rastreabilidade (quem alterou e por quê).

### Colunas

| Coluna                 | Tipo sugerido      | Nulo? | Descrição |
|------------------------|--------------------|-------|-----------|
| `id`                   | BIGINT UNSIGNED PK | NÃO   | Identificador único do registro de histórico. |
| `tarefa_obrigacao_id`  | BIGINT UNSIGNED FK | NÃO   | Referência à tarefa (`tarefas_obrigacao`). |
| `status_anterior`      | VARCHAR(30)        | SIM   | Status anterior (pode ser nulo na primeira entrada). |
| `status_novo`          | VARCHAR(30)        | NÃO   | Novo status aplicado. |
| `data_alteracao`       | DATETIME           | NÃO   | Data/hora da mudança. |
| `usuario_escritorio_id`| BIGINT UNSIGNED FK | SIM   | Usuário que realizou a alteração (nulo em alterações automáticas, se desejado). |
| `motivo`               | TEXT               | SIM   | Motivo da alteração ou observações (ex.: “cliente atrasou envio de docs”). |

### Índices e chaves

- `PRIMARY KEY (id)`
- `FOREIGN KEY (tarefa_obrigacao_id) REFERENCES tarefas_obrigacao(id)`
- `FOREIGN KEY (usuario_escritorio_id) REFERENCES usuarios_escritorio(id)`
- Índices recomendados:
  - `INDEX (tarefa_obrigacao_id, data_alteracao)`

---

## 4. Integração com outros módulos

O módulo de **Tarefas e Agenda** se relaciona diretamente com:

- `tipos_obrigacoes` (módulo Obrigações/Guias)  
- `configuracoes_obrigacao_empresa` (define o que deve ser gerado)  
- `guias_fiscais` (tarefas podem acompanhar status da guia)  
- `solicitacoes_documentos` (uma tarefa pode depender de documentos COMPLETOs)

Essas relações normalmente são feitas **no domínio** (lógica de aplicação), mas você pode optar por criar colunas de vínculo direto, como:

- `tarefas_obrigacao.guia_fiscal_id`
- `tarefas_obrigacao.solicitacao_documento_id`

Caso queira, é possível estender este documento no futuro com essas referências adicionais.

---

