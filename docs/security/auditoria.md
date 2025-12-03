# Política de Auditoria & Logs – Plataforma Contábil

Este documento define:

- quais eventos são auditados,
- como os logs são armazenados,
- quem pode visualizá-los,
- por quanto tempo são mantidos,
- e como devem ser interpretados.

A auditoria é um dos pilares do sistema, principalmente em ambiente contábil, onde rastreamento de alteração é fundamental para conformidade.

---

# 1. Objetivos da Auditoria

A auditoria do sistema tem os seguintes objetivos:

1. **Rastreabilidade completa**
   Garantir que todas as ações críticas gerem histórico imutável.

2. **Responsabilidade operacional**
   Permitir identificar quem fez o quê, quando e em qual empresa.

3. **Segurança**
   Detectar acessos indevidos, tentativas de fraude e mau uso.

4. **Compliance**
   Atender requisitos de:
   - auditorias contábeis,
   - auditorias fiscais,
   - controles internos,
   - LGPD.

5. **Recuperação de histórico**
   Permitir reconstruir um processo:
   - guia enviada,
   - comprovante adicionado,
   - documento validado,
   - pedido analisado,
   - tarefa concluída.

---

# 2. Estrutura de Auditoria

O sistema utiliza dois tipos principais de auditoria:

1. **Audit Log** (operações gerais)
2. **Histórico de Estados** (workflow)

Ambos são complementares.

---

# 3. Tipos de Log

---

## 3.1. Audit Log (Log Geral)

Armazena toda alteração de dados críticos.

Campos sugeridos:

| Campo | Descrição |
|-------|-----------|
| id | identificador único |
| usuario_id | usuário responsável pela ação |
| empresa_id | empresa associada à ação |
| modulo | “guias”, “documentos”, “tarefas”, “pedidos”, “empresas”, etc. |
| acao | qual ação foi executada |
| descricao | texto humano explicando o evento |
| dados_antes | JSON opcional |
| dados_depois | JSON opcional |
| ip_origem | IP do usuário |
| user_agent | navegador ou app |
| criado_em | timestamp |

### Ações que geram Audit Log

- Login e logout
- Troca de senha
- Criação, edição e inativação de usuários
- Criação e edição de empresa
- Alterações de parâmetros fiscais
- Exclusão de arquivos enviados
- Importações via integração
- Alteração de permissões

---

## 3.2. Histórico de Estados (Workflow)

Cada módulo possui tabelas de histórico próprias:

- documentos
- solicitações
- guias
- tarefas
- pedidos

Esses históricos são *imutáveis* e registram:

| Campo | Descrição |
|--------|-----------|
| id | identificador |
| referencia_id | id do documento/tarefa/pedido |
| estado_anterior | string |
| estado_novo | string |
| observacao | comentário opcional |
| usuario_id | quem realizou |
| criado_em | timestamp |

### Por exemplo:

**Documento**  
`EM_VALIDACAO → COMPLETO`

**Guia**  
`GERADA → ENVIADA → PAGA`

**Tarefa**  
`EM_ABERTO → EM_ANDAMENTO → AGUARDANDO_CLIENTE → CONCLUIDA`

**Pedido**  
`ABERTO → EM_ANALISE → AGUARDANDO_CLIENTE → CONCLUIDO`

---

# 4. Eventos Auditados por Módulo

---

## 4.1. Documentos

Eventos auditados:

- Solicitação criada automaticamente
- Solicitação criada manualmente
- Documento enviado pelo cliente
- Documento removido
- Documento validado (COMPLETO, INCOMPLETO, RECUSADO)
- Documento movido entre estados
- Observação adicionada
- Importação de lote (XML/automações)

Entry de exemplo:

```json
{
  "modulo": "documentos",
  "acao": "validacao",
  "descricao": "Documento marcado como COMPLETO",
  "estado_anterior": "EM_VALIDACAO",
  "estado_novo": "COMPLETO",
  "usuario_id": 8,
  "empresa_id": 55,
  "criado_em": "2025-10-05T14:00:55Z"
}


4.2. Guias & Obrigações

Eventos auditados:

Guia gerada (manual ou automática)

Guia enviada ao cliente

Guia atualizada (valor, vencimento)

Comprovante de pagamento enviado

Guia marcada como paga

Guia marcada como atrasada

Cancelamento de guia

4.3. Tarefas & Agenda

Eventos auditados:

Tarefa gerada automaticamente

Tarefa criada manualmente

Mudança de status

Alteração de responsável

Comentário adicionado

Tarefa concluída

Tarefa reaberta

4.4. Pedidos & Formulários

Eventos auditados:

Pedido aberto pelo cliente

Solicitação de documentos dentro do pedido

Anexos adicionados

Atualização de campos preenchidos

Mudança de status

Mensagens/comentários internos

Conclusão do pedido

4.5. Empresa & Usuários

Eventos auditados:

Empresa criada

Empresa editada

Empresa inativada

Responsáveis da empresa alterados

Usuário cliente criado

Usuário cliente editado

Usuário cliente inativado

Usuário escritório criado/alterado

Permissões alteradas

Configurações de envio atualizadas

5. Retenção de Logs

Recomendações:

Tipo de Log	Tempo
Workflow (histórico de estados)	ilimitado
Audit Log geral	5 anos
Logs de acesso (login/logout)	2 anos
Logs técnicos (erro/stacktrace)	90 dias
Logs de integração	180 dias

Para contabilidade, manter histórico ilimitado é altamente recomendado, pois afeta apurações, revisões e obrigações fiscais.

6. Acesso aos Logs
Perfil	Acesso
socio_admin	total
gestor	total (exceto logs sensíveis, ex.: credenciais)
analistas	logs do seu departamento, empresas sob responsabilidade
colaborador_visualização	leitura parcial
cliente_admin	somente logs referentes: documentos enviados, guias enviadas e pedidos da própria empresa
cliente_financeiro	idem ao admin, porém sem auditoria administrativa
cliente_basico	altamente restrito
7. Segurança dos Logs

Padrões recomendados:

logs não podem ser alterados (somente append)

logs antigos devem ser armazenados em storage frio (S3 Glacier)

dados sensíveis (senha, token) jamais aparecem em texto puro

registros sempre ligados a:

IP,

user-agent,

usuário,

empresa.

8. Integração com Monitoramento

Sugestões futuras:

enviar logs críticos para:

Sentry,

Elastic Stack,

Grafana Loki,

CloudWatch.

Notificações automáticas quando:

falha de geração de guias,

documentos atrasados acumulados,

filas congestionadas,

tarefas atrasadas por X dias.

9. Política de Exportação

Permitir exportar logs:

por empresa,

por módulo,

por período,

csv/json/pdf,

com filtros de usuário e evento.

Exportação disponível apenas para:

socio_admin,

gestor.

10. Exemplos Práticos
Caso 1 – Cliente enviou documento errado

Workflow:

PENDENTE → PARCIAL → EM_VALIDACAO → INCOMPLETO


Auditoria:

upload feito por usuário cliente

validação feita por analista

Caso 2 – Guia enviada e paga
GERADA → ENVIADA → PAGA


Logs:

guia gerada automaticamente

guia enviada manualmente

comprovante anexado por cliente

analista validou pagamento

Caso 3 – Pedido com documentos

cliente abriu o pedido

analista solicitou documentos extras

cliente enviou anexos

analista aprovou e concluiu

Conclusão

Este documento define a política de auditoria central da plataforma, garantindo:

rastreabilidade completa,

conformidade contábil/fiscal,

controle de mudanças,

segurança e transparência.