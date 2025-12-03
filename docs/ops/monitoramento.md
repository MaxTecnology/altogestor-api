# Monitoramento & Alertas – Guia Oficial de Observabilidade

Este documento descreve tudo que deve ser monitorado na plataforma:

- saúde dos serviços,
- desempenho,
- filas,
- jobs críticos,
- geração de obrigações,
- documentos atrasados,
- falhas de API,
- consumo de banco e storage,
- alertas e notificações ao time técnico.

Ele é essencial para operação 24/7, auditoria e confiabilidade do sistema.

---

# 1. Objetivos do Monitoramento

O monitoramento deve permitir:

1. **Detectar problemas rapidamente**
   - API fora do ar  
   - filas travadas  
   - erros no envio de guias  
   - jobs não executando  

2. **Evitar atrasos em obrigações fiscais**
   Ex.: DAS não enviado no dia 20.

3. **Evitar perda de dados**
   Ex.: uploads falhando, storage inacessível.

4. **Acompanhar uso e performance**
   - banco
   - CPU / memória
   - storage

5. **Gerar alertas previsíveis**
   - documentos atrasados
   - tarefas acumuladas
   - filas acima do limite

---

# 2. Ferramentas Recomendadas

### Monitoramento da aplicação
- **Sentry** (erros)
- **Grafana** (dashboards)
- **Prometheus** (métricas)
- **Loki** (logs)

### Monitoramento de infra
- **Netdata**
- **Zabbix**
- **UptimeRobot**
- **AWS CloudWatch** (se usar AWS)

---

# 3. O que deve ser monitorado

## 3.1. API (Backend)

| Métrica | Descrição |
|--------|-----------|
| Latência | tempo de resposta das rotas |
| Taxa de erro 4xx | problemas do usuário |
| Taxa de erro 5xx | problemas internos |
| Consumo de memória | estabilidade |
| Consumo de CPU | gargalos |
| Taxa de requisições/minuto | tráfego |
| Tempo de fila do servidor | sobrecarga |

---

## 3.2. Banco de Dados

Monitorar:

- conexões ativas  
- queries lentas  
- deadlocks  
- crescimento das tabelas principais  
- tamanho de índices  
- replicas (se houver)  
- CPU/IO do banco  
- uso de disco  

### Tabelas críticas para alertas
- `guias`  
- `documentos`  
- `solicitacoes_documentos`  
- `tarefas`  
- `pedidos`  
- `logs_auditoria`

---

## 3.3. Redis (Cache e Fila)

Monitorar:

- memória usada  
- jobs pendentes por fila  
- jobs com erro  
- jobs com retry infinito  
- workers fora do ar  
- fila parada (nenhum job concluído em X minutos)

### Alertas recomendados

| Condição | Alerta |
|---------|--------|
| fila `guias` > 10 jobs | amarelo |
| fila `guias` > 30 jobs | vermelho |
| fila `documentos` > 50 jobs | vermelho |
| worker inativo por > 2 min | crítico |
| taxa de falhas > 5% | crítico |

---

## 3.4. Scheduler (Cron interno)

É **obrigatório monitorar** se o scheduler rodou nos últimos 5 minutos.

Jobs essenciais:

- geração de solicitações mensais  
- envio de notificações  
- verificação de documentos atrasados  
- atualização de atrasos em guias  
- tarefas recorrentes  

### Alertas recomendados
- “Job X não executa há 10 minutos”  
- “Job de geração mensal falhou”  
- “Último schedule rodou há +5m”  

---

## 3.5. Storage (Arquivos)

Monitorar:

- disponibilidade do bucket  
- tempo de resposta  
- permissão negada (ACL errado)  
- uploads falhando  
- % de crescimento mensal  

### Alertas
- Storage inacessível → **crítico**  
- Taxa de erro em upload > 2% → **alto**  
- Crescimento anormal > 30% em 24h → possível abuso  

---

# 4. Alertas Operacionais Internos

## 4.1. Documentos Pendentes/Atrasados

O sistema deve emitir alertas quando:

- uma empresa fica com > X documentos atrasados  
- solicitações vencidas acumulam  
- documentos críticos do mês não foram enviados  

Pode ser:
- notificação interna  
- envio por WhatsApp/E-mail (opcional)  
- alerta no dashboard do escritório  

---

## 4.2. Obrigações & Guias

Alertas importantes:

- guia não gerada no prazo  
- guia vencendo hoje  
- guia atrasada  
- comprovante não enviado  

Esses alertas alimentam dashboards e relatórios.

---

## 4.3. Tarefas & Agenda

Alertas:
- tarefas atrasadas acima do limite  
- colaboradores com carga excessiva  
- tarefas próximas da data-meta  
- tarefas do dia não iniciadas  

---

## 4.4. Pedidos

Alertas:
- pedidos parados em EM_ANALISE por > 48h  
- pedidos aguardando cliente > X dias  
- pedidos críticos sem responsável  

---

# 5. Eventos Críticos (Alto Risco)

Listar eventos que DEVEM gerar alerta para o time técnico:

- **API retornando 5xx repetidamente**
- **fila travada**
- **worker offline**
- **storage indisponível**
- **conexões DB acima do limite**
- **migrations falhando**
- **erro na geração de obrigações**
- **falha no envio de guia**
- **falha no envio de e-mail/WhatsApp**
- **consumo de CPU > 90%**
- **latência de API > 800ms por 5 minutos**

---

# 6. Dashboards Recomendados

## 6.1. Dashboard Operacional (Escritório)
- documentos pendentes
- guias vencendo hoje
- tarefas atrasadas
- pedidos críticos
- status das filas
- status do scheduler

## 6.2. Dashboard DevOps
- status da API
- latência
- uso de CPU/memória
- erros por minuto (Sentry)
- consumo do banco
- tamanho das filas
- uso de storage

---

# 7. Logs para Monitorar

## 7.1. Logs Críticos (alta prioridade)
- exceptions (Sentry)
- falha de job
- falha de fila
- falha de scheduler
- falha ao acessar storage
- falha ao gerar guia

## 7.2. Logs Importantes
- warnings
- performance lenta
- processamento de documento grande
- importações via XML

## 7.3. Logs Auxiliares
- acesso
- auditoria
- ações do cliente
- tentativas de login

---

# 8. Notificações Internas (DevOps)

### Meios recomendados:
- Slack / Discord  
- WhatsApp (via integração)  
- E-mail  
- Webhook personalizado  

### Temas de alerta:
- falhas críticas  
- fila travada  
- storage inacessível  
- cron atrasado  
- DB indisponível  

---

# 9. Frequência de Monitoramento

| Item | Frequência |
|------|------------|
| API Healthcheck | 30s |
| Banco (conexões) | 1 min |
| Filas | contínuo |
| Scheduler | 1 min |
| Storage Health | 5 min |
| Tarefas atrasadas | 15 min |
| Documentos atrasados | 30 min |
| Obrigações pendentes | 1x/dia |
| Pedidos críticos | 20 min |

---

# 10. Conclusão

Este documento descreve a política completa de **monitoramento, alertas e observabilidade** da plataforma.

Ele garante:

- estabilidade operacional,  
- prevenção de falhas,  
- segurança fiscal,  
- rastreabilidade,  
- confiabilidade.  

É obrigatório para DevOps, backend, suporte e automações internas.

