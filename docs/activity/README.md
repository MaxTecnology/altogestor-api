# Diagramas de Atividade – Fluxos Operacionais

Este diretório contém fluxos detalhados de atividades, mostrando passo a passo como cada processo funciona.

Ideal para:
- Documentar regras de negócio complexas,
- Alinhar processos internos,
- Criar automações,
- Implementar lógica de backend.

## 📁 Arquivos

### **08-activity-fluxo-documentos-guias.puml**
Fluxo completo de:
- Solicitação de documentos
- Validação
- Geração de obrigação
- Publicação e envio de guia
- Pagamento

### **14-activity-fluxo-pedido-formulario.puml**
Fluxo do cliente ao abrir um pedido com formulário parametrizado:
- Seleção do tipo
- Preenchimento automático de campos
- Envio dos documentos
- Criação do PedidoCliente

### **15-activity-fluxo-validacao-documentos.puml**
Fluxo interno de análise do escritório:
- EM_VALIDACAO → COMPLETO / INCOMPLETO / RECUSADO
- Notificações ao cliente
- Interações da equipe interna

## 📌 Observação
Fluxos de atividade são ótimos para validar “como deveria ser” antes de implementar qualquer funcionalidade.
