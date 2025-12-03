# Banco de Dados – Tabelas do Módulo de Documentos

Este documento descreve, coluna a coluna, as principais tabelas envolvidas no **módulo de documentos e solicitações**.

Ele é pensado para servir como base para:

- criação de **migrations**;
- geração de **models** e **DTOs**;
- entendimento funcional por parte do time de produto/negócio;
- uso com IA/Codex para gerar código com segurança.

As definições de tipo são **sugestões** (pensando em MySQL/PostgreSQL); você pode ajustá-las conforme o banco escolhido.

---

## 1. Tabela `empresas`

Tabela base, usada por vários módulos. Aqui está apenas o essencial para contexto do módulo de documentos.

### Finalidade

Armazena os dados principais das empresas clientes do escritório.

### Colunas

| Coluna     | Tipo sugerido       | Nulo? | Descrição                                                                 |
|-----------|----------------------|-------|---------------------------------------------------------------------------|
| `id`      | BIGINT UNSIGNED PK   | NÃO   | Identificador único da empresa.                                           |
| `nome`    | VARCHAR(150)         | NÃO   | Nome ou razão social da empresa.                                          |
| `cnpj_cpf`| VARCHAR(20)          | NÃO   | CNPJ ou CPF da empresa, preferencialmente apenas dígitos.                 |
| `ativo`   | TINYINT(1) / BOOLEAN | NÃO   | Indica se a empresa está ativa na plataforma.                             |

### Índices e chaves

- `PRIMARY KEY (id)`
- Opcional: índice ou UNIQUE em `cnpj_cpf` conforme regra de negócio.

---

## 2. Tabela `departamentos`

### Finalidade

Define os departamentos internos do escritório (ex.: Contábil, Fiscal, DP), usados para organizar modelos de documentos e responsabilidades.

### Colunas

| Coluna      | Tipo sugerido      | Nulo? | Descrição                                  |
|------------|---------------------|-------|--------------------------------------------|
| `id`       | BIGINT UNSIGNED PK  | NÃO   | Identificador único do departamento.       |
| `nome`     | VARCHAR(100)        | NÃO   | Nome do departamento.                      |
| `descricao`| VARCHAR(255) / TEXT | SIM   | Descrição opcional do departamento.        |

### Índices e chaves

- `PRIMARY KEY (id)`

---

## 3. Tabela `usuarios_escritorio`

### Finalidade

Armazena os usuários internos (colaboradores do escritório contábil).

### Colunas

| Coluna    | Tipo sugerido      | Nulo? | Descrição                                              |
|----------|---------------------|-------|--------------------------------------------------------|
| `id`     | BIGINT UNSIGNED PK  | NÃO   | Identificador único do usuário interno.               |
| `nome`   | VARCHAR(150)        | NÃO   | Nome completo do colaborador.                         |
| `email`  | VARCHAR(150)        | NÃO   | E-mail de login/contato do colaborador.               |
| `telefone`| VARCHAR(30)        | SIM   | Telefone de contato (ramal, celular, etc.).           |
| `cargo`  | VARCHAR(100)        | SIM   | Cargo exercido (ex.: Analista Fiscal, Sócio, etc.).   |
| `ativo`  | TINYINT(1)/BOOLEAN  | NÃO   | Indica se o usuário está ativo.                       |

### Índices e chaves

- `PRIMARY KEY (id)`
- Recomenda-se `UNIQUE (email)`.

---

## 4. Tabela `usuarios_cliente`

### Finalidade

Representa os usuários do **lado do cliente** (pessoas que acessam o portal para enviar documentos, ver guias, etc.).

### Colunas

| Coluna    | Tipo sugerido      | Nulo? | Descrição                                              |
|----------|---------------------|-------|--------------------------------------------------------|
| `id`     | BIGINT UNSIGNED PK  | NÃO   | Identificador único do usuário cliente.               |
| `nome`   | VARCHAR(150)        | NÃO   | Nome completo do usuário.                             |
| `email`  | VARCHAR(150)        | NÃO   | E-mail de login/contato do usuário.                   |
| `telefone`| VARCHAR(30)        | SIM   | Telefone de contato.                                  |
| `cargo`  | VARCHAR(100)        | SIM   | Cargo na empresa (ex.: Dono, Financeiro, Contador).   |
| `ativo`  | TINYINT(1)/BOOLEAN  | NÃO   | Indica se o usuário está ativo.                       |

### Índices e chaves

- `PRIMARY KEY (id)`
- Recomenda-se `INDEX (email)` ou `UNIQUE (email)` dependendo da estratégia de login.

---

## 5. Tabela `empresa_usuario_cliente`

### Finalidade

Tabela de vínculo entre **empresa** e **usuários_cliente**, permitindo que um usuário tenha acesso a mais de uma empresa (se desejado) e definindo preferências de recebimento.

### Colunas

| Coluna                       | Tipo sugerido      | Nulo? | Descrição                                                                 |
|-----------------------------|---------------------|-------|---------------------------------------------------------------------------|
| `id`                        | BIGINT UNSIGNED PK  | NÃO   | Identificador único do vínculo.                                          |
| `empresa_id`                | BIGINT UNSIGNED FK  | NÃO   | Referência à tabela `empresas`.                                          |
| `usuario_cliente_id`        | BIGINT UNSIGNED FK  | NÃO   | Referência à tabela `usuarios_cliente`.                                  |
| `perfil_acesso`             | VARCHAR(50)         | NÃO   | Perfil de acesso (ex.: `admin`, `padrao`, `somente_visualizacao`).       |
| `receber_email_lembrete_impostos` | TINYINT(1)/BOOLEAN | NÃO | Indica se este contato recebe lembretes de impostos por e-mail.          |

### Índices e chaves

- `PRIMARY KEY (id)`
- `FOREIGN KEY (empresa_id) REFERENCES empresas(id)`
- `FOREIGN KEY (usuario_cliente_id) REFERENCES usuarios_cliente(id)`
- Recomenda-se `UNIQUE (empresa_id, usuario_cliente_id)` para não duplicar vínculo.

---

## 6. Tabela `empresa_departamento_responsavel`

### Finalidade

Configura, por empresa, **quem é o responsável interno (usuário do escritório)** por cada departamento (ex.: “Fulano é responsável fiscal da Empresa X”).

### Colunas

| Coluna               | Tipo sugerido      | Nulo? | Descrição                                                   |
|----------------------|---------------------|-------|-------------------------------------------------------------|
| `id`                 | BIGINT UNSIGNED PK  | NÃO   | Identificador único do vínculo.                            |
| `empresa_id`         | BIGINT UNSIGNED FK  | NÃO   | Referência à tabela `empresas`.                            |
| `departamento_id`    | BIGINT UNSIGNED FK  | NÃO   | Referência à tabela `departamentos`.                       |
| `usuario_escritorio_id` | BIGINT UNSIGNED FK| NÃO   | Referência à tabela `usuarios_escritorio` (responsável).   |

### Índices e chaves

- `PRIMARY KEY (id)`
- `FOREIGN KEY (empresa_id) REFERENCES empresas(id)`
- `FOREIGN KEY (departamento_id) REFERENCES departamentos(id)`
- `FOREIGN KEY (usuario_escritorio_id) REFERENCES usuarios_escritorio(id)`
- Recomenda-se `UNIQUE (empresa_id, departamento_id)` para ter **um responsável principal** por depto (se for a regra).

---

## 7. Tabela `modelos_documentos`

### Finalidade

Define os **modelos de documentos** cobrados por empresa e departamento, com periodicidade, criticidade e regras de bloqueio.

### Colunas

| Coluna              | Tipo sugerido        | Nulo? | Descrição                                                                 |
|---------------------|----------------------|-------|---------------------------------------------------------------------------|
| `id`                | BIGINT UNSIGNED PK   | NÃO   | Identificador único do modelo de documento.                              |
| `empresa_id`        | BIGINT UNSIGNED FK   | NÃO   | Empresa à qual esse modelo pertence.                                     |
| `departamento_id`   | BIGINT UNSIGNED FK   | NÃO   | Departamento responsável (Fiscal, Contábil, etc.).                       |
| `nome_documento`    | VARCHAR(150)         | NÃO   | Nome descritivo do documento (ex.: `XML de Saídas`, `Relatório de Vendas`). |
| `tipo`              | VARCHAR(50)          | NÃO   | Tipo categórico (ex.: `xml_entrada`, `relatorio`, `outro`).              |
| `periodicidade`     | VARCHAR(50)          | NÃO   | Periodicidade (`mensal`, `trimestral`, `anual`, `eventual`).             |
| `dia_limite_envio`  | INT                  | SIM   | Dia limite padrão do envio (1–31), opcional se for outra regra.         |
| `critico`           | TINYINT(1)/BOOLEAN   | NÃO   | Indica se é um documento crítico para apuração.                          |
| `permite_envio_parcial` | TINYINT(1)/BOOLEAN | NÃO | Se permite que o cliente envie parte dos documentos antes de completar. |
| `regra_bloqueio`    | VARCHAR(50)          | SIM   | Regra de bloqueio (`nenhum`, `aviso`, `bloquear_servicos`, etc.).       |
| `observacoes`       | TEXT                 | SIM   | Observações internas.                                                    |
| `ativo`             | TINYINT(1)/BOOLEAN   | NÃO   | Indica se o modelo está ativo para novas solicitações.                   |

### Índices e chaves

- `PRIMARY KEY (id)`
- `FOREIGN KEY (empresa_id) REFERENCES empresas(id)`
- `FOREIGN KEY (departamento_id) REFERENCES departamentos(id)`
- Recomenda-se índice em `(empresa_id, departamento_id)`
- Opcional: `UNIQUE (empresa_id, nome_documento)` se não quiser nomes duplicados por empresa.

---

## 8. Tabela `solicitacoes_documentos`

### Finalidade

Representa uma **solicitação de documentos** para uma empresa em um determinado período (ex.: “Documentos fiscais de 10/2025”).

### Colunas

| Coluna                 | Tipo sugerido        | Nulo? | Descrição                                                                 |
|------------------------|----------------------|-------|---------------------------------------------------------------------------|
| `id`                   | BIGINT UNSIGNED PK   | NÃO   | Identificador único da solicitação.                                      |
| `modelo_documento_id`  | BIGINT UNSIGNED FK   | NÃO   | Referência ao `modelos_documentos` que originou a solicitação.           |
| `empresa_id`           | BIGINT UNSIGNED FK   | NÃO   | Empresa à qual a solicitação pertence.                                   |
| `departamento_id`      | BIGINT UNSIGNED FK   | NÃO   | Departamento relacionado.                                                |
| `periodo_referencia`   | VARCHAR(20)          | NÃO   | Período de referência (ex.: `2025-10`).                                  |
| `data_limite`          | DATETIME             | SIM   | Data limite para envio da documentação.                                  |
| `status`               | VARCHAR(30)          | NÃO   | Status da solicitação (`PENDENTE`, `PARCIAL`, `EM_VALIDACAO`, etc.).     |
| `data_status`          | DATETIME             | SIM   | Data/hora da última mudança de status.                                   |
| `data_conclusao`       | DATETIME             | SIM   | Data/hora em que a solicitação foi concluída (COMPLETO).                 |
| `usuario_responsavel_id` | BIGINT UNSIGNED FK | SIM   | Usuário do escritório responsável pela análise/validação.                |

### Índices e chaves

- `PRIMARY KEY (id)`
- `FOREIGN KEY (modelo_documento_id) REFERENCES modelos_documentos(id)`
- `FOREIGN KEY (empresa_id) REFERENCES empresas(id)`
- `FOREIGN KEY (departamento_id) REFERENCES departamentos(id)`
- `FOREIGN KEY (usuario_responsavel_id) REFERENCES usuarios_escritorio(id)`
- Recomenda-se índice em `(empresa_id, periodo_referencia, departamento_id)`.

---

## 9. Tabela `documentos_enviados`

### Finalidade

Registra cada arquivo/documento enviado pelo cliente em resposta a uma solicitação específica.

### Colunas

| Coluna                    | Tipo sugerido        | Nulo? | Descrição                                                         |
|---------------------------|----------------------|-------|-------------------------------------------------------------------|
| `id`                      | BIGINT UNSIGNED PK   | NÃO   | Identificador único do documento enviado.                         |
| `solicitacao_documento_id`| BIGINT UNSIGNED FK   | NÃO   | Referência à `solicitacoes_documentos`.                           |
| `usuario_cliente_id`      | BIGINT UNSIGNED FK   | NÃO   | Usuário cliente que fez o upload.                                 |
| `data_envio`              | DATETIME             | NÃO   | Data/hora do envio do arquivo.                                    |
| `nome_arquivo`            | VARCHAR(255)         | NÃO   | Nome original do arquivo.                                         |
| `caminho_arquivo`         | VARCHAR(500)         | NÃO   | Caminho/URL no storage.                                           |
| `tipo_arquivo`            | VARCHAR(50)          | SIM   | Tipo de arquivo (MIME ou categoria: `pdf`, `xml`, `imagem`).      |
| `origem`                  | VARCHAR(50)          | NÃO   | Origem do envio (`portal`, `importacao`, `integracao`).           |

### Índices e chaves

- `PRIMARY KEY (id)`
- `FOREIGN KEY (solicitacao_documento_id) REFERENCES solicitacoes_documentos(id)`
- `FOREIGN KEY (usuario_cliente_id) REFERENCES usuarios_cliente(id)`
- Recomenda-se índice em `(solicitacao_documento_id, data_envio)`.

---

## 10. Tabela `historico_estado_solicitacao`

### Finalidade

Mantém o **histórico de mudança de estados** de cada solicitação, para auditoria e rastreabilidade.

### Colunas

| Coluna                  | Tipo sugerido        | Nulo? | Descrição                                                             |
|-------------------------|----------------------|-------|-----------------------------------------------------------------------|
| `id`                    | BIGINT UNSIGNED PK   | NÃO   | Identificador único do registro de histórico.                         |
| `solicitacao_documento_id` | BIGINT UNSIGNED FK| NÃO   | Referência à `solicitacoes_documentos`.                               |
| `estado_anterior`       | VARCHAR(30)          | SIM   | Estado anterior (pode ser nulo se for o primeiro registro).          |
| `estado_novo`           | VARCHAR(30)          | NÃO   | Novo estado aplicado.                                                 |
| `data_alteracao`        | DATETIME             | NÃO   | Data/hora da alteração.                                               |
| `usuario_escritorio_id` | BIGINT UNSIGNED FK   | SIM   | Usuário do escritório responsável pela alteração (pode ser nulo em mudanças automáticas). |
| `motivo`                | TEXT                 | SIM   | Motivo ou observação da mudança (ex.: “Documento ilegível”).         |

### Índices e chaves

- `PRIMARY KEY (id)`
- `FOREIGN KEY (solicitacao_documento_id) REFERENCES solicitacoes_documentos(id)`
- `FOREIGN KEY (usuario_escritorio_id) REFERENCES usuarios_escritorio(id)`
- Recomenda-se índice em `(solicitacao_documento_id, data_alteracao)`.

---

## 11. Tabela `envios_documento_cliente`

### Finalidade

Registra os **envios ativos** feitos pelo escritório ao cliente (e-mail, WhatsApp, etc.), incluindo links para documentos, guias, solicitações, etc.

### Colunas

| Coluna               | Tipo sugerido        | Nulo? | Descrição                                                                 |
|----------------------|----------------------|-------|---------------------------------------------------------------------------|
| `id`                 | BIGINT UNSIGNED PK   | NÃO   | Identificador único do envio.                                            |
| `empresa_id`         | BIGINT UNSIGNED FK   | NÃO   | Empresa à qual o envio se relaciona.                                     |
| `usuario_escritorio_id` | BIGINT UNSIGNED FK| NÃO   | Usuário do escritório que realizou o envio.                              |
| `usuario_cliente_id` | BIGINT UNSIGNED FK   | SIM   | Contato do cliente que recebeu (pode ser nulo se for envio genérico).    |
| `tipo_recurso`       | VARCHAR(50)          | NÃO   | Tipo de recurso enviado (`solicitacao_documento`, `documento_enviado`, `guia_fiscal`, etc.). |
| `recurso_id`         | BIGINT UNSIGNED      | NÃO   | ID do recurso, conforme o `tipo_recurso`.                                |
| `canal_envio`        | VARCHAR(50)          | NÃO   | Canal (`email`, `whatsapp`, `outro`).                                    |
| `data_envio`         | DATETIME             | NÃO   | Data/hora do envio.                                                      |
| `assunto`            | VARCHAR(255)         | SIM   | Assunto da mensagem (quando aplicável).                                  |
| `mensagem_resumo`    | TEXT                 | SIM   | Corpo/resumo da mensagem enviada.                                        |
| `status_envio`       | VARCHAR(30)          | NÃO   | Status (`pendente`, `enviado`, `erro`).                                  |
| `detalhe_status`     | TEXT                 | SIM   | Detalhes em caso de erro ou informações da resposta do provedor.        |

### Índices e chaves

- `PRIMARY KEY (id)`
- `FOREIGN KEY (empresa_id) REFERENCES empresas(id)`
- `FOREIGN KEY (usuario_escritorio_id) REFERENCES usuarios_escritorio(id)`
- `FOREIGN KEY (usuario_cliente_id) REFERENCES usuarios_cliente(id)`
- Recomenda-se índice em `(empresa_id, data_envio)`
- Opcional: índice em `(tipo_recurso, recurso_id)` para rastrear envios de um recurso específico.

---

## 12. Próximos passos

Sugestão para deixar o módulo ainda mais sólido:

- Criar `docs/db/tabelas-obrigacoes-guias.md` com o mesmo nível de detalhe.  
- Criar `docs/db/tabelas-tarefas-agenda.md`.  
- Criar `docs/db/tabelas-pedidos-formularios.md`.