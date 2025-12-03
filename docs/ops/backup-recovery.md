# Backup & Recovery – Procedimentos Oficiais

Este documento define o processo completo de **backup, retenção e restauração** da plataforma.  
Por se tratar de um sistema contábil e fiscal, a integridade dos dados é **obrigatória por lei**.

Inclui:

- Backup do banco de dados  
- Backup dos arquivos (documentos, comprovantes, anexos)  
- Backup de configurações e logs  
- Retenção mínima legal  
- Estratégias de restauração (por empresa, por módulo ou completa)  
- Procedimentos operacionais  

---

# 1. Objetivos do Backup

Os backups visam:

1. **Retenção legal** (OBRIGAÇÃO CONTÁBIL / FISCAL)
2. **Proteção contra perda de dados**
3. **Restaurar sistema em caso de falhas**
4. **Restaurar documentos enviados por clientes**
5. **Manter histórico confiável**
6. **Atender auditorias**

---

# 2. Itens que Devem ser Backupados

## 2.1. Banco de Dados
Inclui:
- informações de empresas
- usuários
- documentos e seus metadados
- solicitações
- tarefas
- obrigações e guias
- pedidos
- históricos e auditorias
- logs
- configurações

## 2.2. Storage (Arquivos)
Inclui:
- documentos enviados pelo cliente (XML, PDF, imagens)
- comprovantes de pagamento
- anexos de pedidos
- relatórios internos
- arquivos do escritório

## 2.3. Configurações do Sistema
Inclui:
- variáveis de ambiente (env)
- chaves secretas
- parâmetros fiscais por empresa
- modelos de notificação
- modelos de documentos e pedidos

## 2.4. Logs
Principalmente:
- logs de auditoria
- logs de acesso
- logs de jobs

---

# 3. Frequência de Backup

A plataforma deve seguir o seguinte cronograma:

| Item | Frequência | Tipo |
|------|------------|------|
| Banco de Dados | **diário** | Full |
| Banco (incremental) | a cada 6h | Binlog/WAL |
| Storage | diário | Diferencial |
| Storage (full) | semanal | Completo |
| Logs importantes | diário | Export |
| Configurações | ao alterar | Snapshot |

---

# 4. Retenção (Storage/DB)

Recomendações de retenção baseadas na legislação:

| Tipo | Retenção |
|------|----------|
| Documentos contábeis | **5 anos (mínimo)** |
| Documentos fiscais | **5 anos (mínimo)** |
| Documentos trabalhistas | **5–10 anos** |
| Logs de auditoria | **ilimitado** |
| Logs de acesso | 2 anos |
| Backups completos | 12 meses |
| Snapshots | 30 dias |
| Backups incrementais | 7 dias |

> Para evitar riscos legais, recomenda-se **não excluir documentos contábeis** antes de 5 anos.

---

# 5. Local de Armazenamento dos Backups

Recomendação oficial:

| Ambiente | Local |
|----------|--------|
| DEV | local (opcional) |
| HOMOLOGAÇÃO | S3/Minio (bucket próprio) |
| PRODUÇÃO | S3 com criptografia KMS |

### Camadas adicionais:
- backup na própria região (S3 Standard)
- cópia para região secundária (S3 Glacier)
- redundância para auditoria (Cold Archive)

---

# 6. Backup do Banco de Dados

## 6.1. MySQL (exemplo)

Full backup:

mysqldump --single-transaction --compress --quick
-u $DB_USER -p$DB_PASS $DB_NAME > backup.sql


Incremental (binlog):



mysqlbinlog binlog.000123 > incremental.sql


## 6.2. PostgreSQL (se usado)

Full:



pg_dump -Fc -f backup.dump $DB_NAME


Incremental (WAL):



pg_basebackup ...


---

# 7. Backup do Storage

Estratégia recomendada:

### Full semanal
Copiar **todos** os arquivos do bucket S3/Minio para:



s3://backup/full/yyyy-mm-dd/


### Diferencial diário
Copiar apenas arquivos novos/alterados:



s3://backup/differential/yyyy-mm-dd/


Ferramentas recomendadas:
- `aws s3 sync`
- `rclone sync`
- `minio client (mc)`

---

# 8. Plano de Recovery (Restauro)

## 8.1. Tipos de Restauração

### 1. **Restauração Completa**
Para falhas graves (perda de servidor).
- restaurar full DB
- aplicar incrementais
- restaurar storage
- reiniciar serviços

### 2. **Restauração por Empresa**
Cenário comum em contabilidade.
- recuperar documentos de empresa específica
- recuperar solicitações
- recuperar guias
- restaurar pedidos

Processo:
- filtrar pastas no storage por empresa
- restaurar registros via snapshot do BD

### 3. **Restauração por Módulo**
Exemplo:
- recuperar apenas guias e comprovantes
- restaurar solicitações de um período

### 4. **Restauração de Arquivo Específico**
Exemplo:
- cliente apagou arquivo por engano
- recuperar versão do dia anterior

---

# 9. Procedimento de Recuperação Completa

## 9.1. Passo a Passo

1. **Parar Workers**


php artisan queue:pause


2. **Restaurar banco**
- aplicar backup full
- aplicar binlog/WAL incremental

3. **Reindexar banco (se necessário)**

4. **Restaurar storage**


aws s3 sync backup/full/yyyy-mm-dd s3://bucket-principal/


5. **Subir API**

6. **Rodar migrações pendentes**


php artisan migrate --force


7. **Reativar as filas**


php artisan queue:resume


8. **Executar validação**
- checar contagens de registros
- checar integridade dos arquivos
- rodar healthcheck

---

# 10. Testes de Backup

Recomenda-se testar:

- **restauro completo** → 1x por mês  
- **restauro por empresa** → 1x por trimestre  
- **restauro de arquivo isolado** → 1x por semana (automático)  

---

# 11. Auditoria de Backup

A seguinte tabela deve ser mantida:

| Campo | Descrição |
|------|------------|
| id | identificador |
| tipo | full / differential / incremental |
| tamanho | total em MB/GB |
| local | destino S3 / Minio |
| inicio | início do backup |
| fim | término |
| status | sucesso/falha |
| mensagem | logs |

---

# 12. Checklist do Gestor

### Antes:
- verificar espaço em disco
- confirmar credenciais S3
- confirmar cron jobs ativos

### Durante:
- monitorar tamanho do backup
- monitorar uso do banco
- verificar latência

### Depois:
- validar integridade
- registrar auditoria
- testar recuperação

---

# 13. Conclusão

Este documento formaliza o processo oficial de **backup e recuperação** da plataforma.  
Ele garante:

- integridade dos dados contábeis,
- retenção legal,
- proteção contra perdas,
- confiabilidade operacional,
- auditoria completa.

É indispensável para produção e homologação.