<?php

namespace App\Http\Controllers;

use App\Http\Requests\SwitchEmpresaRequest;
use App\Models\Empresa;
use Illuminate\Http\JsonResponse;

class SwitchEmpresaController extends Controller
{
    /**
     * Seleciona empresa ativa do usuário no tenant atual.
     *
     * @group Perfil
     * @header X-Tenant-ID string required Public ID do tenant.
     * @bodyParam empresa_id integer required ID interno da empresa (do tenant atual). Exemplo: 1
     */
    public function __invoke(SwitchEmpresaRequest $request): JsonResponse
    {
        $user = $request->user();
        $tenantId = $user->tenant_id;

        $empresa = Empresa::query()
            ->where('tenant_id', $tenantId)
            ->where(function ($q) use ($request) {
                $q->where('id', $request->integer('empresa_id'))
                    ->orWhere('public_id', $request->input('empresa_id'));
            })
            ->first();

        if (! $empresa) {
            abort(404, 'empresa_not_found');
        }

        $temVinculo = $user->empresas()->where('empresas.id', $empresa->id)->exists();
        if (! $temVinculo) {
            abort(403, 'empresa_not_allowed');
        }

        // No momento não persistimos "empresa ativa" em sessão/token; o front mantém a seleção.
        return response()->json([
            'empresa' => [
                'id' => $empresa->id,
                'public_id' => $empresa->public_id,
                'razao_social' => $empresa->razao_social,
                'nome_fantasia' => $empresa->nome_fantasia,
                'cnpj' => $empresa->cnpj,
            ],
        ]);
    }
}
