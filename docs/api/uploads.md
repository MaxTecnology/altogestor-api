# API Reference – Uploads & Arquivos (v1)

Este documento define o padrão oficial de **upload, download e manipulação de arquivos** usados em:

- Solicitações de documentos
- Documentos enviados pelo cliente
- Comprovantes de pagamento
- Anexos de pedidos
- Arquivos internos do escritório
- Logs/relatórios de processamento (futuro)

A padronização é essencial para garantir consistência entre backend, frontend e storage.

Prefixo padrão:

/api/v1/uploads


---

# 1. Conceitos Gerais

### Arquivo
Qualquer conteúdo enviado pelo cliente ou escritório:
- PDF
- Imagem (PNG, JPG)
- XML
- TXT/CSV
- ZIP

### Regras gerais de upload
- tamanho máximo por arquivo: **20MB**
- múltiplos arquivos permitidos
- para lotes grandes (XML), recomenda-se ZIP
- vírus devem ser verificados (quando disponível)
- nome original sempre preservado no metadata
- armazenado em storage (S3, Minio etc.)

---

# 2. Estrutura de Armazenamento (Storage)

Arquivos são armazenados assim:



/documentos/{empresa_id}/{solicitacao_id}/{arquivo_id}
/guias/{empresa_id}/{guia_id}/{arquivo_id}
/pedidos/{empresa_id}/{pedido_id}/{arquivo_id}
/interno/{departamento}/{ano}/{arquivo}


Cada arquivo possui metadados:

```json
{
  "id": 991,
  "nome_original": "nota_fiscal_1025.xml",
  "extensao": "xml",
  "tamanho_bytes": 84200,
  "mime_type": "application/xml",
  "usuario_id": 200,
  "empresa_id": 55,
  "tipo_contexto": "solicitacao_documento",
  "contexto_id": 8803,
  "storage_path": "documentos/55/8803/nota_fiscal_1025.xml",
  "created_at": "2025-11-05T14:10:55Z"
}

3. Endpoints
📌 3.1 – Upload de Arquivo (Cliente ou Escritório)
POST /uploads

Envio de arquivos com multipart/form-data.

Body (multipart)
Campo	Tipo	Descrição
file	binary	arquivo a enviar
empresa_id	number	empresa relacionada
tipo	string	documento, comprovante, pedido
contexto_id	number	solicitacao_id, guia_id ou pedido_id
Exemplos de tipos

Documento → tipo = "documento" + contexto_id = solicitacao_documento_id

Comprovante → tipo = "comprovante" + contexto_id = guia_id

Anexo de pedido → tipo = "pedido" + contexto_id = pedido_id

Exemplo de Requisição
POST /api/v1/uploads
Content-Type: multipart/form-data


Campos:

file: arquivo upload

empresa_id: 55

tipo: “documento”

contexto_id: 8803

Resposta 201
{
  "id": 991,
  "nome_original": "extrato_bancario.pdf",
  "extensao": "pdf",
  "tamanho_bytes": 824422,
  "empresa_id": 55,
  "tipo": "documento",
  "contexto_id": 8803,
  "url_download": "https://storage.../documentos/55/8803/991.pdf"
}

📌 3.2 – Download de Arquivo
GET /uploads/{arquivo_id}

Retorna o arquivo em streaming.

Exemplo

GET /api/v1/uploads/991


Resposta: download direto (PDF, XML, ZIP etc.)

📌 3.3 – Listar Arquivos por Contexto
GET /uploads/contexto

Query params:

Param	Descrição
tipo	documento / comprovante / pedido
contexto_id	id do documento/guia/pedido
Exemplo
GET /api/v1/uploads/contexto?tipo=documento&contexto_id=8803

Resposta
[
  {
    "id": 991,
    "nome_original": "xml_entrada.xml",
    "extensao": "xml",
    "criado_em": "2025-10-01T12:00:00Z",
    "usuario_id": 200
  }
]

📌 3.4 – Remover Arquivo
DELETE /uploads/{arquivo_id}

Regras:

Cliente só remove arquivos ANTES da validação

Escritório pode remover arquivos (auditado)

Registros vão para a auditoria

Exemplo
DELETE /api/v1/uploads/991


Resposta 204

Sem corpo.

4. Validação e Regras de Negócio
4.1. Tipos de arquivo permitidos

PDF

XML

TXT / CSV

PNG / JPG / JPEG

ZIP

4.2. Tamanho máximo
<= 20MB por arquivo
<= 100MB por requisição (somando múltiplos)

4.3. Conteúdos bloqueados

.exe

.bat

.dll

.js

.sh
(por segurança, evitar upload de scripts executáveis)

5. Segurança

Todos os uploads exigem token válido

URLs de download podem ser:

assinadas (URL temporária)

ou via streaming interno

Nenhum arquivo fica público

Auditoria completa de remoção e upload

6. Auditoria (Integrado ao módulo de logs)

Cada evento de upload gera:

acao: "arquivo_enviado"
modulo: "uploads"
usuario_id
empresa_id
contexto_id
arquivo_id


Remoção gera:

acao: "arquivo_removido"

7. Exemplos completos
Exemplo – Cliente envia documentos fiscais
POST /api/v1/uploads
tipo = documento
contexto_id = 8803
file = xml_saidas_outubro.zip

Exemplo – Escritório envia guia ao cliente
POST /api/v1/uploads
tipo = guia
contexto_id = 4520
file = guia_das_1025.pdf

Exemplo – Cliente envia comprovante
POST /api/v1/uploads
tipo = comprovante
contexto_id = 4520
file = comprovante_pagamento.jpg

Conclusão

Esta documentação padroniza todo uso de arquivos no sistema e viabiliza:

documentos enviados pelo cliente,

comprovantes,

anexos de pedidos,

anexos internos do escritório,

histórico preservado,

auditoria completa.