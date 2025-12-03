# Matriz de Permissões – Perfis, Acessos e Ações

Este documento define a **matriz de permissões** da plataforma, respondendo:

- Quem pode acessar o quê?
- Quem pode **editar**, **aprovar**, **recusar**, **enviar**?
- Quais operações são exclusivas de sócio / gestor?
- O que o **cliente** pode ou não fazer?

Ele é referência para:

- camada de autorização no backend,
- regras de exibição no frontend,
- geração de middlewares/policies (ex.: Laravel Policies, Nest Guards),
- futura implementação de RBAC/ACL.

---

## 1. Perfis (roles) principais

### 1.1. Perfis do Escritório (`usuarios_escritorio`)

| Role               | Descrição                                                                 |
|--------------------|---------------------------------------------------------------------------|
| `socio_admin`      | Acesso total ao ambiente do escritório. Configura regras, empresas, usuários. |
| `gestor`           | Gestão de equipe, tarefas, clientes e parâmetros operacionais.            |
| `analista_fiscal`  | Atua no módulo fiscal (obrigações, guias, documentos fiscais).            |
| `analista_contabil`| Atua no módulo contábil (balanços, lançamentos, docs contábeis).          |
| `analista_dp`      | Atua em folha, e-Social, férias, rescisões etc.                           |
| `colaborador_visualizacao` | Acompanha pendências e status, mas não altera estados críticos.  |

> Na prática, o sistema pode permitir um usuário ter **mais de um papel**, mas esta matriz assume o cenário “tradicional”.

---

### 1.2. Perfis do Cliente (`usuarios_cliente`)

| Role               | Descrição                                                                 |
|--------------------|---------------------------------------------------------------------------|
| `cliente_admin`    | Representa o dono/responsável principal da empresa. Gerencia acessos e recebe todas as guias. |
| `cliente_financeiro` | Focado em pagamentos, envio de comprovantes, pedidos financeiros.     |
| `cliente_basico`   | Acesso limitado: visualizar algumas informações e enviar documentos.     |

---

## 2. Escopo de módulos

Para simplificar, vamos agrupar as ações por módulo:

1. **Empresas & Usuários**
2. **Documentos**
3. **Obrigações & Guias**
4. **Tarefas & Agenda**
5. **Pedidos & Formularios**
6. **Notificações**
7. **Configuração Geral / Sistema**

---

## 3. Matriz de Permissões por Módulo

### 3.1. Empresas & Usuários

Abrange:

- CRUD de empresas,
- vínculo empresa ↔ usuário cliente,
- gestão de usuários do escritório,
- gestão de usuários cliente.

#### 3.1.1. Tabela de alto nível

| Ação                                                         | Socio Admin | Gestor | Analista* | Colab Visualização | Cliente Admin | Cliente Financeiro | Cliente Básico |
|--------------------------------------------------------------|:-----------:|:------:|:---------:|:------------------:|:-------------:|:------------------:|:--------------:|
| Criar empresa                                                | ✅          | ✅     | ❌        | ❌                 | ❌            | ❌                 | ❌             |
| Editar dados da empresa                                      | ✅          | ✅     | ❌        | ❌                 | ❌            | ❌                 | ❌             |
| Inativar empresa                                             | ✅          | ✅     | ❌        | ❌                 | ❌            | ❌                 | ❌             |
| Listar empresas                                              | ✅          | ✅     | ✅        | ✅                 | 🔸 (apenas as suas) | 🔸            | 🔸           |
| Criar usuário do escritório                                  | ✅          | ✅     | ❌        | ❌                 | ❌            | ❌                 | ❌             |
| Editar usuário do escritório                                 | ✅          | ✅     | ❌        | ❌                 | ❌            | ❌                 | ❌             |
| Inativar usuário do escritório                               | ✅          | ✅     | ❌        | ❌                 | ❌            | ❌                 | ❌             |
| Criar usuário cliente para a empresa                         | ✅          | ✅     | ✅ (se responsável) | ❌          | ✅ (apenas da própria empresa) | ✅ (com restrições) | ❌ |
| Editar usuário cliente                                       | ✅          | ✅     | ✅ (se responsável) | ❌          | ✅                 | ✅ (parcial)    | ❌             |
| Inativar usuário cliente                                     | ✅          | ✅     | ✅ (se responsável) | ❌          | ✅                 | ❌              | ❌             |

> `Analista*` aqui engloba os analistas (fiscal, contábil, DP) limitado às empresas/setores que ele atende.

---

### 3.2. Documentos (Solicitações & Uploads)

Abrange:

- modelos de documentos,
- solicitações automáticas,
- envio de arquivos pelo cliente,
- validação interna,
- histórico de estados.

#### 3.2.1. Ações

- Gerenciar **modelos de documentos** (definir quais docs são cobrados de quem)
- Criar solicitação manual
- Ver lista de solicitações
- Ver detalhes de solicitação
- Enviar documentos (cliente)
- Validar documentos (aceitar / recusar / marcar como completo)
- Consultar histórico

#### 3.2.2. Matriz

| Ação                                                   | Socio Admin | Gestor | Analista Fiscal/Contábil/DP | Colab Visualização | Cliente Admin | Cliente Financeiro | Cliente Básico |
|--------------------------------------------------------|:-----------:|:------:|:----------------------------:|:------------------:|:-------------:|:------------------:|:--------------:|
| Gerenciar modelos de documentos                        | ✅          | ✅     | 🔸 (dependendo do depto)     | ❌                 | ❌            | ❌                 | ❌             |
| Criar solicitação manual                               | ✅          | ✅     | ✅                           | ❌                 | ❌            | ❌                 | ❌             |
| Ver solicitações de qualquer empresa                   | ✅          | ✅     | 🔸 (somente suas empresas)   | 🔸 (somente leitura) | ❌           | ❌                 | ❌             |
| Ver solicitações da própria empresa                    | ❌          | ❌     | ❌                           | ❌                 | ✅            | ✅                 | ✅ (limitado)  |
| Enviar documentos para uma solicitação                 | ❌          | ❌     | 🔸 (casos especiais, importação) | ❌            | ✅            | ✅                 | ✅             |
| Validar documentos (EM_VALIDACAO → COMPLETO/INCOMPLETO/RECUSADO) | ✅ | ✅ | ✅                           | ❌                 | ❌            | ❌                 | ❌             |
| Ver histórico de uma solicitação                       | ✅          | ✅     | ✅                           | ✅                 | ✅ (da sua empresa) | ✅            | ✅ (limitado)  |

---

### 3.3. Obrigações & Guias

Abrange:

- tipos de obrigação,
- configurações por empresa,
- guias fiscais,
- comprovantes,
- status das guias.

#### 3.3.1. Matriz

| Ação                                                   | Socio Admin | Gestor | Analista Fiscal | Analista Contábil/DP | Colab Visualização | Cliente Admin | Cliente Financeiro | Cliente Básico |
|--------------------------------------------------------|:-----------:|:------:|:---------------:|:---------------------:|:------------------:|:-------------:|:------------------:|:--------------:|
| Gerenciar tipos de obrigação                           | ✅          | ✅     | 🔸 (apenas fiscais, se permitido) | ❌ | ❌         | ❌            | ❌                 | ❌             |
| Configurar obrigação por empresa                       | ✅          | ✅     | ✅               | 🔸 (se relevante)      | ❌                 | ❌            | ❌                 | ❌             |
| Gerar guia manualmente                                 | ✅          | ✅     | ✅               | 🔸 (quando aplicável)  | ❌                 | ❌            | ❌                 | ❌             |
| Ver guias de qualquer empresa                          | ✅          | ✅     | 🔸 (apenas empresas que atende) | 🔸 (somente leitura) | ❌        | ❌                 | ❌             |
| Ver guias da própria empresa                           | ❌          | ❌     | ❌               | ❌                     | ❌                 | ✅            | ✅                 | ✅ (limitado)  |
| Enviar guia para o cliente                             | ✅          | ✅     | ✅               | 🔸                     | ❌                 | ❌            | ❌                 | ❌             |
| Anexar comprovante de pagamento                        | ❌          | ❌     | 🔸 (casos internos) | ❌                  | ❌                 | ✅            | ✅                 | ✅ (se tiver permissão) |
| Marcar guia como PAGA                                  | ✅          | ✅     | ✅               | 🔸                     | ❌                 | 🔸 (opcional: pode sinalizar pagamento aguardando validação) | ✅ (opcional) | ❌ |

---

### 3.4. Tarefas & Agenda

Abrange:

- modelos de tarefas,
- geração automática,
- atualização de status,
- calendário,
- relatórios de produtividade.

#### 3.4.1. Matriz

| Ação                                | Socio Admin | Gestor | Analista (todos) | Colab Visualização | Cliente (qualquer) |
|-------------------------------------|:-----------:|:------:|:----------------:|:------------------:|:------------------:|
| Gerenciar modelos de tarefas        | ✅          | ✅     | 🔸 (por depto)   | ❌                 | ❌                 |
| Ver tarefas de qualquer colaborador | ✅          | ✅     | 🔸 (apenas suas + empresas) | 🔸 (somente leitura) | ❌ |
| Ver tarefas próprias                | ✅          | ✅     | ✅               | ✅                 | ❌                 |
| Atualizar status de tarefa própria  | ✅          | ✅     | ✅               | ❌                 | ❌                 |
| Atualizar tarefa de outro usuário   | ✅          | ✅     | 🔸 (se for líder de equipe) | ❌ | ❌ |
| Ver relatórios de produtividade     | ✅          | ✅     | 🔸 (resumo próprio) | ❌              | ❌                 |

---

### 3.5. Pedidos & Formulários

Abrange:

- modelos de pedidos,
- campos e documentos do modelo,
- pedidos abertos pelo cliente ou internamente,
- upload de docs,
- histórico de status.

#### 3.5.1. Matriz

| Ação                                      | Socio Admin | Gestor | Analista (setor) | Colab Visualização | Cliente Admin | Cliente Financeiro | Cliente Básico |
|-------------------------------------------|:-----------:|:------:|:----------------:|:------------------:|:-------------:|:------------------:|:--------------:|
| Criar modelo de pedido                    | ✅          | ✅     | 🔸 (do seu depto) | ❌                 | ❌            | ❌                 | ❌             |
| Editar modelo de pedido                   | ✅          | ✅     | 🔸                | ❌                 | ❌            | ❌                 | ❌             |
| Abrir pedido para empresa (interno)       | ✅          | ✅     | ✅                | ❌                 | ❌            | ❌                 | ❌             |
| Abrir pedido (cliente)                    | ❌          | ❌     | ❌                | ❌                 | ✅            | ✅                 | ✅ (limitado)  |
| Ver pedidos de todas empresas             | ✅          | ✅     | 🔸 (empresas que atende) | 🔸 (leitura) | ❌ | ❌        | ❌             |
| Ver pedidos da própria empresa            | ❌          | ❌     | ❌                | ❌                 | ✅            | ✅                 | ✅ (limitado)  |
| Atualizar status de pedido (interno)      | ✅          | ✅     | ✅                | ❌                 | ❌            | ❌                 | ❌             |
| Enviar documentos anexos no contexto do pedido | ❌     | ❌     | 🔸 (apenas complementos internos) | ❌ | ✅ | ✅       | ✅             |

---

### 3.6. Notificações

Abrange:

- envio de notificações (email, WhatsApp, etc.),
- templates,
- logs.

#### 3.6.1. Matriz

| Ação                                   | Socio Admin | Gestor | Analista | Colab Visualização | Cliente Admin | Cliente Outros |
|----------------------------------------|:-----------:|:------:|:--------:|:------------------:|:-------------:|:--------------:|
| Gerenciar templates de notificação     | ✅          | ✅     | ❌       | ❌                 | ❌            | ❌             |
| Enviar notificação manual (painel)     | ✅          | ✅     | ✅       | ❌                 | ❌            | ❌             |
| Ver logs de notificações               | ✅          | ✅     | 🔸 (filtrado por empresa/cliente) | 🔸 (mais limitado) | 🔸 (logs recebidos) | 🔸 (limitado) |

---

### 3.7. Configuração Geral / Sistema

- Parâmetros globais,
- integrações (ASAAS, gateways, e-mail SMTP, WhatsApp API, etc.),
- chaves de API.

| Ação                          | Socio Admin | Gestor | Outros |
|-------------------------------|:-----------:|:------:|:------:|
| Gerenciar configurações globais | ✅        | 🔸 (restrito) | ❌   |
| Gerenciar integrações externas  | ✅        | 🔸 (restrito) | ❌   |

---

## 4. Recomendações de Implementação

### 4.1. Backend

- Implementar autorização em camada específica:
  - Policies/Guards/Middlewares, ex.:
    - `CanViewEmpresa`
    - `CanManageDocumentosEmpresa`
    - `CanManageGuiasEmpresa`
- Criar helpers genéricos como:
  - `user.can(action, resource, context)`  
    Ex.: `user.can('validar_documentos', 'empresa', empresa_id)`

### 4.2. Frontend

- Esconder botões/ações que o usuário não pode executar, com base em:
  - `perfil` (role),
  - `permissoes` retornadas no endpoint `/me`.

### 4.3. Auditoria

- Toda ação crítica deve gerar **log**:
  - mudança de status (guia, solicitação, tarefa, pedido),
  - exclusão de documentos,
  - alteração de configs de empresa,
  - mudança de permissões de usuário.

---

## 5. Próximos documentos relacionados

Para complementar esta matriz:

- `docs/domain/glossario.md` – termos de domínio usados em todo o sistema.
- `docs/security/auditoria.md` – o que e como será logado.
- `docs/api/empresas.md` – endpoints específicos de empresa e vínculos.

