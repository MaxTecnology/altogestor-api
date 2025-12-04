<?php

namespace App\Http\Controllers\Documentos;

use App\Http\Controllers\Controller;
use App\Http\Requests\UploadDocumentoRequest;
use App\Models\Documento;
use App\Models\DocumentoHistorico;
use App\Models\SolicitacaoDocumento;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Storage;

class UploadDocumentoController extends Controller
{
    /**
     * Upload de documento para uma solicitação.
     *
     * @group Documentos
     * @header X-Tenant-ID string required Public ID do tenant.
     */
    public function __invoke(int $solicitacaoId, UploadDocumentoRequest $request): JsonResponse
    {
        $user = $request->user();
        $empresasPermitidas = $user->empresas()->pluck('empresas.id');

        $solicitacao = SolicitacaoDocumento::query()
            ->where('id', $solicitacaoId)
            ->whereIn('empresa_id', $empresasPermitidas)
            ->first();

        if (! $solicitacao) {
            abort(404, 'solicitacao_not_found');
        }

        $arquivo = $request->file('arquivo');
        $tenantId = $user->tenant_id;
        $path = $arquivo->store("documents/tenant_{$tenantId}/solicitacao_{$solicitacao->id}", 'public');

        $documento = Documento::query()->create([
            'solicitacao_documento_id' => $solicitacao->id,
            'user_id' => $user->id,
            'nome_original' => $arquivo->getClientOriginalName(),
            'caminho' => $path,
            'mime_type' => $arquivo->getClientMimeType(),
            'tamanho_bytes' => $arquivo->getSize(),
            'origem' => 'portal',
            'status' => 'ENVIADO',
        ]);

        DocumentoHistorico::query()->create([
            'tenant_id' => $tenantId,
            'solicitacao_documento_id' => $solicitacao->id,
            'documento_id' => $documento->id,
            'user_id' => $user->id,
            'status' => 'ENVIADO',
            'observacao' => $request->input('observacao'),
        ]);

        // Atualiza status da solicitação para PARCIAL se havia pendência, senão mantém
        if (in_array($solicitacao->status, ['PENDENTE', 'INCOMPLETO', 'RECUSADO'])) {
            $solicitacao->update(['status' => 'PARCIAL']);
        }

        return response()->json([
            'data' => $documento,
            'url' => Storage::disk('public')->url($path),
        ], 201);
    }
}
