# API Reference – Visão Geral

Este diretório concentra a documentação de todas as APIs da plataforma de:

- Gestão de Documentos
- Obrigações & Guias Fiscais
- Tarefas & Agenda
- Pedidos & Formulários
- Notificações
- (Futuro) Usuários, Autenticação, Dashboard, Integrações

A proposta é ter uma documentação **clara, modular e amigável à IA** (Codex, GPT etc.), permitindo geração automática de:

- controllers,
- rotas,
- DTOs,
- testes automatizados,
- SDKs.

---

## 1. Padrões Gerais

### 1.1. Versão

Todas as rotas estão sob o prefixo:

```http
/ api / v1

1.2. Autenticação

Autenticação via header:

Authorization: Bearer <token>


Tipos de token:

usuário do escritório (painel interno),

usuário do cliente (portal),

token técnico (integração / service account).

1.3. Formato das Respostas

Padrão JSON.

Em listagens, priorizamos o padrão:

{
  "data": [...],
  "pagination": {
    "page": 1,
    "last_page": 5
  }
}

1.4. Códigos de Status HTTP

Uso padrão:

200 OK

201 Criado

204 Sem conteúdo

400 Requisição inválida

401 Não autenticado

403 Sem permissão

404 Não encontrado

409 Conflito de negócio

422 Erro de validação

500 Erro interno

2. Módulos de API
2.1. Documentos

Arquivo: documentos.md

Abrange:

Solicitações de documentos por empresa/período;

Upload de documentos pelo cliente;

Validação pelo escritório;

Histórico de status;

Envios ao cliente (links, anexos).

2.2. Obrigações & Guias

Arquivo: obrigacoes-guias.md

Abrange:

Cadastro de tipos de obrigação (DAS, DEFIS, ISS etc.);

Configuração por empresa (dia limite, responsável, gera guia no sistema);

CRUD de guias fiscais;

Upload de comprovantes;

Vinculação entre guias e documentos;

Envio das guias ao cliente.

2.3. Tarefas & Agenda

Arquivo: tarefas-agenda.md

Abrange:

Modelos de tarefas (regras de geração);

Tarefas por competência/empresa/responsável;

Atualização de status (workflow da tarefa);

Histórico de tarefas;

Visões de calendário e relatórios de produtividade.

2.4. Pedidos & Formulários

Arquivo: pedidos-formularios.md

Abrange:

Modelos de pedido com campos dinâmicos;

Documentos obrigatórios no modelo;

Abertura de pedidos pelo cliente;

Respostas de campos;

Upload de documentos;

Histórico de status.

2.5. Notificações

Arquivo: notificacoes.md

Abrange:

Abstração de envio (e-mail, WhatsApp, SMS);

Notificações transacionais (guia gerada, docs pendentes, pedido atualizado);

Logs de envio/mapa de falhas;

Integração futura com provedores externos (ASAAS, Twilio, Z-API etc. se aplicar).