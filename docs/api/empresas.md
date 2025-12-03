# API Reference – Empresas & Configurações (v1)

Esta API cobre tudo relacionado à gestão de empresas atendidas pelo escritório contábil:

- Cadastro de empresas
- Dados fiscais e operacionais
- Responsáveis internos por departamento
- Vínculos de usuários cliente ↔ empresa
- Parâmetros de configuração por empresa
- Tags e classificações
- Ativação / inativação

Prefixo das rotas:

/api/v1


---

# 1. Conceitos

### Empresa
CNPJ atendido pelo escritório, com características fiscais e operacionais próprias.

### Usuários Cliente da Empresa
Usuários do portal vinculados exclusivamente a esta empresa.

### Responsáveis por Departamento
Para cada empresa, definimos quem do escritório é o responsável por:

- Fiscal
- Contábil
- DP

Isso interfere diretamente em:

- tarefas geradas,
- validações de documentos,
- atendimento de pedidos,
- visibilidade no painel interno.

---

# 2. Endpoints

---

# 📌 2.1 – Listagem de Empresas

### **GET /empresas**

Lista empresas atendidas pelo escritório, com filtros.

**Query Params**

| Campo | Tipo | Descrição |
|-------|------|-----------|
| ativo | boolean | filtrar por ativo/inativo |
| nome | string | busca por razão social/fantasia |
| cnpj | string | busca exata |
| tag | string | ex.: `simples`, `lucro-real`, `societario` |
| departamento_id | number | empresas atendidas pelo depto |
| responsavel_escritorio_id | number | responsável interno |

**Resposta 200**

```json
{
  "data": [
    {
      "id": 55,
      "razao_social": "Padaria Alfa LTDA",
      "nome_fantasia": "Padaria Alfa",
      "cnpj": "12.345.678/0001-99",
      "regime_tributario": "SN",
      "ativo": true,
      "tags": ["comercio", "simples"],
      "responsaveis": {
        "fiscal": { "id": 8, "nome": "Carlos Fiscal" },
        "contabil": { "id": 12, "nome": "Paula Contábil" },
        "dp": { "id": 20, "nome": "Marcos DP" }
      }
    }
  ],
  "pagination": {
    "page": 1,
    "last_page": 2
  }
}

📌 2.2 – Criar Empresa
POST /empresas

Body

{
  "razao_social": "Padaria Alfa LTDA",
  "nome_fantasia": "Padaria Alfa",
  "cnpj": "12.345.678/0001-99",
  "inscricao_estadual": "123456789",
  "inscricao_municipal": "555444",
  "regime_tributario": "SN",
  "telefone": "(82) 99999-0000",
  "email": "contato@padariaalfa.com",
  "endereco": {
    "rua": "Rua X",
    "numero": "100",
    "bairro": "Centro",
    "cidade": "Maceió",
    "estado": "AL",
    "cep": "57000-000"
  },
  "responsaveis": {
    "fiscal": 8,
    "contabil": 12,
    "dp": 20
  },
  "tags": ["comercio", "simples"]
}


Resposta 201

{
  "id": 55,
  "ativo": true
}

📌 2.3 – Obter detalhes da empresa
GET /empresas/{id}

Resposta 200

{
  "id": 55,
  "razao_social": "Padaria Alfa LTDA",
  "cnpj": "12.345.678/0001-99",
  "regime_tributario": "SN",
  "ativo": true,
  "dados_contato": {...},
  "responsaveis": {...},
  "usuarios_cliente": [...],
  "configuracoes": {
    "envio_lembrete_documentos": true,
    "envio_lembrete_guias": true,
    "envio_lembrete_pedidos": false
  }
}

📌 2.4 – Atualizar empresa
PATCH /empresas/{id}

Body (exemplo)

{
  "telefone": "(82) 98888-0000",
  "regime_tributario": "LP",
  "tags": ["restaurante", "lucro-presumido"]
}

📌 2.5 – Ativar / Inativar empresa
PATCH /empresas/{id}/status

Body

{
  "ativo": false
}


Resposta

{
  "id": 55,
  "ativo": false
}

📌 2.6 – Definir responsáveis da empresa
PATCH /empresas/{id}/responsaveis

Body

{
  "fiscal": 8,
  "contabil": 12,
  "dp": 20
}


Resposta

{
  "empresa_id": 55,
  "responsaveis": {
    "fiscal": 8,
    "contabil": 12,
    "dp": 20
  }
}

3. Vínculo com Usuários Cliente
📌 3.1 – Listar usuários cliente de uma empresa
GET /empresas/{empresa_id}/usuarios-cliente

Resposta

[
  {
    "id": 200,
    "nome": "João – Financeiro",
    "email": "financeiro@padariaalfa.com",
    "perfil": "cliente_financeiro",
    "ativo": true
  }
]

📌 3.2 – Criar usuário cliente para empresa
POST /empresas/{empresa_id}/usuarios-cliente

Body

{
  "nome": "João – Financeiro",
  "email": "financeiro@padariaalfa.com",
  "telefone": "(82) 93333-0000",
  "cargo": "Financeiro",
  "perfil": "cliente_financeiro"
}

4. Configurações por Empresa
📌 4.1 – Obter configurações
GET /empresas/{id}/configuracoes

Resposta

{
  "empresa_id": 55,
  "envio_lembrete_documentos": true,
  "envio_lembrete_guias": true,
  "envio_lembrete_pedidos": false,
  "dias_antecipacao_guias": 3,
  "dias_antecipacao_documentos": 2
}

📌 4.2 – Atualizar configurações
PATCH /empresas/{id}/configuracoes

Body

{
  "envio_lembrete_guias": false,
  "dias_antecipacao_guias": 1
}

5. Eventos de Negócio ligados à Empresa

A API de empresa é ponto central para disparar:

tarefas automáticas,

solicitações automáticas de documentos,

geração de guias,

notificações.

Exemplos:

Evento: novo responsável fiscal

Atualização em:

PATCH /empresas/{id}/responsaveis


Pode disparar:

Reatribuição de tarefas futuras,

Reatribuição de validações pendentes.

6. Códigos de Erro
Código	Significado
400	Erro de validação
401	Não autenticado
403	Sem permissão
404	Empresa não encontrada
409	Conflito (CNPJ duplicado, usuário já vinculado)
422	Dados inválidos
500	Erro interno
7. Integrações com outros módulos
Módulo	Como se relaciona
Documentos	Solicitações automáticas dependem de empresa + modelo de documento
Obrigações & Guias	Empresa define regras fiscais individuais
Tarefas & Agenda	Tarefas são sempre vinculadas a empresa + responsável
Pedidos	Cada pedido pertence a uma empresa
Notificações	Configurações de envio dependem da empresa