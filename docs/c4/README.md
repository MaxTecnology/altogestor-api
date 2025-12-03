# C4 – Arquitetura da Plataforma

Este diretório contém todos os diagramas C4 da plataforma, representando a visão arquitetural em camadas:

- **Contexto (Nível 1)** – visão macro do sistema.
- **Containers (Nível 2)** – componentes principais da solução.
- **Componentes (Nível 3)** – módulos internos do backend.
- **Infraestrutura (Nível 4)** – visão de deploy e serviços.

## 🎯 Quando usar os diagramas C4

Use estes diagramas quando precisar:

- Definir os limites estruturais da aplicação.
- Explicar a arquitetura para novos desenvolvedores.
- Gerar documentação para auditoria/compliance.
- Definir responsabilidades entre sistemas.
- Integrar novos serviços ou módulos.

## 📁 Arquivos

### **01-c4-contexto-geral.puml**
Visão de alto nível: usuários, sistemas externos, backend, painel e portal.

### **02-c4-containers-plataforma.puml**
Mostra os containers técnicos:
- Backend
- Worker/Scheduler
- Banco de Dados
- Storage
- Painel e Portal
- Serviço de Notificações

### **03-c4-componentes-backend-obrigacoes-tarefas.puml**
Componentes internos do backend:
- Empresas & Clientes
- Documentos
- Obrigações
- Guias
- Financeiro
- Tarefas
- Pedidos com formulários
- Geração periódica automática

### **16-c4-infraestrutura-deploy.puml**
Infraestrutura e deploy:
- Reverse Proxy / Gateway
- API
- Worker
- Banco
- Storage
- Logs
- Serviços externos

## 📌 Observação
Cada arquivo possui somente **um bloco completo**:

