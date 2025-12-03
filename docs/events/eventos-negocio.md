# Eventos de Negócio – Catálogo Oficial do Sistema

Este documento define **todos os eventos internos** emitidos pela plataforma.  
Eles são fundamentais para:

- auditoria interna  
- automações (jobs, notificações, geração de tarefas)  
- integrações externas (webhooks, futuro)  
- monitoração de processos  
- rastreabilidade contábil e fiscal  

Cada evento possui:

- nome único  
- payload padrão  
- origem (módulo)  
- impacto  
- ações automáticas associadas  

---

# 1. Estrutura de um Evento

Todo evento segue o formato:

```json
{
  "evento": "nome_do_evento",
  "timestamp": "2025-11-28T12:00:00Z",
  "usuario_id": 200,
  "empresa_id": 55,
  "dados": {
      "...": "..."
  }
}

Eventos são sempre:

registrados em auditoria

enviados ao módulo interno de automação

notificados para workers dedicados (queue: eventos)

2. Classificação dos Eventos

Os eventos são agrupados em:

Eventos de Documentos

Eventos de Solicitações

Eventos de Obrigações/Guias

Eventos de Tarefas/Agenda

Eventos de Pedidos/Formulários

Eventos de Usuários/Empresa

Eventos de Sistema / Internos

3. Eventos de Documentos (Cliente → Escritório)
3.1. documento_enviado

Quando o cliente envia um documento.

{
  "evento": "documento_enviado",
  "empresa_id": 55,
  "usuario_id": 200,
  "dados": {
    "documento_id": 991,
    "solicitacao_id": 8803,
    "tipo": "XML",
    "arquivo_id": 8811
  }
}

3.2. documento_validado

Escritório validou o documento enviado.

3.3. documento_invalido

Documento enviado está errado, corrompido ou ilegível.

3.4. documento_recusado

Documento não será aceito pelo escritório.

3.5. documento_completo

Todos os documentos de uma solicitação foram enviados corretamente.

4. Eventos de Solicitações de Documentos
4.1. solicitacao_criada

Criada manualmente ou por rotina mensal.

4.2. solicitacao_vencida

Cliente não enviou no prazo.

4.3. solicitacao_completa

Quando todos os documentos foram validados.

4.4. solicitacao_reaberta

Após documento recusado.

5. Eventos de Obrigações e Guias
5.1. obrigacao_gerada

Criada automaticamente no mês (ex.: DAS dia 20).

5.2. guia_enviada_ao_cliente

Escritório anexou guia e notificou cliente.

5.3. comprovante_enviado

Cliente enviou comprovante de pagamento.

5.4. guia_vencida

Cliente não enviou comprovante no prazo.

5.5. obrigacao_concluida

Fluxo da obrigação finalizado.

6. Eventos de Tarefas e Agenda
6.1. tarefa_criada

Criada automaticamente ou manualmente.

6.2. tarefa_atualizada

Data-meta alterada, responsável trocado, status modificado.

6.3. tarefa_concluida

Responsável finalizou tarefa.

6.4. tarefa_atrasada

Data-meta ultrapassada.

6.5. tarefa_reaberta

Após análise da gestão.

7. Eventos de Pedidos (Cliente → Escritório)
7.1. pedido_aberto

Cliente abriu um pedido.

7.2. pedido_em_analise

Colaborador iniciou o atendimento.

7.3. pedido_aguardando_cliente

Solicitação de informação adicional.

7.4. pedido_concluido

Pedido finalizado.

7.5. anexo_adicionado_no_pedido

Cliente ou escritório anexou arquivo.

8. Eventos de Usuários
8.1. usuario_cadastrado

Novo usuário criado no sistema.

8.2. usuario_atualizado

Alteração de dados pessoais.

8.3. usuario_desativado

Desabilitado do sistema.

8.4. login_sucesso

Para logs de auditoria.

8.5. login_falha

Tentativa inválida (risco de ataque).

9. Eventos de Empresa
9.1. empresa_criada

Nova empresa cadastrada.

9.2. empresa_atualizada

Alteração de dados fiscais.

9.3. responsaveis_atualizados

Responsáveis por departamento alterados.

9.4. empresa_desativada

Conta do cliente desligada.

10. Eventos de Sistema / Internos
10.1. worker_falhou

Fila detectou erro crítico.

10.2. storage_indisponivel

Falha ao acessar S3/Minio.

10.3. scheduler_parado

Cron interno não executou.

10.4. importacao_realizada

Importações de XML/arquivos.

10.5. backup_concluido

Registro de rotina interna.

11. Relação de Eventos com Notificações

Eventos que podem disparar notificações:

Evento	Notifica Cliente?	Notifica Escritório?
documento_enviado	❌	✔
documento_invalido	✔	✔
documento_recusado	✔	✔
guia_enviada_ao_cliente	✔	❌
comprovante_enviado	❌	✔
pedido_aberto	❌	✔
pedido_aguardando_cliente	✔	❌
tarefa_atrasada	❌	✔
solicitacao_vencida	✔	✔
12. Relação de Eventos com Auditoria

Eventos sempre auditados:

acesso a arquivos sensíveis

download de documentos

mudanças de estado

alterações de dados pessoais

criação e exclusão de registros

envio de comprovantes

13. Eventos usados como gatilhos de automação
13.1. Rotina mensal

obrigacao_gerada

solicitacao_criada

13.2. Pipeline de validação

documento_enviado

documento_validado

documento_invalido

13.3. Atrasos

tarefa_atrasada

guia_vencida

solicitacao_vencida

13.4. Conclusão

tarefa_concluida

pedido_concluido

obrigacao_concluida

14. Futuro: Webhooks

Este catálogo será base do módulo:

/webhooks


Onde cada evento poderá ser enviado para:

ERP

CRM

automações do cliente

WhatsApp corporativo

ferramentas fiscais

15. Conclusão

Este arquivo define todos os eventos oficiais do sistema, tornando:

workflows previsíveis

auditoria completa

integrações possíveis

automações mais fáceis

notificações consistentes

Ele é a base para integrações internas, externas e para o módulo de webhooks futuro.