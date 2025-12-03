# API Reference – Notificações & Templates (v1)

Este documento detalha a API de **notificações** da plataforma, incluindo:

- templates
- canais de envio
- fila de envio
- log de entrega
- associação com eventos de negócio
- preferências por empresa e por usuário

Ele complementa a visão geral de notificações descrita em outros documentos.

Prefixo base:

/api/v1/notificacoes


---

# 1. Conceitos

### Notificação
Mensagem enviada a um destinatário, por um ou mais canais:

- interna (painel)
- e-mail
- WhatsApp (ou outro provedor)
- push (futuro)

### Template
Modelo de notificação com:

- título
- corpo (texto HTML/Markdown)
- variáveis dinâmicas
- canal padrão

### Evento de Negócio
Gatilho que dispara uma ou mais notificações.  
Definidos em: `docs/events/eventos-negocio.md`.

---

# 2. Templates de Notificação

## 2.1. Listar templates

### **GET /notificacoes/templates**

Query params opcionais:

| Campo | Tipo | Descrição |
|--------|------|-----------|
| canal | string | `interna`, `email`, `whatsapp` |
| ativo | boolean | filtrar ativos/deletados |

**Resposta 200**

```json
{
  "data": [
    {
      "id": 10,
      "nome": "Guia Disponível para Pagamento",
      "slug": "guia_disponivel",
      "canal": "email",
      "assunto": "Sua guia {{tipo_obrigacao}} está disponível",
      "corpo": "Olá {{nome_cliente}},\nSua guia {{tipo_obrigacao}} da competência {{competencia}} está disponível...",
      "variaveis": ["nome_cliente", "tipo_obrigacao", "competencia", "vencimento"],
      "ativo": true
    }
  ]
}

2.2. Criar template
POST /notificacoes/templates

Body

{
  "nome": "Guia Disponível para Pagamento",
  "slug": "guia_disponivel",
  "canal": "email",
  "assunto": "Sua guia {{tipo_obrigacao}} está disponível",
  "corpo": "Olá {{nome_cliente}},\nSua guia {{tipo_obrigacao}} da competência {{competencia}} ...",
  "variaveis": ["nome_cliente", "tipo_obrigacao", "competencia", "vencimento"],
  "ativo": true
}


Resposta 201

{
  "id": 10
}

2.3. Atualizar template
PATCH /notificacoes/templates/{id}

Permite alterar:

assunto

corpo

variáveis

ativo/inativo

2.4. Deletar (inativar) template
DELETE /notificacoes/templates/{id}

Marca como inativo (ativo = false), sem excluir fisicamente.

3. Envio de Notificações
3.1. Disparo Manual
POST /notificacoes/enviar

Usado pelo painel do escritório para disparos pontuais.

Body

{
  "empresa_id": 55,
  "usuario_destino_id": 200,
  "template_slug": "guia_disponivel",
  "canal": "email",
  "dados": {
    "nome_cliente": "João",
    "tipo_obrigacao": "DAS",
    "competencia": "2025-10",
    "vencimento": "2025-11-20"
  }
}


Resposta

{
  "notificacao_id": 8801,
  "status": "enfileirada"
}


O envio real é feito via fila, não no thread da requisição.

3.2. Envio disparado por evento de negócio (interno)

Não é exposto como endpoint público.
O backend faz algo como:

NotificacaoService::dispararPorEvento('guia_enviada_ao_cliente', $contexto);


Ele:

Localiza templates associados ao evento

Resolve variáveis

Cria registro de notificação

Enfileira job para envio

4. Notificações Internas (painel)
4.1. Listar notificações do usuário logado
GET /notificacoes/minhas

Query params:

Campo	Tipo	Descrição
lidas	boolean	filtrar por lidas/não lidas
tipo	string	guia, documento, pedido, tarefa

Resposta

{
  "data": [
    {
      "id": 321,
      "titulo": "Guia DAS disponível",
      "mensagem": "Sua guia DAS da competência 2025-10 está disponível.",
      "tipo": "guia",
      "referencia_tipo": "guia",
      "referencia_id": 4510,
      "lida": false,
      "criado_em": "2025-11-10T10:00:00Z"
    }
  ]
}

4.2. Marcar como lida
PATCH /notificacoes/{id}/marcar-lida

Body

{
  "lida": true
}

4.3. Marcar todas como lidas
POST /notificacoes/marcar-todas-lidas

Sem body.

5. Logs de Entrega
5.1. Listar logs de envio
GET /notificacoes/logs

Filtros:

Param	Descrição
empresa_id	filtra por empresa
canal	email/whatsapp/interna
status	enviado/erro/pendente

Resposta

{
  "data": [
    {
      "id": 9001,
      "notificacao_id": 8801,
      "canal": "email",
      "status": "enviado",
      "tentativa": 1,
      "mensagem_erro": null,
      "enviado_em": "2025-11-10T10:01:00Z"
    }
  ]
}

6. Preferências de Notificação
6.1. Preferências por empresa
GET /empresas/{empresa_id}/preferencias-notificacao

Resposta

{
  "empresa_id": 55,
  "email_guias": true,
  "whatsapp_guias": false,
  "email_documentos_pendentes": true,
  "whatsapp_documentos_pendentes": true,
  "email_pedidos": true,
  "whatsapp_pedidos": false
}

PATCH /empresas/{empresa_id}/preferencias-notificacao

Atualiza flags, respeitando LGPD e termos.

6.2. Preferências por usuário
GET /usuarios/{id}/preferencias-notificacao

Permite o usuário escolher se quer/not quer certos avisos.

7. Canais de Notificação
Suportados:

interna → aparece no painel

email → usando SMTP configurado

whatsapp → via provedor externo

multi → mais de um canal ao mesmo tempo

Fila e Retentativas

Sempre enviado por jobs

max_tentativas: 3

backoff exponencial

canais externos (email/whatsapp) com logs de erro detalhados

8. Eventos de Negócio → Notificações

Mapeamento (exemplos):

Evento	Notificação
guia_enviada_ao_cliente	email + interna para cliente
guia_vencida	interna + whatsapp opcional
documento_invalido	email + interna para cliente
documento_enviado	interna para escritório
pedido_aberto	interna para escritório (responsável)
pedido_aguardando_cliente	email/whatsapp para cliente
tarefa_atrasada	interna para responsável + gestor
9. Códigos de Erro
HTTP	Código interno	Descrição
400	NT-001	Variáveis obrigatórias não fornecidas
400	NT-002	Template não suporta o canal informado
404	NT-003	Template não encontrado
404	NT-004	Usuário/empresa não encontrado
500	NT-999	Erro ao enfileirar notificação
10. Conclusão

Este documento detalha a API de notificações com:

templates,

disparos manuais e automáticos,

logs de entrega,

visão interna do usuário,

preferências por usuário e empresa,

integração direta com eventos de negócio.

Ele é base para:

UX do painel,

automação de avisos,

integrações futuras,

redução de retrabalho do escritório.


---

## 📄 `docs/api/webhooks.md`

Mesmo sendo “futuro”, já deixo pronto pra você não precisar pensar nisso depois 😄

```markdown
# API Reference – Webhooks (v1 – Futuro)

Os **webhooks** permitem que sistemas externos recebam eventos em tempo real da plataforma, como:

- guias enviadas
- documentos recebidos
- pedidos concluídos
- tarefas atrasadas
- etc.

Este módulo é opcional, mas planejado para versões futuras da plataforma.

Prefixo sugerido:



/api/v1/webhooks


---

# 1. Conceitos

### Endpoint de Webhook
URL do sistema do cliente, que será chamada sempre que um evento ocorrer.

### Assinatura
Token secreto usado para verificar a autenticidade da requisição.

### Evento
Mesmos eventos definidos em: `docs/events/eventos-negocio.md`.

---

# 2. Cadastro de Webhook

## 2.1. Criar webhook

### **POST /webhooks**

**Body**

```json
{
  "empresa_id": 55,
  "url": "https://api.meucliente.com.br/integracoes/contabilidade",
  "eventos": [
    "guia_enviada_ao_cliente",
    "documento_enviado",
    "pedido_concluido"
  ],
  "ativo": true
}


Resposta

{
  "id": 1001,
  "secret": "whsec_xxxxxxxxxxxxx"
}


O secret é usado para assinar os payloads.

2.2. Listar webhooks
GET /webhooks

Filtros:

empresa_id

ativo

2.3. Atualizar webhook
PATCH /webhooks/{id}

Pode alterar:

url

eventos

ativo/inativo

2.4. Deletar (inativar) webhook
DELETE /webhooks/{id}

Marca ativo = false.

3. Payload dos Eventos

Todos os payloads seguem o padrão:

{
  "id": "evt_20251128_001",
  "evento": "guia_enviada_ao_cliente",
  "data": {
    "empresa_id": 55,
    "guia_id": 4510,
    "tipo_obrigacao": "DAS",
    "competencia": "2025-10",
    "vencimento": "2025-11-20",
    "status": "ENVIADA"
  },
  "timestamp": "2025-11-28T10:00:00Z"
}

4. Assinatura (Segurança)

Toda requisição de webhook terá header:

X-Webhook-Signature: <assinatura>
X-Webhook-Timestamp: <timestamp>


Assinatura calculada com HMAC-SHA256:

HMAC(secret, timestamp + '.' + body_json)


O sistema do cliente deve:

Ler X-Webhook-Timestamp

Ler o corpo original da requisição (JSON bruto)

Recalcular HMAC

Comparar com X-Webhook-Signature

Validar se o timestamp não é muito antigo (ex.: > 5 min)

5. Retentativas & Erros

Se o sistema do cliente retornar:

2xx → sucesso

4xx ou 5xx → erro

A plataforma deve:

tentar reenviar X vezes (ex.: 3)

aplicar backoff exponencial

registrar no log de webhooks

Tabela de log:

Campo	Descrição
id	identificação
webhook_id	ref. ao webhook configurado
evento	nome do evento
payload	json enviado
status_http	código
tentativas	número de tentativas
ultimo_erro	mensagem resumida
ultimo_envio_em	timestamp
6. Segurança Adicional

Sempre usar HTTPS

Não permitir URLs internas (ex.: 127.0.0.1, 192.168.x.x)

Validar DNS para evitar SSRF

Limitar tempo de resposta (timeout)

7. Teste de Webhook
POST /webhooks/{id}/testar

Envia um evento fictício para a URL cadastrada, permitindo o cliente testar a integração.

Resposta:

{
  "status": "enviado",
  "http_status": 200
}


(ou o status retornado pelo endpoint externo)

8. Relação com Eventos de Negócio

Os webhooks podem ser disparados para os eventos listados em:

docs/events/eventos-negocio.md

Exemplos comuns:

guia_enviada_ao_cliente

documento_enviado

documento_validado

pedido_concluido

tarefa_atrasada

9. Conclusão

O módulo de webhooks permite:

integração em tempo real com ERPs, CRMs, bots, automações

disparo automático de fluxos no cliente

maior personalização

comunicação assíncrona segura

Embora seja planejado como fase futura, esta especificação já guia o backend e integrações.