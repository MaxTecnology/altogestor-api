# Healthcheck & Observabilidade – Guia Oficial

Este documento define os **endpoints de saúde**, **métricas**, **verificações internas**, e a estrutura de **observabilidade** da plataforma.

Essencial para:
- DevOps  
- Monitoramento  
- Load Balancing  
- Auto Scaling  
- Kubernetes / Docker  

---

# 1. Objetivo

Integrar ao sistema mecanismos para:

- verificar se os serviços estão saudáveis  
- detectar falhas antes que impactem clientes  
- permitir decisões automáticas (restart, alerta, failover)  
- monitorar performance  
- expor métricas técnicas  

---

# 2. Endpoints de Healthcheck

Existem dois grupos:

---

# 📌 2.1. Endpoint Básico (Liveness Probe)

### **GET /api/health**

Verifica se a API está no ar (não faz checks profundos).

### Resposta:

```json
{
  "status": "ok",
  "version": "1.0.3",
  "timestamp": "2025-11-28T14:20:00Z"
}

Uso recomendado:

Nginx upstream

Docker HEALTHCHECK

Kubernetes Liveness Probe

📌 2.2. Endpoint Completo (Readiness Probe)
GET /api/health/full

Este endpoint verifica:

Serviço	Verificação
Banco de Dados	teste de conexão
Redis	PING
Fila	latência + jobs pendentes
Storage	teste de leitura mínima
Mail service	handshake simples
Scheduler	última execução
Workers	quantidade ativa
Exemplo de Resposta:
{
  "status": "ok",
  "database": "ok",
  "redis": "ok",
  "queue": {
    "default": 2,
    "documentos": 0,
    "guias": 1
  },
  "storage": "ok",
  "mail": "ok",
  "scheduler_last_run": "2025-11-28T14:00:00Z",
  "workers": {
    "ativos": 4,
    "esperados": 4
  },
  "version": "1.0.3"
}

3. Checks Internos

Cada item do healthcheck completo segue regras:

3.1. Banco de Dados

Executar uma query simples:

SELECT 1;


Falhas:

sem resposta → status "critical"

lentidão excessiva (>200ms) → status "warning"

3.2. Redis

Executar:

PING


Falhas comuns:

latência > 50ms

falta de memória

reset do redis

3.3. Fila (Queue)

Verificar:

workers ativos

jobs pendentes

jobs com erro

tempo médio de processamento

Critérios:
Condição	Level
jobs > 10	warning
jobs > 50	critical
worker inativo	critical
retries frequentes	alerta
3.4. Storage

Testes:

listar um bucket pequeno (default)

verificar acesso do backend

verificar tempo de resposta

3.5. Mail Service

Testar SMTP handshake / ping:

porta acessível

autenticação básica (sem envio real)

3.6. Scheduler

Scheduler deve atualizar uma tabela scheduler_monitor a cada execução:

UPDATE scheduler_monitor SET last_run = NOW();


O healthcheck compara last_run com o tempo atual.

Regras:

diferença > 5m → warning

diferença > 10m → critical

3.7. Workers

Backend registra workers ativos em workers_monitor.

Dados:

worker_id

fila

last_heartbeat

Heartbeat enviado a cada 30s.

Regras:

última atualização > 60s → worker inativo

4. Observabilidade – Métricas Exportadas

Sistema deve expor endpoint Prometheus (futuro):

GET /metrics

Exemplo de métricas:

api_requests_total 15420
api_requests_5xx 12
api_requests_latency_ms 130
queue_jobs_pending{fila="documentos"} 20
queue_jobs_errors{fila="guias"} 2
database_connections 18
storage_latency_ms 40
scheduler_delay_seconds 45


Essas métricas alimentam:

Grafana dashboards

Alarmes Prometheus

Insights de desempenho

5. Níveis de Severidade

Cada healthcheck retorna:

Level	Significado
OK	tudo funcionando
WARNING	risco, mas ainda funcional
CRITICAL	intervenção imediata necessária
OFFLINE	serviço parado
6. Integração com Load Balancers
Nginx
location /health {
    proxy_pass http://app:9000;
}

Dockerfile
HEALTHCHECK CMD curl -f http://localhost:9000/health || exit 1

Kubernetes
livenessProbe:
  httpGet:
    path: /api/health
    port: 9000

readinessProbe:
  httpGet:
    path: /api/health/full
    port: 9000

7. Alertas Automáticos
Quando critical:

Slack interno

WhatsApp técnico

E-mail imediato

Retentativas automáticas

Restart de services

Eventos críticos:

DB offline

Redis offline

Fila parada

Worker travado

Storage inacessível

Scheduler não executando

8. Log Especial de Healthcheck

Criar tabela:

health_logs


Campos:

id

tipo (database / redis / queue / storage / api)

status (ok/warning/critical)

mensagem

timestamp

Permite histórico de disponibilidade.

9. Checklist de Maturidade de Observabilidade
Item	Status
Health básico	✔ completo
Health completo	✔ completo
Métricas Prometheus	opcional
Dashboards Grafana	recomendado
Alertas Sentry	obrigatório
Monitor fila	obrigatório
Monitor scheduler	obrigatório
Log de health	recomendado
10. Conclusão

Este documento padroniza todo o processo de:

healthcheck

readiness

monitoramento técnico

observabilidade

métricas

alarmes internos

Ele é fundamental para garantir que o sistema se mantenha estável, monitorado e confiável.