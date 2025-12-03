# Roadmap Oficial – Plataforma Contábil & Financeira

Este documento apresenta a visão estratégica e técnica de evolução da plataforma.  
Serve como guia para:

- desenvolvimento do produto  
- planejamento de módulos  
- priorização de funcionalidades  
- comunicação com clientes e equipe  
- automações futuras  
- integrações e melhorias contínuas  

O roadmap está dividido em fases:

- **Fase 1 – MVP (funcionamento essencial)**
- **Fase 2 – Expansão Contábil**
- **Fase 3 – Módulo Financeiro Completo**
- **Fase 4 – Automação & Inteligência**
- **Fase 5 – Plataforma Avançada**

---

# FASE 1 — MVP (Base Estrutural) ✔ EM ANDAMENTO

### Objetivo:  
Criar o sistema essencial para atender o escritório contábil e seus clientes, com foco em obrigações, documentos e tarefas.

### Entregas:

## 1. Arquitetura & Estrutura
- ✔ C4 completo (context, container, component)
- ✔ Modelagem de domínio
- ✔ Diagramas de classe
- ✔ API base estruturada
- ✔ Banco de dados inicial
- ✔ Padrões de código
- ✔ Documentação LGPD, segurança, auditoria

## 2. Módulo – Empresas & Usuários
- ✔ cadastro de empresa
- ✔ permissões por departamento
- ✔ papéis (cliente, escritório, gestor)

## 3. Módulo – Solicitação de Documentos (Cliente → Escritório)
- ✔ criação manual e automática
- ✔ envio de arquivos
- ✔ validação do escritório
- ✔ estados: pendente, parcial, completo, recusado
- ✔ evidências armazenadas e auditadas

## 4. Módulo – Obrigações & Guias
- ✔ geração automática mensal (DAS, ISS, etc.)
- ✔ envio de guias ao cliente
- ✔ recebimento de comprovantes
- ✔ controle de atrasos
- ✔ acompanhamento por empresa/departamento

## 5. Módulo – Tarefas & Agenda
- ✔ tarefas manuais e automáticas
- ✔ calendários integrados
- ✔ metas, datas úteis, posterga/antecipa
- ✔ responsável por departamento
- ✔ indicadores e atrasos

## 6. Módulo – Pedidos (Cliente → Escritório)
- ✔ modelos de pedidos
- ✔ formulários configuráveis
- ✔ anexos obrigatórios
- ✔ estado completo (aberto, análise, aguardando cliente, concluído)

## 7. Módulo – Notificações
- ✔ interna
- ✔ e-mail
- ✔ templates estruturados
- ✔ eventos integrados

## 8. Observabilidade & Operação
- ✔ monitoramento
- ✔ healthcheck
- ✔ backup e recovery
- ✔ logs de auditoria

**Status: 90% concluído**

---

# FASE 2 — EXPANSÃO CONTÁBIL

### Objetivo:
Fortalecer o uso intensivo pelo escritório contábil, aumentando produtividade e automação interna.

### Entregas:

## 1. Dashboard Avançado do Escritório
- 🔶 visão por colaborador
- 🔶 alertas inteligentes
- 🔶 painel por empresa
- 🔶 indicadores por departamento (fiscal, contábil, DP)

## 2. Fluxos Avançados de Documentos
- 🔶 modelos de solicitações personalizadas
- 🔶 workflows por tipo de cliente (MEI, Simples, Lucro Presumido)
- 🔶 conjuntos de documentos por período (mensal, esporádico, anual)

## 3. Geração Automática de Obrigações
- 🔶 calendário fiscal inteligente
- 🔶 criação antecipada com base em parâmetros
- 🔶 regras avançadas de postergação/antecipação (dias úteis)

## 4. Módulo de Relatórios
- 🔶 relatório de pendências por empresa
- 🔶 produtividade de colaboradores
- 🔶 SLA de atendimento
- 🔶 histórico de envios

## 5. Integrações Internas
- 🔶 importação automática via OneDrive/SharePoint
- 🔶 integrações com SERPRO (futuro)
- 🔶 importação de XML fiscal para pré-validação

---

# FASE 3 — MÓDULO FINANCEIRO

### Objetivo:
Oferecer um módulo financeiro completo para o cliente, com integração opcional com ASAAS.

### Entregas:

## 1. Financeiro do Cliente
- 🔶 contas a pagar/receber
- 🔶 extrato
- 🔶 categorias
- 🔶 centro de custo
- 🔶 anexos e comprovantes
- 🔶 visão consolidada

## 2. Integração com ASAAS
- 🔶 geração de boletos
- 🔶 pagamentos via PIX
- 🔶 sincronização de status
- 🔶 conciliação automática

## 3. Controle Financeiro do Escritório
- 🔶 repassar cobrança ao cliente
- 🔶 histórico de assinaturas
- 🔶 cobrança por empresa
- 🔶 régua de cobrança automatizada

---

# FASE 4 — AUTOMAÇÃO & IA

### Objetivo:
Reduzir o trabalho operacional do escritório e aumentar eficiência.

### Entregas:

## 1. Automação Inteligente
- 🔶 leitura automática de documentos (OCR)
- 🔶 detecção de documentos incompletos
- 🔶 validação fiscal prévia
- 🔶 organização automática de arquivos

## 2. Robôs de Rotina Contábil
- 🔶 alerta de obrigações incompletas
- 🔶 cobrança automática de documentos
- 🔶 reconhecimento de padrões fiscais

## 3. IA para Escrita de Guias e Pedidos
- 🔶 pré-respostas automáticas
- 🔶 geração de instruções para cliente
- 🔶 preenchimento inteligente de tarefas

---

# FASE 5 — PLATAFORMA AVANÇADA

### Objetivo:
Transformar o sistema em uma plataforma SaaS robusta e completa.

### Entregas:

## 1. Webhooks completos
- 🔶 envio de eventos em tempo real
- 🔶 assinatura digital
- 🔶 reenvio em caso de falha

## 2. Módulo de API Pública
- 🔶 lista de empresas
- 🔶 documentos em tempo real
- 🔶 guias e obrigações
- 🔶 pedidos e tarefas

## 3. Marketplace de Aplicativos
- 🔶 integrações pré-construídas
- 🔶 exportação para ERPs
- 🔶 módulos adicionais

## 4. Multi-Escritório e White Label
- 🔶 tema customizado
- 🔶 domínio próprio
- 🔶 planos e billing via ASAAS

---

# Prioridades Técnicas Gerais

### Alta Prioridade
- finalizar MVP
- testar fluxos automáticos
- melhorar monitoramento
- concluir UI/UX do cliente

### Média Prioridade
- módulo financeiro do cliente
- ASAAS integração
- ferramentas internas do escritório

### Baixa Prioridade
- IA avançada
- marketplace
- API pública

---

# Conclusão

Este roadmap define o direcionamento estratégico do sistema:

- MVP sólido  
- integração contábil eficiente  
- expansão para financeiro  
- automações inteligentes  
- plataforma SaaS robusta  

O roadmap deve ser revisado a cada 3 meses.

