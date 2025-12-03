# Integrações – Guia Oficial

Este documento descreve todas as integrações externas que podem ser utilizadas pelo sistema:

- **ASAAS** (boletos, cobranças, PIX)
- **OneDrive / SharePoint** (importação de arquivos)
- **E-mail SMTP** (notificações)
- **WhatsApp API / Provedores** (notificações)
- **Webhooks** (Futuro – callbacks baseados em eventos)
- **Outras integrações planejadas**

O objetivo é padronizar:

- autenticação
- formatos de payload
- endpoints utilizados
- boas práticas
- logs e auditoria
- erros comuns
- limites e recomendações

---

# 1. Visão Geral das Integrações

| Integração | Função | Status |
|------------|--------|--------|
| ASAAS | pagamentos, boletos, PIX | opcional |
| OneDrive/SharePoint | importação de documentos | ativo |
| SMTP (e-mail) | notificações | obrigatório |
| WhatsApp API | notificações | opcional |
| Webhooks externos | callbacks | futuro |
| Outros provedores | módulos futuros | planejado |

---

# 2. ASAAS – Integração de Pagamentos

A plataforma poderá integrar com ASAAS no módulo financeiro do cliente.

### Funcionalidades suportadas:

- Criação de cobrança (boleto / pix)
- Recuperação de status
- Webhook de confirmação de pagamento
- Cancelamento de cobrança
- Notificação de atraso

### Endpoints mais utilizados:

| Recurso | Método | Endpoint |
|--------|--------|----------|
| Criar cobrança | POST | `/v3/payments` |
| Consultar cobrança | GET | `/v3/payments/{id}` |
| Cancelar | DELETE | `/v3/payments/{id}` |
| Webhooks | POST | `/webhook/asaas` |

### Autenticação:

Headers:
Access-Token: {ASAAS_API_KEY}


### Logs recomendados:
- `asaas_request`
- `asaas_response`
- `asaas_webhook_received`
- `asaas_payment_confirmed`

---

# 3. OneDrive / SharePoint – Integração de Arquivos

Essa integração é essencial para importar:

- XML de entrada/saída
- relatórios
- pastas de documentos fiscais
- arquivos contábeis enviados pelo cliente

### Tipo de Integração
- OAuth 2.0 via Microsoft Graph API
- Permissões: Files.Read.All, Sites.Read.All

### Fluxos suportados:

#### 3.1. Importação manual
Usuário escolhe um arquivo/pasta e envia para o sistema.

#### 3.2. Automação (n8n ou interno)
O sistema monitora pastas específicas como:



/Empresas/{empresa}/Fiscal/Entrada
/Empresas/{empresa}/Contábil/Livros


#### 3.3. Identificação de arquivos novos
Eventos ou polling:

- `drive/root/children`
- `delta` endpoint para detectar mudanças

### Logs recomendados:
- `onedrive_list_files`
- `onedrive_download`
- `onedrive_new_file_detected`

---

# 4. E-mail SMTP – Notificações Oficiais

A plataforma usa SMTP para enviar:

- guias
- solicitações
- avisos de documentos pendentes
- pedidos e atualizações
- notificações mensais

### Parametrização no `.env`:



MAIL_HOST=
MAIL_PORT=
MAIL_USERNAME=
MAIL_PASSWORD=
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=
MAIL_FROM_NAME="Escritório Contábil"


### Boas práticas:

- enviar sempre via fila
- registrar logs: `email_sent`, `email_error`
- evitar anexos > 10MB (usar links seguros)

---

# 5. WhatsApp API

Integração opcional, mas útil para notificações rápidas ao cliente.

### Suporte:

- enviar mensagens automáticas:  
  - guias do mês  
  - documentos pendentes  
  - solicitações abertas  
  - pedidos aguardando cliente  

### Provedores suportados:

- Meta WhatsApp API (oficial)  
- Z-API  
- UltraMSG  
- WATI  
- Suporte futuro a múltiplos provedores

### Autenticação:
Depende do provedor.

### Logs recomendados:
- `whatsapp_sent`
- `whatsapp_error`
- `whatsapp_webhook_received` (quando aplicável)

---

# 6. Webhooks – Futuro

A plataforma poderá expor webhooks para o cliente receber eventos externos:

### Eventos disponíveis:

- documento_enviado  
- documento_validado  
- guia_enviada  
- comprovante_enviado  
- pedido_aberto  
- pedido_concluido  
- tarefa_atrasada  
- solicitacao_criada  

### Exemplo de payload:

```json
{
  "evento": "guia_enviada",
  "empresa_id": 55,
  "dados": {
    "guia_id": 4510,
    "vencimento": "2025-10-20"
  }
}

Requisitos:

secret para assinatura

retry automático

histórico de entrega

7. Padronização das Integrações

Todas as integrações seguem:

7.1. Retries

3 tentativas

backoff exponencial

7.2. Logs

request

response

status

payload

tempo de execução

ambiente (dev/homolog/prod)

7.3. Auditoria

Integrações críticas geram entradas em:

auditoria
integracoes_logs
eventos_negocio

8. Erros comuns e como lidar
Erro	Causa	Ação
401 Unauthorized	API Key inválida	Revisar credenciais
403 Forbidden	Permissão insuficiente	Ajustar permissões (Graph/ASAAS)
404 Not Found	Caminho SharePoint incorreto	Ajustar pastas
429 Too Many Requests	Limite de uso	Implementar retry/backoff
500 Internal Error	Problema do provedor	Repetir em fila
Timeout	Conexão lenta	Reenviar via worker
9. Boas Práticas Gerais de Integração

Nunca fazer integrações no thread da requisição
→ sempre usar fila.

Centralizar integrações em Services
→ AsaasService, OneDriveService, WhatsappService.

Criar logs estruturados para automação
→ permite reprocessar usando Codex.

Realizar sanitização de dados
→ remover caracteres inválidos antes de enviar.

Todos os retornos devem ser validados
→ especialmente ASAAS e WhatsApp.

Não armazenar tokens sem criptografia
→ usar secrets ou env criptografado.

10. Futuras integrações possíveis

Sefaz / DTE (monitoramento direto)

SERPRO (serviços fiscais)

e-Social API

SPED (upload de arquivos)

Bancos (para conciliação)

EDI Fiscal

Amazon Textract / Vision para OCR

11. Conclusão

Este documento define todas as integrações da plataforma, incluindo:

estrutura

padrões

autenticação

logs

fluxos

boas práticas

Ele é essencial para:

novos desenvolvedores

automações (Codex/IA)

DevOps

suporte

criação de módulos futuros