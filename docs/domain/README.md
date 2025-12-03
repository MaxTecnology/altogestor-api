# Modelo de Domínio – Diagramas de Classes

Este diretório contém o modelo de dados conceitual da plataforma.  
Representa **entidades**, **atributos**, **relacionamentos** e **regras de domínio**.

Os diagramas aqui servem para:
- Projetar banco de dados.
- Criar migrations.
- Definir modelos no backend.
- Sincronizar entendimento entre devs e produto.

## 📁 Arquivos

### **04-state-solicitacao-documento.puml**
Máquina de estados de "Solicitação de Documento":
- PENDENTE
- PARCIAL
- EM_VALIDACAO
- INCOMPLETO
- RECUSADO
- COMPLETO
- EM_ATRASO
- NAO_ENTREGUE

### **05-class-diagrama-documentos.puml**
Modelo completo de documentos:
- ModeloDocumento
- SolicitaçãoDocumento
- DocumentoEnviado
- Histórico de Estados
- EnvioDocumentoCliente

### **06-class-diagrama-obrigacoes-guias.puml**
Domínio das obrigações fiscais e guias:
- TipoObrigacao
- Configuração de Obrigação por Empresa
- GuiaFiscal
- Comprovantes

### **07-state-guia-fiscal.puml**
Estado da Guia Fiscal:
- GERADA_INTERNA
- DISPONIVEL_PORTAL
- ENVIADA_CLIENTE
- VISUALIZADA_CLIENTE
- PAGA
- ATRASADA
- CANCELADA

### **09-class-diagrama-tarefas-agenda.puml**
Modelo de agenda/tarefas:
- ModeloTarefaObrigacao
- TarefaObrigacao
- Histórico de Tarefas

### **10-class-diagrama-pedidos-formularios.puml**
Dominio de pedidos com formulário parametrizado:
- ModeloPedido
- Campos e Documentos obrigatórios
- PedidoCliente
- Respostas e Uploads
- Histórico

