# Domínios e Módulos

Para evitar monólito caótico, o projeto será estruturado em módulos dentro de `App/Domain`.

## Módulos Principais

### 1. Core
- Tenants
- Empresas
- Usuários
- EmpresaUser (vínculo)
- RBAC
- Auditoria
- Logs de Ação

### 2. Documentos
- Modelos
- Uploads
- Histórico de Status
- Assinatura (futuro)

### 3. Obrigações
- Tipos de obrigações
- Configs por empresa
- Guias
- Comprovantes
- Histórico de Estado

### 4. Workflow
- Tarefas
- Agenda
- Atribuição
- Histórico

### 5. Forms
- Modelos de formulário
- Campos
- Pedidos
- Respostas
- Anexos

### 6. Notifications
- Templates
- Envio
- Logs
- Integrações externas

## Estrutura Sugerida

app/Domain/
Core/
Documentos/
Obrigacoes/
Workflow/
Forms/
Notifications/


Cada domínio contém:
- Models
- Actions/Services
- Policies
- Controllers (opcional)