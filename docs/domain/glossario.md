# Glossário de Domínio – Plataforma Contábil

Este glossário define todos os termos usados nos módulos da plataforma.  
É fundamental para o alinhamento entre equipe, documentação, desenvolvimento e integrações.

---

# 1. Entidades Principais

---

## **Empresa**
Representa uma empresa cliente do escritório contábil.

**Sinônimos:** cliente, entidade, CNPJ atendido.

**Campos característicos:**
- Razão social, nome fantasia, CNPJ
- Regime tributário
- Responsável por cada departamento (fiscal/contábil/DP)
- Parâmetros fiscais e operacionais

---

## **Usuário do Escritório**
Colaborador interno que acessa o painel do escritório.

Pode ter perfis como:
- sócio_admin,
- gestor,
- analista_fiscal,
- analista_contábil,
- analista_dp.

---

## **Usuário do Cliente**
Usuário que acessa o **Portal do Cliente**.

Perfis:
- cliente_admin,
- cliente_financeiro,
- cliente_basico.

Cada usuário pode ter vínculos com **uma ou mais empresas**.

---

# 2. Documentos

---

## **Modelo de Documento**
É a definição do que deve ser enviado pelo cliente regularmente.

Exemplos:
- XML de Entrada
- XML de Saída
- Relatório de Faturamento
- Movimento Contábil
- Extrato Bancário

Define:
- periodicidade (mensal/anual/único),
- documentos obrigatórios/opcionais,
- departamento responsável.

---

## **Solicitação de Documento**
Pedido formal (automático ou manual) para o cliente enviar documentos referentes a um período.

Exemplo:
> Solicitar XML de Saída de outubro/2025 para a empresa Padaria Alfa.

Pode ter status:
- PENDENTE  
- PARCIAL  
- EM_VALIDACAO  
- COMPLETO  
- INCOMPLETO  
- RECUSADO

---

## **Documento Enviado**
Arquivo enviado pelo cliente, relacionado a uma solicitação.

Metadados importantes:
- nome original,
- tipo (pdf, xml, imagem),
- origem (portal, integração, importação),
- usuário cliente que enviou.

---

## **Validação de Documento**
Processo interno do escritório que analisa um documento enviado e o classifica como:

- COMPLETO  
- EM_VALIDACAO  
- INCOMPLETO  
- RECUSADO

Toda troca de estado gera registro no histórico.

---

# 3. Obrigações & Guias

---

## **Tipo de Obrigação**
Tipo abstrato que se repete conforme periodicidade e regra fiscal.

Exemplos:
- DAS
- DEFIS
- ISS
- DCTF
- FGTS
- DIRF

Define:
- periodicidade (mensal/anual),
- departamento responsável,
- regras gerais.

---

## **Configuração de Obrigação por Empresa**
Define **como** cada empresa atende cada obrigação.

Exemplo:
- Empresa X – DAS:
  - vencimento dia 20,
  - gerar guia automaticamente,
  - responsável: Analista Fiscal João.

---

## **Guia Fiscal**
Documento referente ao pagamento de uma obrigação.

Exemplos:
- Guia DAS 10/2025
- Guia ISS 10/2025
- DCTF 2025–10

Campos importantes:
- competência,
- valor,
- data de vencimento,
- status (GERADA, ENVIADA, PAGA, ATRASADA).

---

## **Comprovante de Pagamento**
Documento enviado pelo cliente comprovando o pagamento de uma guia.

Pode ser validado internamente pelo escritório.

---

# 4. Tarefas & Agenda

---

## **Modelo de Tarefa**
Configuração que define **como gerar** tarefas automáticas.

Exemplo:
- “Apurar DAS ME”
  - gerar todo mês,
  - 3 dias úteis antes do vencimento,
  - vinculado ao tipo de obrigação “DAS”.

---

## **Tarefa**
Instância concreta gerada para uma empresa em uma competência.

Exemplo:
> Tarefa: “Apurar DAS” – Empresa Padaria Alfa – Competência 10/2025

Status:
- EM_ABERTO  
- EM_ANDAMENTO  
- AGUARDANDO_CLIENTE  
- CONCLUIDA  
- ATRASADA  
- CANCELADA  

---

## **Agenda / Calendário**
Visão consolidada de tarefas:

- por dia,
- por colaborador,
- por empresa,
- atrasadas,
- do mês.

---

# 5. Pedidos & Formulários

---

## **Modelo de Pedido**
Definição de solicitações pré-configuradas que o cliente pode abrir.

Exemplos:
- Alteração de contrato social
- Abertura de empresa
- Solicitação de balanço
- Carta de exclusão
- Solicitação de emissão de nota

Define:
- campos dinâmicos,
- documentos obrigatórios.

---

## **Campo do Modelo de Pedido**
Campo dinâmico que o cliente preenche:

Exemplos:
- “Nome do sócio”
- “CPF do sócio”
- “Capital social”
- “Data do evento”

Tipos possíveis:
- texto,
- número,
- data,
- CPF/CNPJ,
- lista (select),
- booleano,
- e outros.

---

## **Pedido**
Solicitação aberta pelo cliente.

Tem:
- modelo,
- empresa,
- anexos,
- campos preenchidos,
- responsável interno,
- histórico.

Status:
- ABERTO  
- EM_ANALISE  
- AGUARDANDO_CLIENTE  
- CONCLUIDO  
- CANCELADO  

---

## **Documento do Pedido**
Anexo enviado para um pedido (ex.: RG do sócio).

---

# 6. Notificações

---

## **Notificação**
Ação de comunicação via:

- e-mail,
- WhatsApp,
- SMS,
- notificação interna.

Pode ser:
- manual (disparada pelo analista),
- automática (via evento de negócio),
- baseada em template.

---

## **Template de Notificação**
Modelo pré-formatado contendo:
- assunto,
- corpo com variáveis,
- canal: email/whatsapp.

Exemplo:
> “Olá {{nome_cliente}}, sua guia {{competencia}} está disponível.”

---

## **Evento de Negócio**
Gatilho interno que dispara notificações automaticamente.

Exemplos:
- guia_gerada
- documentos_atrasados
- pedido_atualizado
- tarefa_atrasada

---

# 7. Auditoria & Segurança

---

## **Histórico (Log de Alterações)**
Registro de cada mudança de estado importante nos módulos:

- documentos,
- guias,
- tarefas,
- pedidos.

Inclui:
- estado anterior,
- estado novo,
- data,
- usuário responsável.

---

## **Permissão (Role Action)**
Regra que define se o usuário pode:
- ver,
- editar,
- validar,
- enviar,
- concluir.

Baseada em:
- tipo de usuário (cliente/escritório),
- perfil (role),
- empresa vinculada,
- departamento responsável.

---

# 8. Processos e Períodos

---

## **Competência**
Período contábil/fiscal que representa o mês/ano de referência.

Formato: `YYYY-MM`

Exemplo: `2025-10`.

---

## **Data Meta**
Data calculada para conclusão da tarefa (com ou sem dias úteis).

---

## **Periodicidade**
Repetição da obrigação ou do documento:

- mensal,
- trimestral,
- anual,
- sob demanda.

---

# 9. Gatilhos e Integrações (Futuro)

---

## **Integração de Importação**
Entrada automática de documentos via API, S3, OneDrive, FTP etc.

---

## **Integração Financeira**
Geração de cobranças, boletos ou PIX através de ASAAS ou outro provedor.

---

## **Webhook**
Chamadas automáticas para sistemas externos:

- “Guia gerada”
- “Pedido concluído”
- “Documentos pendentes”
- “Tarefa atrasada”

---

# Conclusão

Este glossário formaliza os termos de domínio fundamentais da plataforma e deve ser utilizado por:

- desenvolvedores (backend/frontend),
- UX,
- documentação da API,
- geração via IA,
- integração com parceiros.

Qualquer novo termo introduzido no sistema deve ser registrado aqui para manter consistência.

