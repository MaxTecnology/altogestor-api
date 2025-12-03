# Prompts Oficiais – Backend (Codex / GPT)

Este documento contém prompts padronizados e altamente otimizados para gerar código no backend com base na arquitetura definida em:

- `docs/dev/padroes-codigo.md`  
- `docs/api/*`  
- `docs/db/*`  
- `docs/events/eventos-negocio.md`  
- `docs/security/*`  

Os prompts são divididos por tipo: migrations, models, services, controllers, jobs etc.

---

# 1. PROMPT – Criar Migration

Você é um desenvolvedor experiente. Gere uma MIGRATION Laravel para a tabela abaixo.

Especificação da tabela

{cole aqui o conteúdo do arquivo docs/db/*.md referente à tabela}

Regras

timestamp padrão

chave primária auto_increment

foreign keys conforme documentação

tipos: use integer/bigint conforme necessidade

campos enum: usar check ou enum nativo

indices conforme documentação

não coloque lógica no arquivo


---

# 2. PROMPT – Criar Model (Laravel)



Gere um MODEL Laravel para a tabela abaixo:

Tabela

{colar documentação da tabela}

Regras

definir $fillable

definir casts corretos

relacionamentos conforme documentação

não incluir regra de negócio

incluir scopes úteis (simples)


---

# 3. PROMPT – Criar Repository



Gere um REPOSITORY para a entidade abaixo.

Entidade

{Nome da entidade}

Regras

CRUD básico

sem regra de negócio

sem exceções complexas

retornos tipados

usar model correspondente


---

# 4. PROMPT – Criar Service



Gere um SERVICE conforme arquitetura oficial.

Função

{descrever o caso de uso}

Regras

sem acesso a Request

sem retorno de Response

lógica de negócio completa

usar Repository

disparar eventos documentados em docs/events/eventos-negocio.md

retornar DTO correspondente


---

# 5. PROMPT – Criar DTO



Gere um DTO para transportar dados da entidade abaixo.

Entidade

{Nome da entidade}

Regras

método static fromModel()

encapsular apenas dados seguros

nunca retornar model inteiro


---

# 6. PROMPT – Criar Controller



Gere um CONTROLLER Laravel conforme arquitetura oficial.

Rota/Funcionalidade

{descrever a rota}

Regras

controllers finos

usar Requests para validação

chamar Service

retornar via ApiResponse padrão

sem lógica de negócio no controller


---

# 7. PROMPT – Criar Request (Validação)



Gere uma classe REQUEST Laravel para validar os dados abaixo.

Campos

{lista de campos}

Regras

usar regras de acordo com docs/db

validar enums

validar relacionamentos (exists)

retornar mensagens amigáveis


---

# 8. PROMPT – Criar Job (Fila)



Gere um JOB Laravel para o processamento abaixo.

Processo

{descrever job}

Regras

implementar ShouldQueue

timeout 120 segundos

tentativas = 3

chamar service interno

logar início e conclusão


---

# 9. PROMPT – Criar Evento + Listener



Gere um EVENTO e LISTENER conforme docs/events/eventos-negocio.md.

Evento

{nome do evento}

Payload

{dados do evento}

Listener

registrar no EventServiceProvider

processar no listener chamando service correspondente

logar execução


---

# 10. PROMPT – Criar Testes (PHPUnit)



Gere TESTES unitários conforme arquitetura.

Escopo

{descrever a funcionalidade}

Regras

criar mocks para Repository

testar regras de negócio (Service)

testes de controller: integração

testar eventos disparados

use nomes claros para os métodos


---

# 11. PROMPT – Criar Seeder



Gere um SEEDER Laravel para popular dados de exemplo.

Entidade

{nome da tabela}

Regras

usar faker

garantir consistência (foreign keys)

gerar volume apenas moderado (50 registros)


---

# 12. PROMPT – Criar Documentação de API



Gere uma documentação de API em formato Markdown baseada na rota abaixo.

Rota

{listar endpoints}

Regras

incluir exemplos

incluir descrição de cada campo

incluir possíveis erros

seguir padrão dos arquivos docs/api/*


---

# 13. PROMPT – Criar Workflow (Jobs Automatizados)



Crie um workflow detalhado para rodar no scheduler:

Processo

(exemplo: geração mensal de solicitações de documentos)

Regras

listar eventos que disparam o workflow

listar jobs envolvidos

tratamentos de erro

logs obrigatórios


---

# 14. PROMPT – Criar Migração + Model + Service + Controller (pacote completo)



Gerar todos os arquivos necessários (migration, model, repository, service, dto, controller, request)
para o recurso abaixo, seguindo rigorosamente as convenções descritas em docs/dev/padroes-codigo.md.

Recurso

{descreva o recurso}

Regras

controllers mínimos

services com lógica

repositories só CRUD

DTO estruturado

eventos disparados conforme evento de negócio

responses padronizadas


---

# 15. PROMPT – Refatoração Inteligente



Refatore o código abaixo seguindo os padrões oficiais do projeto:

Código

{colar trecho}

Regras

aplicar separação controller -> service -> repository

padronizar responses

mover validações para Request

mover lógica para Service

adicionar logs estruturados

garantir emissão de eventos


---

# 16. PROMPT – Criar Diagrama (PlantUML)



Gere um diagrama PlantUML conforme o módulo abaixo.

Módulo

{documentar módulo}

Regras

seguir padrão docs/c4/*

classes com atributos principais

estados quando aplicável

relacionamentos claros


---

# 17. PROMPT – Criar Mocks e Stubs



Gere mocks e stubs para os seguintes repositórios:

{lista}

Regras

usar PHPUnit

cobrir métodos principais


---

# 18. PROMPT – Criar Logs Inteligentes de Auditoria



Gere código PHP para registrar logs de auditoria do evento abaixo:

Evento

{evento}

Regras

usar Log::info

incluir empresa_id, usuario_id

incluir timestamp

incluir payload


---

# 19. PROMPT – Criar Rota + Controller + Validação + Teste



Gere uma rota API, controller, request de validação e teste de integração para a funcionalidade abaixo.

Funcionalidade

{descrever}

Regras

padrão docs/api/*

controller mínimo

validação forte

logs e eventos


---

# 20. Conclusão

Este documento fornece **todos os prompts oficiais** para uso com:

- Codex
- GPT
- GitHub Copilot
- Automação IA

E garante que **tudo** que for gerado pela IA estará:

- seguindo a arquitetura,
- usando padrões oficiais,
- consistente com a documentação,
- pronto para produção.