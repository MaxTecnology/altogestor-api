# Diagramas de Sequência – Comunicação Entre Componentes

Este diretório contém a visão temporal e interativa entre módulos, frontends e serviços externos.

Use estes diagramas quando precisar:
- Entender “quem chama quem”
- Implementar API endpoints
- Criar workers e jobs assíncronos
- Investigar problemas de integração

## 📁 Arquivos

### **11-seq-envio-guia.puml**
Sequência do envio da guia para o cliente:
- Backend → (Evento) → Notificação → Logs

### **12-seq-envio-documentos.puml**
Fluxo do cliente enviando documentos:
- Portal → Storage → Backend
- Atualização de status
- Notificação interna

### **13-seq-geracao-periodica.puml**
Job automático diário:
- Geração de Obrigações
- Criação de Tarefas
- Criação de Solicitações de documento
- Logs e eventos

