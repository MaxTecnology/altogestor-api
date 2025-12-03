# API Reference – Obrigações e Guias Fiscais (v1)

Este módulo controla:

- Tipos de Obrigações (DAS, DEFIS, ISS, DCTF, etc.)
- Configurações por empresa
- Geração e gestão de guias fiscais
- Envio de guias ao cliente
- Upload de comprovantes de pagamento
- Vinculação de guias a documentos
- Auditoria e rastreamento (status e log)

A API segue o padrão REST com prefixo:

/api/v1


---

# 1. Autenticação

Todas as requisições usam:



Authorization: Bearer <token>


---

# 2. Estrutura de Recursos

| Recurso | Descrição |
|--------|-----------|
| `/obrigacoes/tipos` | Cadastro e listagem de tipos de obrigação |
| `/obrigacoes/configuracoes` | Configuração por empresa |
| `/guias` | CRUD de guias fiscais |
| `/guias/comprovantes` | Upload e gestão de comprovantes |
| `/guias/vinculos` | Relaciona guias e solicitações de documentos |
| `/guias/envios` | Envio ao cliente (e-mail, WhatsApp, portal) |

---

# 3. Endpoints

---

# 📌 3.1 – Tipos de Obrigações

### **GET /obrigacoes/tipos**

Lista todos os tipos de obrigação disponíveis.

**Resposta 200**

```json
[
  {
    "id": 1,
    "nome": "DAS",
    "departamento_id": 2,
    "periodicidade": "mensal",
    "tipo_imposto": "federal",
    "ativo": true
  }
]

POST /obrigacoes/tipos

Cria um novo tipo de obrigação.

Body

{
  "nome": "DEFIS",
  "descricao": "Declaração anual do Simples Nacional",
  "departamento_id": 1,
  "periodicidade": "anual",
  "tipo_imposto": "federal"
}


Resposta 201

{
  "id": 8,
  "nome": "DEFIS",
  "ativo": true
}

PUT /obrigacoes/tipos/{id}
DELETE /obrigacoes/tipos/{id}

Desabilita o tipo, não remove fisicamente.

📌 3.2 – Configurações de Obrigações por Empresa
GET /obrigacoes/configuracoes

Lista todas as configurações da empresa.

Query Params

Param	Descrição
empresa_id	filtro obrigatório

Resposta

[
  {
    "id": 11,
    "empresa_id": 55,
    "tipo_obrigacao_id": 1,
    "dia_limite_padrao": 20,
    "responsavel_departamento_id": 8,
    "gera_guia_no_sistema": true,
    "ativo": true
  }
]

POST /obrigacoes/configuracoes

Body

{
  "empresa_id": 55,
  "tipo_obrigacao_id": 1,
  "dia_limite_padrao": 20,
  "responsavel_departamento_id": 8,
  "gera_guia_no_sistema": true
}


Resposta

{
  "id": 11,
  "ativo": true
}

📌 3.3 – Guias Fiscais
GET /guias

Listagem paginada.

Query Params

Campo	Descrição
empresa_id	obrigatorio
tipo_obrigacao_id	opcional
competencia	opcional
status	opcional
vencimento_de	intervalo
vencimento_ate	intervalo

Resposta

{
  "data": [
    {
      "id": 90,
      "empresa_id": 55,
      "tipo_obrigacao_id": 1,
      "competencia": "2025-10",
      "data_vencimento": "2025-11-20T00:00:00",
      "valor_total": 455.23,
      "status_guia": "ENVIADA_CLIENTE"
    }
  ],
  "pagination": {
    "page": 1,
    "last_page": 2
  }
}

POST /guias

Cria uma guia fiscal manualmente.

Body

{
  "empresa_id": 55,
  "tipo_obrigacao_id": 1,
  "competencia": "2025-10",
  "data_vencimento": "2025-11-20",
  "valor_principal": 455.23
}


Resposta 201

{
  "id": 90,
  "status_guia": "GERADA_INTERNA"
}

GET /guias/{id}

Resposta

{
  "id": 90,
  "empresa_id": 55,
  "tipo_obrigacao": "DAS",
  "competencia": "2025-10",
  "data_vencimento": "2025-11-20",
  "valores": {
    "principal": 455.23,
    "juros": 0,
    "multa": 0,
    "total": 455.23
  },
  "status": "ENVIADA_CLIENTE",
  "comprovantes": [...],
  "vinculos_documentos": [...]
}

PATCH /guias/{id}/status

Body

{
  "status": "PAGA"
}


Resposta

{
  "id": 90,
  "status": "PAGA",
  "data_status": "2025-11-15T15:22:00"
}

📌 3.4 – Comprovantes de Pagamento
POST /guias/{id}/comprovantes

Multipart Form:

Campo	Tipo
arquivo	file
usuario_cliente_id	number
observacoes	string (opcional)

Resposta 201

{
  "id": 500,
  "nome_arquivo": "comprovante.pdf",
  "data_envio": "2025-11-12T09:11:00"
}

DELETE /guias/comprovantes/{id}

Remove se a guia ainda não estiver marcada como validada.

📌 3.5 – Vincular Guia → Documentos
POST /guias/{id}/vinculos

Body

{
  "solicitacao_documento_id": 120,
  "tipo_vinculo": "base_apuracao"
}

DELETE /guias/vinculos/{id}
📌 3.6 – Envio de Guias ao Cliente
POST /guias/envios

Body

{
  "empresa_id": 55,
  "usuario_escritorio_id": 8,
  "usuario_cliente_id": 200,
  "guia_id": 90,
  "canal_envio": "email",
  "assunto": "Guia DAS – 10/2025",
  "mensagem_resumo": "Segue em anexo a guia com vencimento em 20/11."
}


Resposta

{
  "status_envio": "enviado",
  "detalhe_status": "OK - provider message id #AFA22"
}

4. Status Machine – Guias
Status	Significado
GERADA_INTERNA	Criada pelo escritório
IMPORTADA	Importada via integração
ENVIADA_CLIENTE	Cliente recebeu
VISUALIZADA	Cliente visualizou
PAGA	Cliente confirmou pagamento
ATRASADA	Passou do vencimento
CANCELADA	Guia cancelada
5. Códigos de Erro
Código	Significado
400	Erro de entrada
401	Não autenticado
403	Permissão negada
404	Não encontrado
409	Conflito (ex.: guia já existe)
422	Validação
500	Erro interno no servidor
6. Integração com outros módulos
Módulo	Como se relaciona
Documentos	Guias podem depender de documentos completos
Tarefas	Tarefa “Gerar DAS” → cria guia
Pedidos	Pedido pode anexar guias
Notificações	Email e WhatsApp para envio de guia
Dashboard	Métricas por competência, atrasos, SLA