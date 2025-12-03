# API Reference – Pedidos & Formulários Parametrizáveis (v1)

O módulo de **Pedidos & Formulários** permite que o cliente abra solicitações estruturadas para o escritório, com:

- tipos de pedidos configuráveis (modelos),
- campos dinâmicos (texto, número, data, lista, CPF/CNPJ etc.),
- documentos obrigatórios/opcionais,
- fluxo de atendimento interno,
- histórico de status.

Prefixo da API:

/api/v1


---

# 1. Autenticação

Todas as rotas são protegidas:

```http
Authorization: Bearer <token>


O token pode representar:

um usuário do cliente (portal),

um usuário do escritório (painel interno),

um token técnico (integração).

2. Recursos da API
Recurso	Descrição
/pedidos/modelos	Definição de tipos de pedido (modelos)
/pedidos/modelos/campos	Campos dinamicamente configuráveis
/pedidos/modelos/documentos	Documentos exigidos para abertura
/pedidos	Pedidos concretos abertos pelo cliente ou internamente
/pedidos/campos	Respostas dos campos preenchidos
/pedidos/documentos	Upload de documentos anexados ao pedido
/pedidos/historico	Histórico de status do pedido
3. Modelos de Pedido
📌 3.1 – Listar modelos de pedido
GET /pedidos/modelos

Retorna os modelos disponíveis, especialmente para o portal do cliente.

Query Params

Param	Tipo	Descrição
departamento_id	number	(Opcional) Filtra por departamento
disponivel_portal	boolean	(Opcional) Filtrar só o que aparece no portal
ativo	boolean	(Opcional) Filtrar por ativo/inativo

Resposta 200

[
  {
    "id": 1,
    "nome": "Alteração de contrato social",
    "descricao": "Alterações em cláusulas, sócios, capital social etc.",
    "departamento_id": 3,
    "disponivel_portal": true,
    "ativo": true
  }
]

📌 3.2 – Criar modelo de pedido
POST /pedidos/modelos

Usado pelo escritório para cadastrar novos tipos de pedido.

Body

{
  "nome": "Abertura de empresa",
  "descricao": "Processo completo de abertura de CNPJ",
  "departamento_id": 3,
  "disponivel_portal": true
}


Resposta 201

{
  "id": 10,
  "nome": "Abertura de empresa",
  "ativo": true
}

📌 3.3 – Atualizar modelo de pedido
PATCH /pedidos/modelos/{id}

Permite atualizar parcialmente.

Exemplo Body

{
  "disponivel_portal": false,
  "ativo": false
}

📌 3.4 – Campos do modelo
GET /pedidos/modelos/{id}/campos

Lista os campos configurados para um modelo.

Resposta

[
  {
    "id": 100,
    "nome_campo": "Nome do sócio",
    "tipo_campo": "texto",
    "obrigatorio": true,
    "ordem": 1
  },
  {
    "id": 101,
    "nome_campo": "CPF do sócio",
    "tipo_campo": "cpf",
    "obrigatorio": true,
    "ordem": 2
  }
]

POST /pedidos/modelos/{id}/campos

Cria um campo no modelo.

Body

{
  "nome_campo": "Capital social",
  "tipo_campo": "numero",
  "obrigatorio": true,
  "ordem": 3,
  "configuracao_extra": {
    "min": 0,
    "max": 999999999.99
  }
}


configuracao_extra pode ser um JSON com regras (lista de opções, máscaras, range etc.)

📌 3.5 – Documentos exigidos pelo modelo
GET /pedidos/modelos/{id}/documentos

Resposta

[
  {
    "id": 50,
    "descricao": "RG do sócio",
    "tipo_arquivo_permitido": "imagem",
    "obrigatorio": true,
    "ordem": 1
  },
  {
    "id": 51,
    "descricao": "Comprovante de residência",
    "tipo_arquivo_permitido": "pdf",
    "obrigatorio": true,
    "ordem": 2
  }
]

POST /pedidos/modelos/{id}/documentos

Body

{
  "descricao": "Contrato social atual",
  "tipo_arquivo_permitido": "pdf",
  "obrigatorio": false,
  "ordem": 3
}

4. Pedidos do Cliente
📌 4.1 – Abrir um pedido (Cliente)
POST /pedidos

Usado pelo portal do cliente.

Body

{
  "empresa_id": 55,
  "modelo_pedido_id": 1,
  "usuario_cliente_id": 200,
  "campos": [
    {
      "modelo_campo_id": 100,
      "valor_texto": "João da Silva"
    },
    {
      "modelo_campo_id": 101,
      "valor_texto": "123.456.789-00"
    }
  ],
  "documentos": [
    {
      "modelo_documento_id": 50,
      "arquivo_temp_id": "tmp_abc123"
    }
  ]
}


Obs: arquivo_temp_id pode ser o ID retornado por uma API de upload temporário.

Resposta 201

{
  "id": 900,
  "status": "ABERTO",
  "data_abertura": "2025-11-15T10:20:00"
}

📌 4.2 – Listar pedidos (interno ou cliente)
GET /pedidos

Query Params

Param	Tipo	Descrição
empresa_id	number	Filtrar por empresa
usuario_cliente_id	number	Pedidos abertos por esse usuário
usuario_responsavel_id	number	Pedidos atribuídos a um analista
status	string	ABERTO, EM_ANALISE, AGUARDANDO_CLIENTE, etc.
modelo_pedido_id	number	Tipo de pedido
page	number	Paginação

Resposta

{
  "data": [
    {
      "id": 900,
      "empresa": "Padaria Alfa",
      "modelo_pedido": "Alteração de contrato social",
      "status": "EM_ANALISE",
      "prioridade": "alta",
      "data_abertura": "2025-11-15T10:20:00"
    }
  ],
  "pagination": {
    "page": 1,
    "last_page": 3
  }
}

📌 4.3 – Detalhar um pedido
GET /pedidos/{id}

Resposta 200

{
  "id": 900,
  "empresa_id": 55,
  "modelo_pedido": "Alteração de contrato social",
  "status": "EM_ANALISE",
  "prioridade": "alta",
  "data_abertura": "2025-11-15T10:20:00",
  "data_fechamento": null,
  "usuario_cliente": {
    "id": 200,
    "nome": "João – Financeiro"
  },
  "usuario_responsavel": {
    "id": 8,
    "nome": "Carlos – Societário"
  },
  "campos_respostas": [
    {
      "modelo_campo_id": 100,
      "nome_campo": "Nome do sócio",
      "tipo_campo": "texto",
      "valor_texto": "João da Silva"
    }
  ],
  "documentos_enviados": [
    {
      "id": 3000,
      "modelo_documento_id": 50,
      "descricao_modelo": "RG do sócio",
      "nome_arquivo": "rg-joao.png",
      "caminho_arquivo": "s3://.../rg-joao.png",
      "data_envio": "2025-11-15T10:21:00"
    }
  ],
  "historico": [...]
}

5. Campos e Respostas
📌 5.1 – Adicionar/atualizar respostas (interno ou cliente)
PUT /pedidos/{id}/campos

Permite incluir/atualizar respostas de campos de um pedido, útil em fluxos mais complexos.

Body

{
  "campos": [
    {
      "modelo_campo_id": 102,
      "valor_data": "2025-12-01"
    },
    {
      "modelo_campo_id": 103,
      "valor_numero": 50000.0
    }
  ]
}


Resposta

{
  "pedido_id": 900,
  "atualizado": true
}

6. Documentos do Pedido
📌 6.1 – Upload de documento atrelado ao pedido
POST /pedidos/{id}/documentos

multipart/form-data

Campo	Descrição
arquivo	Arquivo binário
modelo_documento_id	ID do documento definido no modelo
usuario_cliente_id	ID do usuário que está enviando

Resposta 201

{
  "id": 3000,
  "nome_arquivo": "rg-joao.png",
  "data_envio": "2025-11-15T10:21:00"
}

📌 6.2 – Remover documento
DELETE /pedidos/documentos/{id}

Restrito a casos onde o documento ainda não foi usado em uma etapa crítica do fluxo.

7. Status do Pedido & Histórico
📌 7.1 – Atualizar status do pedido
PATCH /pedidos/{id}/status

Body

{
  "status": "AGUARDANDO_CLIENTE",
  "motivo": "Falta enviar comprovante de residência do novo sócio"
}


Status sugeridos:

ABERTO

EM_ANALISE

AGUARDANDO_CLIENTE

CONCLUIDO

CANCELADO

Resposta

{
  "id": 900,
  "status": "AGUARDANDO_CLIENTE",
  "data_status": "2025-11-16T09:10:00"
}

📌 7.2 – Histórico do pedido
GET /pedidos/{id}/historico

Resposta

[
  {
    "status_anterior": null,
    "status_novo": "ABERTO",
    "data_alteracao": "2025-11-15T10:20:00",
    "usuario": "João – Cliente"
  },
  {
    "status_anterior": "ABERTO",
    "status_novo": "EM_ANALISE",
    "data_alteracao": "2025-11-15T11:00:00",
    "usuario": "Carlos – Societário"
  }
]

8. Códigos de erro
Código	Significado
400	Requisição inválida
401	Não autenticado
403	Permissão negada
404	Pedido ou modelo não encontrado
409	Conflito (ex.: pedido em estado que não permite alteração)
422	Erro de validação (campo obrigatório não preenchido etc.)
500	Erro interno
9. Integração com outros módulos
Módulo	Integração
Documentos	Reaproveitar documentos enviados em pedidos para outras solicitações
Tarefas	Cada pedido pode gerar uma tarefa interna atribuída a um colaborador
Guias	Pedidos podem solicitar revisão/ajuste de tributos e gerar novas guias
Notificações	E-mails/WhatsApp de atualização de status de pedido