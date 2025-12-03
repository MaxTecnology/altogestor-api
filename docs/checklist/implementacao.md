1. Checklist técnico de implementação

Vou escrever como uma lista que você pode literalmente usar como Kanban (ToDo / Doing / Done).

🔹 Fase 0 – Preparar o projeto

Criar repositório(s)

backend (ex.: conta-office-api)

(Opcional agora) frontend (ex.: conta-office-web)

Configurar estrutura básica

Definir stack backend (ex.: Laravel / Nest / etc.)

Criar README inicial apontando para a pasta docs/

Adicionar .editorconfig, .gitignore, padrão de PSR/ESLint/Prettier conforme linguagem.

Docker / Ambiente local

docker-compose com:

app (backend)

db (MySQL/Postgres)

redis

mailhog/mailpit (email fake)

Volume de storage para uploads locais.

CI/CD esqueleto

Pipeline para:

rodar testes

rodar lint

(mais pra frente) build + deploy

🔹 Fase 1 – Banco de dados (Migrations)

Seguir as docs docs/db/*.md e criar migrations em blocos:

Núcleo de autenticação & tenants

usuarios

empresas

usuarios_empresas (vínculo usuário cliente ↔ empresa)

perfis / roles e permissoes (ou tabela de roles + enum)

Documentos & Solicitações

modelos_documentos

solicitacoes_documentos

documentos

arquivos_uploads (ou uploads)

Obrigações & Guias

tipos_obrigacoes

config_obrigacoes_empresa

guias

comprovantes_pagamento

Tarefas & Agenda

modelos_tarefas

tarefas

histórico de estados (se separado)

Pedidos & Formulários

modelos_pedidos

modelos_pedidos_campos

pedidos

pedidos_campos_respostas

pedidos_arquivos

Notificações

templates_notificacoes

notificacoes

notificacoes_logs

preferencias_notificacao_empresa

preferencias_notificacao_usuario

Auditoria & Sistema

auditoria_logs

health_logs (opcional)

scheduler_monitor

workers_monitor

Depois disso: rodar migrations no ambiente dev e validar relações básicas.

🔹 Fase 2 – Núcleo de autenticação & empresas

Implementar Auth + Usuários

Login (JWT ou session)

Perfis: socio_admin, gestor, analista_fiscal, analista_contabil, analista_dp, cliente_admin, cliente_financeiro, cliente_basico

Middleware de autenticação + middleware de role/empresa.

Módulo Empresas

EmpresaRepository, EmpresaService, EmpresaController

Endpoints conforme docs/api/empresas.md

Vínculo usuário cliente ↔ empresa

Definir responsáveis por departamento via API

Permissões / RBAC

Implementar checagem centralizada (ex.: Gate / Policy / middleware custom)

Testar cenários:

usuário interno x cliente

acesso a empresa que não é dele → 403

🔹 Fase 3 – Documentos & Solicitações (primeiro fluxo completo)

Esse é o melhor “primeiro fluxo vertical” pra implementar.

Modelo de documentos

CRUD de modelos_documentos

Definir periodicidade, departamento, obrigatoriedade.

Solicitações de documentos

Criar manualmente (escritório)

Listar por empresa, período, status

Estados: PENDENTE, PARCIAL, EM_VALIDACAO, COMPLETO, INCOMPLETO, RECUSADO

Uploads

Implementar /uploads conforme docs/api/uploads.md

Associar upload à solicitacao_documento / documento

Guardar metadados do arquivo + caminho no storage

Validação

Endpoint para escritório validar documentos

Atualizar estado da solicitação e do documento

Gerar entradas de auditoria e eventos (documento_enviado, documento_validado, documento_invalido etc.)

Notificações ligadas a esse fluxo

Ao criar solicitação → notificar cliente

Documento inválido/recusado → notificar cliente

Documento recebido → notificar responsável interno

Testes

Testar fluxo e2e:

criar empresa + usuário cliente

criar solicitação

cliente faz upload

escritório valida

estados e notificações ok

🔹 Fase 4 – Obrigações & Guias

Tipos de obrigação + config por empresa

Cadastrar tipos_obrigacoes (DAS, ISS, etc.)

Configuração por empresa (vencimento, regras de dias úteis).

Geração de obrigações mensais

Job/scheduler: gerar registros de obrigações/guias por competência

Estados iniciais: GERADA

Envio de guias ao cliente

Upload da guia (PDF)

Notificação via email/whatsapp/interna

Endpoint /guias/{id}/enviar se existir fluxo explícito

Comprovantes

Cliente faz upload do comprovante

Escritório valida (marca guia como paga)

Atrasos

Job de verificação de guias vencidas

Atualiza estado para ATRASADA

Notificações associadas

🔹 Fase 5 – Tarefas & Agenda

Modelos de tarefa

Configuração de tarefas automáticas (ligadas a obrigações, documentos, pedidos).

Geração de tarefas

Scheduler gera tarefas com data-meta (antecipar/postergar em dias úteis)

Fluxo de tarefa

Listar por responsável, empresa, status

Atualizar estados (EM_ABERTO, EM_ANDAMENTO, AGUARDANDO_CLIENTE, CONCLUIDA, ATRASADA)

Logs de mudança de estado

Integração com Dashboard

Alimentar /dashboard/escritorio e /dashboard/cliente

🔹 Fase 6 – Pedidos & Formulários

Modelos de pedidos

Campos dinâmicos

Tipo de anexos obrigatórios

Departamento responsável

Fluxo de pedido

Cliente abre pedido

Escritório analisa, troca estado, solicita info extra

Cliente responde, anexa documentos

Pedido finalizado

Notificações e eventos

pedido_aberto, pedido_aguardando_cliente, pedido_concluido etc.

Notificações conforme docs/api/notificacoes-completo.md

🔹 Fase 7 – Notificações, Dashboard, Observabilidade

Notificações

Implementar templates

Envio via fila (email/whatsapp/interna)

Logs de envio + fila

Preferências por empresa e usuário

Dashboard

Implementar endpoints de /dashboard/escritorio e /dashboard/cliente usando dados reais dos módulos

Healthcheck & Monitoramento

/api/health e /api/health/full

Métricas básicas

Logs estruturados

Integração com Sentry/Grafana (quando estiver disponível)

🔹 Fase 8 – Integrações e Webhooks (quando chegar a hora)

E-mail (SMTP)

Plugar no módulo de notificações.

WhatsApp

Plugar como canal opcional em templates.

ASAAS (quando o módulo financeiro entrar)

Seguir docs/integracoes/README.md.

Webhooks

Implementar docs/api/webhooks.md quando for abrir para terceiros.

2. E o fluxo do frontend, como estamos?

Hoje a gente tem:

Toda a visão de domínio e APIs bem definidas.

Dashboard, documentos, guias, tarefas, pedidos, notificações ― tudo já descrito nas rotas.

O que ainda não fizemos formalmente é:

um doc específico tipo docs/frontend/fluxos.md com:

lista de telas

navegação

“de onde pra onde o usuário vai”

o que aparece em cada tela.

Mas a gente já tem material suficiente pra montar isso rapidinho. Algo assim:

🔹 Fluxo – Portal do Escritório

Principais telas:

Login

Seleção de empresa (quando o usuário interno cuida de várias)

Dashboard do escritório

Módulo Documentos

Lista de solicitações por empresa/período/status

Tela de detalhes da solicitação → documentos anexados, histórico, validação

Módulo Obrigações & Guias

Lista de obrigações por competência

Tela da guia → upload do PDF, comprovante, status

Módulo Tarefas

Minha agenda

Tarefas por empresa/departamento

Módulo Pedidos

Pedidos em análise / aguardando cliente / concluídos

Notificações (campainha no topo)

Administração

Empresas

Usuários

Templates de notificação

Integrações (futuro)

🔹 Fluxo – Portal do Cliente

Principais telas:

Login

Seleção de empresa (se o usuário tiver mais de uma)

Dashboard do cliente

guias a pagar

documentos pendentes

pedidos abertos

Documentos

Solicitações abertas

Tela de enviar/visualizar arquivos

Guias

Guia disponível → ver/download

Upload de comprovante

Pedidos

Abrir pedido a partir de um modelo

Acompanhar status

Notificações

Avisos de guias, docs, pedidos

(Futuro) Financeiro:

contas a pagar/receber

integração com ASAAS