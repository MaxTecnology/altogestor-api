<?php

namespace App\Http\Controllers\Documentos;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreSolicitacaoDocumentoRequest;
use App\Models\Empresa;
use App\Models\ModeloDocumento;
use App\Models\SolicitacaoDocumento;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SolicitacaoDocumentoController extends Controller
{
    /**
     * Lista solicitações de documentos do usuário (por empresas vinculadas).
     *
     * @group Documentos
     * @header X-Tenant-ID string required Public ID do tenant.
     * @queryParam empresa_id integer Filtrar por empresa_id. Opcional.
     * @queryParam status string Filtrar por status. Opcional.
     */
    public function index(Request $request): JsonResponse
    {
        $empresasPermitidas = $request->user()->empresas()->pluck('empresas.id');

        $query = SolicitacaoDocumento::query()
            ->with(['empresa:id,public_id,razao_social,nome_fantasia,cnpj', 'modelo:id,public_id,nome'])
            ->whereIn('empresa_id', $empresasPermitidas)
            ->orderByDesc('id');

        if ($request->filled('empresa_id')) {
            $query->where('empresa_id', $request->input('empresa_id'));
        }

        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        $solicitacoes = $query->paginate(20);

        return response()->json($solicitacoes);
    }

    /**
     * Cria uma solicitação de documento para uma empresa.
     *
     * @group Documentos
     * @header X-Tenant-ID string required Public ID do tenant.
     */
    public function store(StoreSolicitacaoDocumentoRequest $request): JsonResponse
    {
        $user = $request->user();
        $empresaId = $request->input('empresa_id');
        $modeloId = $request->input('modelo_documento_id');

        $temVinculo = $user->empresas()->where('empresas.id', $empresaId)->exists();
        if (! $temVinculo) {
            abort(403, 'empresa_not_allowed');
        }

        $empresa = Empresa::query()->where('id', $empresaId)->where('tenant_id', $user->tenant_id)->first();
        if (! $empresa) {
            abort(404, 'empresa_not_found');
        }

        $modelo = ModeloDocumento::query()->where('id', $modeloId)->where('tenant_id', $user->tenant_id)->first();
        if (! $modelo) {
            abort(404, 'modelo_not_found');
        }

        $solicitacao = SolicitacaoDocumento::query()->create([
            'empresa_id' => $empresa->id,
            'modelo_documento_id' => $modelo->id,
            'competencia' => $request->input('competencia'),
            'observacao' => $request->input('observacao'),
            'status' => 'PENDENTE',
            'gerada_automaticamente' => false,
        ]);

        return response()->json(['data' => $solicitacao->load(['empresa:id,public_id,razao_social', 'modelo:id,public_id,nome'])], 201);
    }

    /**
     * Detalhe de uma solicitação.
     *
     * @group Documentos
     * @header X-Tenant-ID string required Public ID do tenant.
     */
    public function show(int $id, Request $request): JsonResponse
    {
        $empresasPermitidas = $request->user()->empresas()->pluck('empresas.id');

        $solicitacao = SolicitacaoDocumento::query()
            ->with(['empresa:id,public_id,razao_social,nome_fantasia,cnpj', 'modelo:id,public_id,nome', 'documentos', 'historicos'])
            ->where('id', $id)
            ->whereIn('empresa_id', $empresasPermitidas)
            ->first();

        if (! $solicitacao) {
            abort(404, 'solicitacao_not_found');
        }

        return response()->json(['data' => $solicitacao]);
    }
}
