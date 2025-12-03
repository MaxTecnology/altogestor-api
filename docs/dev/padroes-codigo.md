# Padrões de Código – Guia Oficial de Desenvolvimento

Este documento estabelece **padrões obrigatórios** para desenvolvimento no backend e frontend do sistema.

O objetivo é manter:

- consistência
- reutilização
- legibilidade
- facilidade de manutenção
- compatibilidade com Codex/IA
- arquitetura escalável
- testes automatizados previsíveis

Aplica-se a todos os módulos da plataforma.

---

# 1. Arquitetura de Código

O backend segue arquitetura **Service Layer + Controllers + Repositories + DTOs**.

### Estrutura geral recomendada:

app/
├── Http/
│ ├── Controllers/
│ ├── Middleware/
│ └── Requests/
├── Services/
├── Repositories/
├── DTO/
├── Models/
├── Actions/
├── Jobs/
├── Listeners/
├── Events/
├── Policies/
└── Exceptions/


---

# 2. Controllers – Regras

Controllers devem:

- ser *finos*  
- conter **mínima lógica**
- chamar **Services**, nunca acessar Model direto
- retornar sempre via **API Response padrão**

### Estrutura:

```php
class DocumentoController extends Controller
{
    public function store(DocumentoRequest $request)
    {
        $result = $this->service->criarDocumento($request->validated());
        return ApiResponse::success($result);
    }
}

3. Services – Regras

Services contêm toda a regra de negócio.

Características:

Sem acesso direto ao Request

Sem retorno de Response/JSON

Somente lógica de negócio

Usam Repositories para persistência

Exemplo:
class DocumentoService
{
    public function criarDocumento(array $dados): DocumentoDTO
    {
        $documento = $this->repository->criar($dados);

        event(new DocumentoEnviado($documento));

        return DocumentoDTO::fromModel($documento);
    }
}

4. Repositories – Regras

Responsáveis por interagir com o banco.

Apenas operações CRUD

Sem regras de negócio

Sem lógica complexa

Exemplo:
class DocumentoRepository
{
    public function criar(array $dados): Documento
    {
        return Documento::create($dados);
    }
}

5. DTOs – Data Transfer Objects

Objetivo:

padronizar respostas

encapsular dados

evitar expor Models diretamente

Exemplo:
class DocumentoDTO
{
    public static function fromModel(Documento $doc)
    {
        return [
            'id' => $doc->id,
            'empresa_id' => $doc->empresa_id,
            'tipo' => $doc->tipo,
            'status' => $doc->status,
            'criado_em' => $doc->created_at,
        ];
    }
}

6. API Responses

Toda resposta deve seguir:

{
  "success": true,
  "data": { ... },
  "message": "Operação realizada com sucesso",
  "errors": null
}


Erros:

{
  "success": false,
  "message": "Erro ao criar documento",
  "errors": {
    "empresa_id": ["empresa não encontrada"]
  }
}

Classe:
ApiResponse::success($data);
ApiResponse::error($message, $errors);

7. Exceptions Padronizadas

Criar exceções específicas:

app/Exceptions/DocumentoInvalidoException.php
app/Exceptions/PermissaoNegadaException.php


Todas devem herdar de uma Exception base.

8. Eventos & Listeners

Eventos seguem o padrão definido em events/eventos-negocio.md.

Exemplo:
DocumentoEnviado
DocumentoValidado
GuiaEnviada
PedidoConcluido


Todos chamados dentro de Services.

9. Jobs – Filas e Processamento Assíncrono

Regra:

nenhuma lógica pesada dentro do controller

sempre delegar para Job

Padrões:
class ProcessarDocumentoJob implements ShouldQueue
{
    public function handle()
    {
        $this->service->processarDocumento($this->documento);
    }
}

10. Logs – Padronização

Logs estruturados:

Log::info('documento_processado', [
  'documento_id' => $id,
  'empresa_id' => $empresa,
  'usuario_id' => $usuario,
]);


Níveis:

info → sucesso

warning → risco leve

error → falhas

critical → falhas graves (fila, storage, banco)

11. Organização de Arquivos
Cada módulo deve ter:
/Controllers
/Services
/Repositories
/DTO
/Requests
/Listeners
/Events


Exemplo:

/modules/Documentos

12. Padrões de Modelos (Models)

Cada Model deve conter:

casts

fillable

relationships bem definidos

scopes reutilizáveis (não regras de negócio)

Exemplo:
class Documento extends Model
{
    protected $fillable = [
        'empresa_id',
        'usuario_id',
        'status',
        'tipo',
    ];

    public function empresa()
    {
        return $this->belongsTo(Empresa::class);
    }
}

13. Validações (Requests)

Validações sempre em classes dedicadas:

DocumentoRequest
GuiaStoreRequest
TarefaUpdateRequest


Nunca dentro do controller.

14. Convenções de Código
Nomes

controllers → XxxController

services → XxxService

repositories → XxxRepository

requests → XxxRequest

jobs → XxxJob

DTO → XxxDTO

eventos → XxxEvent

Nomes de métodos

criar

atualizar

remover

buscarPorId

listar

processar

validar

15. Padronização de Módulos

Cada módulo deve ter:

/routes
/validators
/policies
/resources


E seguir a mesma estrutura dos módulos principais já documentados.

16. Segurança no Código

Regras:

nunca confiar em dado externo

sempre usar Request->validated()

sanitizar strings

limitar uploads

limitar tamanho de arrays (ex. XMLs grandes)

17. Testes
Tipos:

unitários (services)

integração (controllers)

ponta a ponta (módulos críticos)

testes de fila (jobs)

testes de performance (opcional)

Convenções:

dado de teste sempre prefixado com tests/Fixtures

nomes de testes claros:

test_cliente_envia_documento()

test_tarefa_atrasada_dispara_evento()

18. Estilo e Convenções Gerais
✔ Variáveis em inglês
✔ Comentários curtos e claros
✔ Sem regras de negócio em controller
✔ Sem SQL bruto dentro do controller
✔ Sem lógica duplicada
✔ Reutilizar Services e DTOs
✔ Logs estruturados
✔ Erros padronizados
✔ Respostas padronizadas
✔ Eventos sempre emitidos em casos importantes
19. Conclusão

Este documento formaliza os padrões de código da plataforma:

controllers finos

services com lógica

repositories para banco

DTOs para transporte

jobs para tarefas pesadas

eventos para integrações

logs estruturados

validações centralizadas

API response padrão

boas práticas de segurança e organização

Deve ser seguido por toda a equipe e por automações com IA.