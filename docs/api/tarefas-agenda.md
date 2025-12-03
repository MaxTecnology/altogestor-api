# API Reference – Tarefas e Agenda (v1)

O módulo de Tarefas & Agenda concentra:

- Modelos de tarefas (regra de geração automática)
- Tarefas concretas por competência
- Atualização de status
- Histórico de alterações
- Calendário para os colaboradores
- Consolidação e SLA

Prefixo:

/api/v1


---

# 1. Autenticação



Authorization: Bearer <token>


---

# 2. Recursos da API

| Recurso | Descrição |
|---------|-----------|
| `/tarefas/modelos` | Modelos de tarefas (configurações) |
| `/tarefas` | Tarefas geradas automaticamente ou manualmente |
| `/tarefas/status` | Atualização do status |
| `/tarefas/historico` | Histórico de alterações |
| `/tarefas/calendario` | Visões de agenda, filtros, produtividade |

---

# 3. Endpoints

---

# 📌 3.1 – Modelos de Tarefas (Configurações)

### **GET /tarefas/modelos**

Lista modelos de tarefa existentes.

**Query Params**

| Param | Descrição |
|--------|-----------|
| departamento_id | Opcional |
| tipo_obrigacao_id | Opcional |
| ativo | Opcional |

**Resposta**

```json
[
  {
    "id": 1,
    "nome": "Apurar DAS ME",
    "departamento_id": 2,
    "tipo_obrigacao_id": 1,
    "frequencia": "mensal",
    "tipo_referencia_data": "vencimento_guia",
    "offset_dias": -3,
    "usar_dia_util": true,
    "ativo": true
  }
]

POST /tarefas/modelos

Cria um modelo de tarefa.

Body

{
  "nome": "Conferir folha de pagamento",
  "departamento_id": 1,
  "tipo_obrigacao_id": 14,
  "frequencia": "mensal",
  "tipo_referencia_data": "dia_fixo",
  "dia_fixo": 25,
  "usar_dia_util": true
}


Resposta

{
  "id": 55,
  "ativo": true
}

PATCH /tarefas/modelos/{id}

Atualiza parcialmente o modelo.

DELETE /tarefas/modelos/{id}

Desativa o modelo (não remove).

📌 3.2 – Tarefas de Obrigação (geradas automaticamente)
GET /tarefas

Lista de tarefas, com filtros amplos.

Query Params

Campo	Tipo	Descrição
empresa_id	number	Opcional
responsavel_id	number	Opcional
tipo_obrigacao_id	number	Opcional
competencia	string	2025-10
status	string	EM_ABERTO, EM_ANDAMENTO, CONCLUIDA
data_de	date	Intervalo
data_ate	date	Intervalo
page	number	Paginação

Resposta

{
  "data": [
    {
      "id": 300,
      "empresa": "Padaria Alfa",
      "modelo_tarefa": "Apurar DAS ME",
      "competencia": "2025-10",
      "data_meta": "2025-11-17T00:00:00",
      "status": "EM_ABERTO",
      "responsavel": "Carlos – Fiscal"
    }
  ],
  "pagination": { "page": 1, "last_page": 3 }
}

POST /tarefas

Cria manualmente uma tarefa (útil para exceções).

Body

{
  "empresa_id": 55,
  "modelo_tarefa_id": 14,
  "tipo_obrigacao_id": 1,
  "competencia": "2025-10",
  "responsavel_escritorio_id": 8
}


Resposta

{
  "id": 300,
  "status": "EM_ABERTO"
}

GET /tarefas/{id}

Retorna detalhes completos.

Resposta

{
  "id": 300,
  "empresa_id": 55,
  "modelo_tarefa": "Apurar DAS ME",
  "tipo_obrigacao": "DAS",
  "competencia": "2025-10",
  "data_meta_calculada": "2025-11-17",
  "status": "EM_ABERTO",
  "responsavel": {
    "id": 8,
    "nome": "Carlos – Fiscal"
  },
  "historico": [...]
}

📌 3.3 – Atualização de Status
PATCH /tarefas/{id}/status

Body

{
  "status": "CONCLUIDA",
  "motivo": "Guia enviada e validada"
}


Status válidos

EM_ABERTO

EM_ANDAMENTO

AGUARDANDO_CLIENTE

CONCLUIDA

ATRASADA

CANCELADA

Resposta

{
  "id": 300,
  "status": "CONCLUIDA",
  "data_status": "2025-11-15T12:30:00"
}

📌 3.4 – Histórico de Tarefas
GET /tarefas/{id}/historico

Resposta

[
  {
    "status_anterior": null,
    "status_novo": "EM_ABERTO",
    "usuario": "Gerador automático",
    "data_alteracao": "2025-11-01T00:00:00"
  },
  {
    "status_anterior": "EM_ABERTO",
    "status_novo": "EM_ANDAMENTO",
    "usuario": "Carlos – Fiscal",
    "data_alteracao": "2025-11-10T14:22:00"
  }
]

📌 3.5 – Calendário (Agenda)
GET /tarefas/calendario

Retorna tarefas organizadas por dia, estilo Google Calendar/Gestta.

Query Params

Campo	Descrição
responsavel_id	mostra tarefas de um colaborador
empresa_id	mostra tarefas daquela empresa
mes	ex.: 2025-11
incluir_atrasadas	boolean

Resposta

{
  "dias": {
    "2025-11-17": [
      {
        "id": 300,
        "titulo": "Apurar DAS – Padaria Alfa",
        "status": "EM_ABERTO",
        "tipo_obrigacao": "DAS"
      }
    ]
  }
}

📌 3.6 – SLA e Relatórios
GET /tarefas/relatorios/produtividade

Query Params

Campo	Tipo	Descrição
responsavel_id	number	opcional
periodo_de	date	obrigatório
periodo_ate	date	obrigatório

Resposta

{
  "responsavel": "Carlos – Fiscal",
  "concluidas": 48,
  "atrasadas": 5,
  "pontuacao_total": 188
}

4. Status Machine – Tarefas
Status	Descrição
EM_ABERTO	Tarefa ainda não iniciada
EM_ANDAMENTO	Colaborador iniciou
AGUARDANDO_CLIENTE	Falta documento/informação
CONCLUIDA	Tarefa finalizada
ATRASADA	Passou da data meta
CANCELADA	Cancelada pelo escritório
5. Códigos de Erro
Código	Significado
400	Requisição inválida
401	Não autenticado
403	Acesso negado
404	Tarefa não encontrada
409	Conflito (ex.: tarefa já existe para o período)
422	Erro de validação
500	Erro interno
6. Integrações Diretas
Módulo	Descrição
Obrigações	Tarefa pode ser gerada pelo vencimento da guia
Documentos	Conclusão automática quando os docs da empresa estiverem completos
Notificações	Alertas de tarefas vencendo
Pedidos	Tarefa pode ser criada a partir de pedidos do cliente