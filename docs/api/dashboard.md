# API Reference – Dashboard (v1)

O Dashboard é a visão consolidada usada tanto pelo **escritório** quanto pelo **cliente**.  
Ele reúne informações de:

- Obrigações & Guias  
- Documentos & Solicitações  
- Tarefas & Agenda  
- Pedidos & Atendimentos  
- Notificações  
- Indicadores operacionais

Cada usuário enxerga **somente o que tem permissão**, conforme a matriz de permissões.

Prefixo das rotas:

/api/v1/dashboard


---

# 1. Tipos de Dashboard

Existem duas visões:

### 1. Dashboard Escritório
Usado por:
- sócios,
- gestores,
- analistas.

Traz:

- empresas com pendências,
- documentos não enviados,
- solicitações atrasadas,
- guias vencendo hoje,
- tarefas atrasadas,
- desempenho da equipe,
- volume por departamento.

---

### 2. Dashboard Cliente (Portal)
Usado por:
- cliente_admin,
- cliente_financeiro,
- cliente_basico.

Traz:

- guias pendentes do mês,
- documentos solicitados,
- documentos enviados / faltantes,
- pedidos abertos,
- notificações recebidas,
- resumo de obrigações.

---

# 2. Endpoints

---

# 📌 2.1 – Dashboard do Escritório

## **GET /dashboard/escritorio**

Retorna indicadores globais para o escritório, respeitando o departamento e empresas do usuário.

### Exemplo de Resposta

```json
{
  "pendencias": {
    "documentos_pendentes": 32,
    "documentos_atrasados": 14,
    "guias_vencendo_hoje": 6,
    "guias_atrasadas": 3,
    "tarefas_hoje": 21,
    "tarefas_atrasadas": 8,
    "pedidos_em_analise": 11
  },

  "empresas_com_mais_pendencias": [
    {
      "empresa_id": 55,
      "nome": "Padaria Alfa LTDA",
      "documentos_pendentes": 5,
      "tarefas_atrasadas": 2,
      "guias_vencidas": 1
    },
    {
      "empresa_id": 72,
      "nome": "Distribuidora Maciel",
      "documentos_pendentes": 4,
      "tarefas_atrasadas": 1
    }
  ],

  "tarefas_por_colaborador": [
    {
      "usuario_id": 8,
      "nome": "Carlos Fiscal",
      "tarefas_hoje": 7,
      "tarefas_atrasadas": 3
    },
    {
      "usuario_id": 12,
      "nome": "Ana Contábil",
      "tarefas_hoje": 4,
      "tarefas_atrasadas": 1
    }
  ]
}

📌 2.2 – Dashboard do Cliente
GET /dashboard/cliente

Retorna visão consolidada para o portal do cliente.

Exemplo de Resposta
{
  "empresa_id": 55,
  "resumo": {
    "guias_pendentes": 2,
    "guias_vencidas": 1,
    "documentos_pendentes": 6,
    "pedidos_abertos": 3,
    "notificacoes_nao_lidas": 4
  },
  "proximos_vencimentos": [
    {
      "tipo_obrigacao": "DAS",
      "competencia": "2025-10",
      "vencimento": "2025-11-20",
      "status": "PENDENTE"
    },
    {
      "tipo_obrigacao": "ISS",
      "competencia": "2025-10",
      "vencimento": "2025-11-15",
      "status": "PENDENTE"
    }
  ]
}

📌 2.3 – Indicadores de Obrigações
GET /dashboard/escritorio/obrigacoes

Retorna estatísticas agregadas das obrigações fiscais por período.

Query Params

mes (YYYY-MM)

departamento (fiscal/contabil/dp)

Exemplo de Resposta
{
  "total_obrigacoes": 120,
  "pendentes": 40,
  "em_andamento": 50,
  "concluidas": 25,
  "atrasadas": 5,
  "por_tipo": [
    { "tipo": "DAS", "pendentes": 10, "concluidas": 20, "atrasadas": 1 },
    { "tipo": "ISS", "pendentes": 6, "concluidas": 8, "atrasadas": 0 }
  ]
}

📌 2.4 – Indicadores de Documentos
GET /dashboard/escritorio/documentos

Retorna dados de solicitações/documentos.

Resposta
{
  "documentos_solicitados_mes": 95,
  "documentos_enviados": 62,
  "documentos_validos": 56,
  "documentos_recusados": 8,
  "documentos_atrasados": 14,
  "por_departamento": {
    "fiscal": 40,
    "contabil": 30,
    "dp": 25
  }
}

📌 2.5 – Indicadores de Tarefas
GET /dashboard/escritorio/tarefas

Dados de produtividade e pendências por colaborador.

Resposta
{
  "tarefas_geradas_mes": 210,
  "tarefas_concluidas": 170,
  "tarefas_pendentes": 40,
  "tarefas_atrasadas": 15,
  "por_colaborador": [
    {
      "usuario_id": 8,
      "nome": "Carlos Fiscal",
      "concluidas": 35,
      "pendentes": 5,
      "atrasadas": 2
    }
  ]
}

📌 2.6 – Indicadores de Pedidos (Cliente → Escritório)
GET /dashboard/escritorio/pedidos
Resposta
{
  "pedidos_mes": 42,
  "abertos": 10,
  "em_analise": 18,
  "aguardando_cliente": 6,
  "concluidos": 8,
  "por_tipo": [
    { "modelo": "Abertura de Empresa", "quantidade": 5 },
    { "modelo": "Balanço Patrimonial", "quantidade": 8 }
  ]
}

📌 2.7 – Indicadores do Cliente (detalhados)
GET /dashboard/cliente/detalhado

Mais granular que o dashboard simples do cliente.

Resposta
{
  "documentos": {
    "pendentes": 6,
    "parciais": 1,
    "completos": 4,
    "atrasados": 2
  },
  "guias": {
    "pendentes": 2,
    "vencidas": 1,
    "pagas": 8
  },
  "tarefas": {
    "abertas": 3,
    "aguardando_cliente": 2
  },
  "pedidos": {
    "abertos": 2,
    "aguardando_cliente": 1,
    "concluidos": 6
  }
}

3. Segurança & Permissões

Todas as rotas do dashboard seguem automaticamente:

o tipo de usuário (escritório/cliente);

o perfil (role);

empresas vinculadas ao usuário;

departamentos responsáveis (para analistas).

Exemplos:

Analista Fiscal só vê empresas do fiscal.

Cliente Admin só vê indicadores da própria empresa.

Sócio Admin vê tudo.

4. Eventos e Performance

Por ser uma área altamente acessada, recomenda-se:

cache de 30s–60s para indicadores,

agregações SQL otimizadas,

índices nas tabelas de:

guias,

solicitações,

tarefas,

pedidos,

documentos.

Sugestão para backend:

criar um service DashboardAggregatorService

gerar queries especializadas

utilizar materialized views (em PostgreSQL) se necessário

5. Relacionamento com outros módulos
Módulo	Como afeta o Dashboard
Documentos	pendências, atrasados, validados
Obrigações/Guias	vencimentos, enviadas, pagas
Tarefas	produtividade e atrasos
Pedidos	volume operacional
Empresa	filtros, responsáveis e tags
Notificações	alertas da plataforma
Conclusão

Este documento define todas as rotas do Dashboard, cobrindo:

indicadores do escritório,

indicadores do cliente,

métricas operacionais,

produtividade,

pendências,

atrasos,

consolidação multi-módulo.

Esses endpoints são essenciais para gerar:

o painel do escritório,

o portal do cliente,

widgets do sistema,

relatórios gerenciais.