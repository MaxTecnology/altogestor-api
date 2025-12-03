# API Reference – Documentos (v1)

A API de Documentos é responsável pelo fluxo de **solicitação**, **envio**, **validação**, **histórico** e **envio ao cliente** de arquivos/documentos entre o cliente e o escritório contábil.

---

# 1. Autenticação

Todas as requisições utilizam autenticação via token:

Authorization: Bearer <token>
Tokens podem ser:

- JWT do usuário (cliente/escritório)
- Token de integração (API Key)
- Token administrativo

---

# 2. Versão da API

Prefixo padrão:

/api/v1


---

# 3. Recursos da API

A API é organizada em 5 blocos:

| Bloco | Descrição |
|--------|-----------|
| **Solicitações** | Criação/leitura de solicitações de documentos |
| **Documentos** | Upload, listagem e exclusão |
| **Validação** | Análise interna (escritório) |
| **Histórico** | Auditoria de mudanças |
| **Envios** | Envio de guias/dados/documentos ao cliente |

---

# 4. Endpoints

---

## 📌 4.1 – Solicitações de Documentos

### **GET /solicitacoes**

Lista solicitações conforme filtros.

**Query Params**

| Param | Tipo | Descrição |
|--------|--------|-----------|
| empresa_id | number | Filtra pela empresa |
| departamento_id | number | Filtra por departamento |
| periodo | string | Ex.: `2025-10` |
| status | string | Ex.: `PENDENTE`, `PARCIAL` |
| page | number | Paginação |

**Resposta 200**

```json
{
  "data": [
    {
      "id": 10,
      "modelo_documento": "XML de Saídas",
      "empresa": "Padaria Alfa",
      "periodo_referencia": "2025-10",
      "data_limite": "2025-11-08T23:59:59",
      "status": "PARCIAL"
    }
  ],
  "pagination": {
    "page": 1,
    "last_page": 4
  }
}

POST /solicitacoes

Cria uma solicitação manual.

{
  "modelo_documento_id": 1,
  "empresa_id": 55,
  "departamento_id": 2,
  "periodo_referencia": "2025-10",
  "data_limite": "2025-11-07T23:59:59"
}

Resposta 201

{
  "id": 120,
  "status": "PENDENTE"
}

GET /solicitacoes/{id}

Retorna todos os detalhes da solicitação.

Resposta 200

{
  "id": 120,
  "modelo_documento": "XML de Saídas",
  "empresa_id": 55,
  "departamento_id": 2,
  "status": "EM_VALIDACAO",
  "periodo_referencia": "2025-10",
  "data_limite": "2025-11-07T23:59:59",
  "documentos_enviados": [...],
  "historico": [...]
}

📌 4.2 – Documentos Enviados (Upload)
POST /documentos/upload

Upload de arquivos pelo cliente.

Multipart Form Fields

Campo	Tipo	Descrição
arquivo	file	Arquivo enviado
solicitacao_documento_id	number	Solicitação vinculada
usuario_cliente_id	number	Quem enviou
origem	string	portal, integracao, importacao

Resposta 201

{
  "id": 455,
  "nome_arquivo": "vendas_202510.xml",
  "tipo": "xml",
  "data_envio": "2025-11-10T12:05:00"
}

GET /documentos/{id}

Retorna metadados do documento enviado.

DELETE /documentos/{id}

Remove um documento somente se ainda não tiver sido validado.

📌 4.3 – Validação (Escritório)
POST /validacao/solicitacao/{id}

Escritório altera o status de uma solicitação.

Body

{
  "status": "RECUSADO",
  "motivo": "Documento ilegível"
}


Status possíveis

PENDENTE

PARCIAL

EM_VALIDACAO

COMPLETO

INCOMPLETO

RECUSADO

Resposta

{
  "status": "RECUSADO",
  "data_status": "2025-11-10T12:50:21"
}

📌 4.4 – Histórico
GET /historico/solicitacao/{id}

Retorna todas as alterações de estado da solicitação.

Resposta

[
  {
    "estado_anterior": null,
    "estado_novo": "PENDENTE",
    "data_alteracao": "2025-10-01T00:00:00",
    "usuario": "Gerador automático"
  },
  {
    "estado_anterior": "PARCIAL",
    "estado_novo": "EM_VALIDACAO",
    "usuario": "Carlos – Fiscal"
  }
]

📌 4.5 – Envio de Documentos ao Cliente (Escritório → Cliente)
POST /envios

Body

{
  "empresa_id": 55,
  "usuario_escritorio_id": 8,
  "usuario_cliente_id": 200,
  "tipo_recurso": "guia_fiscal",
  "recurso_id": 90,
  "canal_envio": "email",
  "assunto": "Guia DAS – 10/2025",
  "mensagem_resumo": "Segue em anexo a guia com vencimento em 20/11."
}


Resposta 201

{
  "status_envio": "enviado",
  "detalhe_status": "OK - provider response id #AF122"
}

5. Códigos de erro
Código	Significado
400	Requisição inválida
401	Não autenticado
403	Sem permissão
404	Registro não encontrado
409	Conflito (ex.: solicitação já existe)
422	Erro de validação
500	Erro inesperado
6. Schemas
Solicitação
{
  "id": 100,
  "modelo_documento_id": 1,
  "empresa_id": 55,
  "periodo_referencia": "2025-10",
  "status": "PARCIAL"
}

Documento enviado
{
  "id": 455,
  "solicitacao_documento_id": 100,
  "usuario_cliente_id": 200,
  "nome_arquivo": "vendas.xml",
  "origem": "portal",
  "data_envio": "2025-11-10T10:00:00"
}

Histórico
{
  "estado_anterior": "PENDENTE",
  "estado_novo": "PARCIAL",
  "usuario": "João – Contábil"
}

7. Endpoints futuros (planejados)

POST /solicitacoes/{id}/reenviar-lembrete

GET /documentos/{id}/download

POST /solicitacoes/lote

POST /documentos/analise-automatica

8. Observações

Todas as datas devem ser enviadas em UTC ou com timezone explícito.

Payloads grandes (upload) devem seguir limite configurado pelo servidor.

Toda alteração de status gera entrada no histórico.

9. Integração com outros módulos
Módulo	Interação
Guias Fiscais	Envio de guias ao cliente; comprovação; anexos
Tarefas	Conclusão automática quando documentos forem completos
Pedidos	Pedido pode exigir documentos; reutilização de anexo
Notificações	E-mail/WhatsApp/SMS sobre pendências


