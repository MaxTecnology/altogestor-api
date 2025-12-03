---

Agora a API de notificações, que é o “hub” de envio de e-mail/WhatsApp/SMS e se integra com praticamente tudo que você já documentou.

```markdown
# API Reference – Notificações (v1)

O módulo de Notificações é responsável por:

- Enviar mensagens para clientes e usuários internos (e-mail, WhatsApp, etc.);
- Registrar logs de envios e status;
- Padronizar templates de mensagens;
- Servir como camada de abstração para provedores externos.

Prefixo:

```http
/ api / v1

1. Autenticação
Authorization: Bearer <token>
Content-Type: application/json

2. Estrutura de Recursos
Recurso	Descrição
/notificacoes/envios	Envio de notificações avulsas ou associadas a recursos
/notificacoes/templates	Cadastro de templates reutilizáveis
/notificacoes/eventos	Disparo baseado em “eventos de negócio” (ex.: guia gerada)
/notificacoes/logs	Consulta a logs de envio
3. Envios diretos
📌 3.1 – Enviar uma notificação
POST /notificacoes/envios

Envia uma notificação para um destinatário específico.

Body

{
  "empresa_id": 55,
  "usuario_escritorio_id": 8,
  "usuario_cliente_id": 200,
  "canal": "email",
  "destinatario_manual": null,
  "assunto": "Guia DAS – 10/2025",
  "mensagem": "Olá, segue a guia em anexo com vencimento em 20/11.",
  "tipo_recurso": "guia_fiscal",
  "recurso_id": 90,
  "metadados": {
    "competencia": "2025-10"
  }
}


Campos:

Campo	Tipo	Descrição
empresa_id	number	Empresa relacionada ao envio
usuario_escritorio_id	number	Quem disparou o envio
usuario_cliente_id	number | null	Cliente alvo principal
canal	string	email, whatsapp, sms, interno
destinatario_manual	string | null	E-mail/telefone informado manualmente
assunto	string	Assunto (para e-mail)
mensagem	string	Corpo da mensagem
tipo_recurso	string	guia_fiscal, solicitacao_documento, pedido_cliente, etc.
recurso_id	number	ID do recurso
metadados	object	Extra para logs/provedores

Resposta 201

{
  "id": 1000,
  "status_envio": "enviado",
  "detalhe_status": "OK - provider message id #abc123"
}

📌 3.2 – Reenviar notificação
POST /notificacoes/envios/{id}/reenviar

Reenvia com base no registro anterior.

Resposta 201

{
  "id": 1001,
  "status_envio": "enviado"
}

4. Templates de Notificação
📌 4.1 – Listar templates
GET /notificacoes/templates

Resposta

[
  {
    "id": 1,
    "nome": "Aviso de guia gerada",
    "canal": "email",
    "assunto_padrao": "Nova guia disponível",
    "corpo_padrao": "Olá {{nome_cliente}}, uma nova guia está disponível para a competência {{competencia}}."
  }
]

📌 4.2 – Criar template
POST /notificacoes/templates

Body

{
  "nome": "Aviso de documentos pendentes",
  "canal": "email",
  "assunto_padrao": "Documentos pendentes para competência {{competencia}}",
  "corpo_padrao": "Olá {{nome_cliente}}, ainda estão pendentes os documentos: {{lista_documentos}}."
}


Resposta 201

{
  "id": 2,
  "nome": "Aviso de documentos pendentes"
}

📌 4.3 – Enviar usando template
POST /notificacoes/envios/template

Body

{
  "template_id": 1,
  "empresa_id": 55,
  "usuario_escritorio_id": 8,
  "usuario_cliente_id": 200,
  "canal": "email",
  "dados": {
    "nome_cliente": "João",
    "competencia": "2025-10"
  },
  "tipo_recurso": "guia_fiscal",
  "recurso_id": 90
}


Resposta

{
  "id": 1100,
  "status_envio": "enviado"
}

5. Disparo por Eventos de Negócio
📌 5.1 – Disparar evento de notificação
POST /notificacoes/eventos

Endpoint de uso interno pelo backend para centralizar notificações.

Exemplos de eventos:

guia_gerada

documentos_atrasados

pedido_atualizado

tarefa_atrasada

Body

{
  "tipo_evento": "guia_gerada",
  "empresa_id": 55,
  "referencia_id": 90,
  "dados": {
    "competencia": "2025-10",
    "vencimento": "2025-11-20"
  }
}


Resposta

{
  "processado": true,
  "notificacoes_enfileiradas": 3
}


Por trás, este endpoint pode:

escolher templates,

decidir destinatários,

enviar via diversos canais.

6. Logs de Notificação
📌 6.1 – Listar logs
GET /notificacoes/logs

Query Params

Campo	Descrição
empresa_id	Filtrar por empresa
usuario_cliente_id	Filtrar por cliente
tipo_recurso	Filtrar por tipo (guia_fiscal, solicitacao_documento, etc.)
recurso_id	ID do recurso
canal	email, whatsapp, etc.
status_envio	pendente, enviado, erro
data_de	intervalo
data_ate	intervalo

Resposta

{
  "data": [
    {
      "id": 1000,
      "empresa_id": 55,
      "canal": "email",
      "tipo_recurso": "guia_fiscal",
      "recurso_id": 90,
      "status_envio": "enviado",
      "data_envio": "2025-11-10T12:00:00"
    }
  ],
  "pagination": { "page": 1, "last_page": 2 }
}

7. Códigos de Erro
Código	Significado
400	Requisição inválida
401	Não autenticado
403	Sem permissão
404	Registro não encontrado (template, envio, etc.)
409	Conflito de negócio
422	Erro de validação de payload
500	Erro interno ou problema no provedor externo
8. Integrações

Módulos que normalmente utilizam esta API:

Documentos → Lembrar cliente de enviar docs / confirmar recebimento;

Obrigações & Guias → Avisar sobre guias geradas, vencendo ou atrasadas;

Tarefas & Agenda → Alertar responsáveis sobre tarefas próximas do vencimento;

Pedidos → Informar cliente sobre atualização, pendência ou conclusão;

Dashboard → Métricas de comunicação, taxa de abertura (se rastreado), falhas, etc.

9. Possíveis Extensões

Suporte a múltiplos provedores por canal com fallback;

Webhooks de recebimento (ex.: resposta de WhatsApp ou e-mail);

Modelos condicionais por empresa/departamento;

Configuração de “quiet hours” (não enviar notificações em determinados horários).