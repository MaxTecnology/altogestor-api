# Fluxos de Frontend – Portal do Escritório & Portal do Cliente

Este documento descreve as **telas**, **fluxos de navegação** e **principais ações** para o frontend da plataforma, separados por perfil:

- Portal do Escritório (time interno)
- Portal do Cliente (empresa atendida)

Ele é a base para:

- definição das rotas de frontend,
- design de telas,
- implementação em qualquer framework (React, Vue, etc.),
- integração com as APIs definidas em `docs/api/*`.

---

# 1. Perfis de Usuário e Acesso

## 1.1. Escritório

Perfis internos:

- `socio_admin`
- `gestor`
- `analista_fiscal`
- `analista_contabil`
- `analista_dp`
- `colaborador_visualizacao`

## 1.2. Cliente

Perfis do portal:

- `cliente_admin`
- `cliente_financeiro`
- `cliente_basico`

Cada perfil enxerga um subconjunto das telas abaixo, conforme `docs/security/permissoes.md`.

---

# 2. Fluxo – Autenticação e Seleção de Contexto

## 2.1. Tela de Login

**URL sugerida:** `/login`

Elementos:

- campo e-mail/usuário  
- campo senha  
- botão “Entrar”  
- link “Esqueci minha senha”  
- (futuro) seleção de idioma

Ações:

- `POST /auth/login`  
- Redirecionamento:
  - se usuário interno → Dashboard Escritório
  - se cliente → Dashboard Cliente

---

## 2.2. Seleção de Empresa (quando necessário)

Alguns usuários:

- analistas que lidam com várias empresas  
- clientes com mais de uma empresa  

**URL sugerida:** `/selecionar-empresa`

Layout:

- lista de empresas com:
  - nome, CNPJ
  - tag de regime (Simples, LP, etc.)
  - indicador de pendências (docs/guias/tarefas)

Ações:

- Ao clicar em uma empresa → define `empresa_atual` no frontend e redireciona:
  - Escritório: `/escritorio/dashboard`
  - Cliente: `/cliente/dashboard`

---

# 3. Portal do Escritório

## 3.1. Layout Base

Layout padrão:

- **Topo**:
  - logo do escritório
  - seleção de empresa atual (dropdown)
  - ícone de notificações
  - menu do usuário (perfil, sair)

- **Menu lateral**:
  - Dashboard
  - Empresas
  - Documentos
  - Obrigações & Guias
  - Tarefas & Agenda
  - Pedidos
  - Notificações
  - Administração (apenas perfis elevados)

---

## 3.2. Tela – Dashboard do Escritório

**URL:** `/escritorio/dashboard`

Widgets principais (consome `docs/api/dashboard.md`):

- cards:
  - Documentos pendentes / atrasados
  - Guias vencendo hoje / atrasadas
  - Tarefas de hoje / atrasadas
  - Pedidos em análise / aguardando cliente
- lista de **empresas com mais pendências**
- lista de **tarefas do usuário logado** (atalho rápido)

Ações:

- filtros por:
  - departamento (fiscal, contábil, DP)
  - empresa (quando aplicável)
  - período (hoje, semana, mês)

---

## 3.3. Tela – Empresas

### 3.3.1. Lista de Empresas

**URL:** `/escritorio/empresas`

Elementos:

- tabela com:
  - Razão social
  - CNPJ
  - Regime tributário
  - Status (ativo/inativo)
  - Responsáveis (fiscal/contábil/DP)
  - pendências (ícones/contadores)

Ações:

- botão “Nova empresa”
- link “Editar”
- ação rápida “Ver pendências” (leva pro dashboard filtrado nessa empresa)

### 3.3.2. Detalhe da Empresa

**URL:** `/escritorio/empresas/:id`

Abas sugeridas:

- **Resumo**: dados principais, contatos, regime
- **Responsáveis**: internos por departamento
- **Usuários cliente**: gestão dos usuários do portal
- **Configurações**:
  - preferências de notificação
  - parâmetros de obrigações
  - tags / classificações

---

## 3.4. Tela – Documentos & Solicitações

### 3.4.1. Lista de Solicitações

**URL:** `/escritorio/documentos/solicitacoes`

Filtros:

- empresa
- competência/período
- departamento
- status (pendente, parcial, em validação, etc.)

Tabela:

- Empresa
- Tipo de solicitação / modelo
- Período / competência
- Quantidade de documentos esperados x recebidos
- Status
- Data de vencimento
- Indicador de atraso

Ações:

- “Criar solicitação” (manual)
- “Ver detalhes”

### 3.4.2. Detalhe da Solicitação

**URL:** `/escritorio/documentos/solicitacoes/:id`

Blocos:

- resumo da solicitação (empresa, modelo, período, vencimento, status)
- lista de documentos enviados:
  - arquivo, data/hora, usuário, status de validação
- timeline de estados (workflow)
- área de comentários internos

Ações:

- marcar documento como:
  - EM_VALIDACAO
  - COMPLETO
  - INCOMPLETO
  - RECUSADO
- marcar solicitação como COMPLETA
- reenviar notificação ao cliente
- baixar tudo (ZIP)

---

## 3.5. Tela – Obrigações & Guias

### 3.5.1. Lista de Obrigações / Guias

**URL:** `/escritorio/guias`

Filtros:

- empresa
- tipo de obrigação
- competência
- status (gerada, enviada, paga, atrasada)

Tabela:

- Empresa
- Tipo (DAS, ISS, etc.)
- Competência
- Vencimento
- Status
- Indicador de comprovante enviado

Ações:

- “Anexar guia”
- “Marcar como enviada”
- “Ver comprovante”
- “Marcar como paga”
- “Ver histórico”

---

## 3.6. Tela – Tarefas & Agenda

### 3.6.1. Lista de Tarefas

**URL:** `/escritorio/tarefas`

Vista em:

- tabela
- calendário (semelhante a Nibo/Gestta)

Campos:

- Título
- Empresa
- Responsável
- Departamento
- Data-meta
- Status
- Origem (documento, guia, pedido, manual)

Ações:

- criar tarefa manual
- filtrar por:
  - minhas tarefas
  - atrasadas
  - por empresa
- abrir detalhe da tarefa

### 3.6.2. Detalhe da Tarefa

Exibe:

- descrição completa
- relacionamento (documento/guia/pedido)
- histórico de estado
- comentários
- anexos (se aplicável)

---

## 3.7. Tela – Pedidos (Cliente → Escritório)

### 3.7.1. Lista de Pedidos

**URL:** `/escritorio/pedidos`

Tabela:

- Empresa
- Tipo de pedido/modelo
- Status (aberto, em análise, aguardando cliente, concluído)
- Data de abertura
- Última atualização
- Responsável

### 3.7.2. Detalhe do Pedido

Deve mostrar:

- formulário preenchido
- anexos enviados pelo cliente
- anexos internos do escritório
- histórico de status
- comentários (cliente/escritório)
- solicitações de informação adicional

---

## 3.8. Tela – Notificações (Escritório)

**URL:** `/escritorio/notificacoes`

- lista de notificações internas
- filtros (lidas/não lidas, tipo)
- acesso rápido ao contexto (documento/guia/pedido/tarefa)

---

## 3.9. Tela – Administração (opcional, para perfis altos)

**URL base:** `/escritorio/admin/...`

Seções:

- Usuários do escritório
- Perfis/Permissões
- Templates de notificação
- Integrações (ASAAS, WhatsApp, etc.)

---

# 4. Portal do Cliente

## 4.1. Layout Base

Layout mais simples:

- **Topo**:
  - logo do escritório (ou da plataforma)
  - nome da empresa
  - campainha de notificações
  - menu usuário

- **Menu lateral**:
  - Dashboard
  - Documentos
  - Guias
  - Pedidos
  - (Futuro) Financeiro
  - Perfil / Preferências

---

## 4.2. Tela – Dashboard do Cliente

**URL:** `/cliente/dashboard`

Cards principais:

- Guias pendentes / vencidas
- Documentos pendentes
- Pedidos abertos / em análise
- Tarefas em que o cliente precisa agir (aguardando cliente)

Lista de:

- próximos vencimentos de guias
- solicitações de documentos com prazo próximo
- últimas notificações

---

## 4.3. Tela – Documentos (Cliente)

### 4.3.1. Lista de Solicitações de Documentos

**URL:** `/cliente/documentos/solicitacoes`

Tabela:

- Tipo da solicitação (ex.: “XML Saídas – Outubro/2025”)
- Período / competência
- Status:
  - PENDENTE
  - PARCIAL
  - EM_VALIDACAO
  - COMPLETO
  - INCOMPLETO
  - RECUSADO
- Data de vencimento
- Indicador de atraso

Ações:

- clicar em uma solicitação para enviar/visualizar arquivos

### 4.3.2. Detalhe da Solicitação – Envio de Documentos

**URL:** `/cliente/documentos/solicitacoes/:id`

Blocos:

- resumo da solicitação
- instruções do que deve ser enviado (texto + lista de documentos esperados)
- área de upload:
  - arrastar e soltar
  - lista de arquivos já enviados
  - status (em validação, aceito, recusado)
- timeline de mensagens do escritório (ex.: “falta o XML de Saídas dia 15–20”)

---

## 4.4. Tela – Guias

### 4.4.1. Lista de Guias

**URL:** `/cliente/guias`

Filtros:

- competência
- tipo de obrigação
- status (pendente, vencida, paga)

Tabela:

- Tipo
- Competência
- Vencimento
- Valor (se disponível)
- Status
- Link pra guia (PDF)
- Indicador de comprovante enviado

Ações:

- baixar guia
- enviar comprovante (upload)
- ver histórico da guia

---

## 4.5. Tela – Pedidos (Cliente)

### 4.5.1. Lista de Pedidos

**URL:** `/cliente/pedidos`

- Tipo/modelo do pedido
- Status
- Data de abertura
- Última interação

Ações:

- “Novo pedido” (seleciona um modelo)
- ver detalhes / responder

### 4.5.2. Novo Pedido

**URL:** `/cliente/pedidos/novo`

Fluxo:

1. Selecionar um modelo de pedido (ex.: “Balanço Patrimonial”, “Regularização”, etc.)
2. Exibir formulário dinâmico com campos configurados
3. Permitir envio de anexos obrigatórios
4. Enviar pedido para o escritório

---

## 4.6. Tela – Notificações (Cliente)

**URL:** `/cliente/notificacoes`

Semelhante ao escritório, mas focado em:

- guias
- documentos
- pedidos
- alertas de atraso

---

## 4.7. Tela – Perfil / Preferências

**URL:** `/cliente/perfil`

- dados do usuário
- telefone / e-mail
- preferências de notificação (email/whatsapp)
- (opcional) alterar senha

---

# 5. Considerações de UI/UX Gerais

- **Visão por competência e calendário**:
  - Front pode ter visão tipo calendário pra DEZEMBRO/2025:
    - ícones para vencimento de guias
    - tarefas
    - solicitações de documentos
- **Indicadores claros de atraso** (cores, badges)
- **Filtros salvos** por usuário (lembrar filtro de “minhas tarefas”, “minhas empresas”)
- **Responsividade**:
  - portal do cliente deve funcionar bem em mobile
  - portal do escritório pode começar foco desktop

---

# 6. Próximos passos de frontend

1. Definir stack (ex.: React + Vite + Tailwind + algum UI kit)
2. Criar estrutura de rotas baseada neste documento
3. Criar layout base (Shell) do Escritório e do Cliente
4. Implementar primeiramente:
   - Login
   - Seleção de empresa
   - Dashboard do escritório
   - Fluxo de documentos (escritório e cliente)

Esse documento pode ser refinado à medida que formos definindo componentes, design system e rotas exatas.

