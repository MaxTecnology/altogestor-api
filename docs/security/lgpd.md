# Política de LGPD – Gestão de Dados Pessoais e Sensíveis

Este documento descreve como a plataforma processa, armazena e protege dados pessoais e sensíveis, em conformidade com a **Lei Geral de Proteção de Dados (Lei 13.709/2018)**.

A aplicação é utilizada por:

- escritórios de contabilidade,
- colaboradores internos,
- clientes (empresas),
- usuários externos vinculados a essas empresas.

---

# 1. Princípios da LGPD

O sistema segue os seguintes princípios:

1. **Finalidade:** tratamento somente para propósitos legítimos, específicos e explícitos.
2. **Adequação:** uso compatível com a finalidade informada.
3. **Necessidade:** coleta mínima necessária ao funcionamento do sistema.
4. **Livre acesso:** usuários podem visualizar seus dados.
5. **Qualidade dos dados:** exatidão, clareza e atualização.
6. **Transparência:** comunicação clara com o titular.
7. **Segurança:** medidas técnicas para reduzir riscos.
8. **Prevenção:** adoção de práticas para evitar incidentes.
9. **Não discriminação:** dados não são utilizados de forma discriminatória.
10. **Responsabilização:** registros de auditoria, logs e rastreabilidade.

---

# 2. Categorias de Dados Tratados

A plataforma trata 3 categorias principais:

---

## 2.1. Dados Pessoais Comuns

### Exemplos
- Nome completo
- E-mail
- Telefone
- CPF
- RG
- Cargo/função
- Endereço comercial
- Dados de login

### Objetivo do Tratamento
- autenticação e acesso ao sistema,
- envio de notificações,
- composição da identificação de documentos,
- assinaturas digitais,
- registro de histórico e auditoria.

---

## 2.2. Dados Pessoais de Empresa (PJ)

### Exemplos
- Razão social
- Nome fantasia
- CNPJ
- Inscrição estadual/municipal
- Endereço
- Responsáveis internos por departamento

### Objetivo
Gestão contábil, fiscal e trabalhista da empresa.

---

## 2.3. Dados Sensíveis (LGPD – Art. 5º, II)

A plataforma **não solicita diretamente** dados sensíveis, MAS eles podem aparecer em:

- anexos de pedidos (ex.: informações de saúde)
- documentos enviados pelo cliente (ex.: ASOs, laudos)
- dados trabalhistas (ex.: dependentes)

### Tratamento de Dados Sensíveis
- criptografia em repouso
- acesso restrito por perfil (DP)
- logs completos para acesso e download
- download sempre autenticado
- nunca expor publicamente

---

# 3. Base Legal

A base legal utilizada depende do contexto:

### 3.1. Execução de contrato (Art. 7º, V)
Para prestação de serviços contábeis, fiscais e trabalhistas.

### 3.2. Obrigação legal ou regulatória (Art. 7º, II)
Tratamento exigido para cumprimento de obrigações:

- fiscais (ISS, ICMS, DAS, DEFIS),
- contábeis,
- trabalhistas (eSocial, GFIP, FGTS).

### 3.3. Legítimo interesse (Art. 7º, IX)
- envio de lembretes de documentos,
- notificações de guias,
- pedidos pendentes.

### 3.4. Consentimento (Art. 7º, I) — opcional
Somente para funcionalidades que extrapolam obrigações legais.

---

# 4. Direitos do Titular

A plataforma permite implementar os seguintes direitos do titular:

1. **Confirmação de tratamento**
2. **Acesso aos dados**
3. **Correção de dados incompletos**
4. **Anonimização (quando cabível)**
5. **Eliminação (casos sem obrigação legal de retenção)**
6. **Portabilidade**
7. **Informação sobre compartilhamento**
8. **Revogação do consentimento (quando aplicável)**

### Como solicitar
Titulares podem solicitar ações via:

- Usuário cliente admin  
ou  
- Canal do escritório (e-mail ou suporte)

---

# 5. Retenção e Eliminação de Dados

A retenção segue a necessidade legal:

| Tipo de Dado | Retenção |
|--------------|----------|
| Documentos contábeis/fiscais | **5 a 10 anos** (exigências legais) |
| Arquivos enviados pelo cliente | 5 anos (pode ser mais, conforme área contábil) |
| Histórico de auditoria | ilimitado |
| Logs de acesso | 2 anos |
| Contas inativas | 5 anos após encerramento |

### Eliminação/Anonimização
Para dados que **não possuem obrigatoriedade legal**:

- anonimização irreversível (hash sem referência),
- remoção final após período acordado.

---

# 6. Segurança & Proteção

A plataforma aplica:

### 6.1. Proteções Técnicas
- **Criptografia** (armazenamento seguro)
- **TLS 1.2+** para todas as conexões
- **URLs de download assinadas** e expiram automaticamente
- **Controle de acesso baseado em papéis (RBAC)**
- **Auditoria completa** em nível de entidade e estado
- **Proteção contra ataques de força bruta**
- **Rate limiting** para endpoints sensíveis
- **Firewall de aplicação**

### 6.2. Proteções Organizacionais
- acesso a dados sensíveis restrito à equipe de DP,
- políticas internas de confidencialidade,
- controle de permissões granular por empresa,
- logs completos de acesso e alteração.

---

# 7. Compartilhamento de Dados

A plataforma **não vende, compartilha ou disponibiliza dados** a terceiros, exceto quando:

### 1. Exigido por lei
- Receita Federal
- Prefeituras
- Sefaz
- Ministério do Trabalho
- CAIXA (FGTS)
- Previdência

### 2. Integrações configuradas pelo cliente/escritório
Exemplos:
- ASAAS (boletos e pagamentos)
- WhatsApp API
- E-mail transacional
- OneDrive/SharePoint (importação)

Sempre exigindo:
- meios seguros,
- autenticação,
- logs de auditoria.

---

# 8. Responsabilidades

### Escritório Contábil (Controlador)
Responsável pela decisão sobre:
- finalidades,
- bases legais,
- compartilhamento,
- retenção,
- resposta aos titulares.

### Plataforma (Operador)
Responsável por:
- providenciar meios técnicos,
- garantir segurança,
- evitar vazamentos,
- executar tratamento conforme instruções do controlador.

---

# 9. Incidentes e Notificações

Em caso de incidente:
- registrar em log interno,
- reportar ao escritório (controlador),
- avaliar risco aos titulares,
- acionar medidas corretivas.

O escritório é responsável por comunicar a ANPD, quando necessário.

---

# 10. Anonimização e Minimização

A plataforma aplica:

- mascaramento de CPF/CNPJ quando possível,
- logs sem dados sensíveis,
- exclusão de anexos substituídos por novos,
- arquivamento de documentos antigos em storage frio (S3 Glacier).

---

# 11. Storage Seguro

Todos os arquivos são armazenados usando:

- storage criptografado,
- ACLs privadas,
- rotacionamento de chaves (KMS quando disponível),
- URLs assinadas com expiração.

---

# 12. Auditoria (Integração)

Operações que sempre geram auditoria:

- acesso a arquivos sensíveis,
- download de documentos,
- envio ou alteração de anexos,
- mudança de estados,
- alteração de dados pessoais.

As entradas são imutáveis e mantidas por prazo indeterminado.

---

# 13. Conclusão

Este documento formaliza a política de LGPD da plataforma, garantindo:

- tratamento correto dos dados,
- segurança,
- rastreamento,
- transparência,
- alinhamento com obrigações legais e fiscais.

É obrigatório que desenvolvedores, colaboradores e parceiros entendam e respeitem este documento.

