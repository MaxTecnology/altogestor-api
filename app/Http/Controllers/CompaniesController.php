<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CompaniesController extends Controller
{
    /**
     * Lista empresas vinculadas ao usuário no tenant atual.
     *
     * @group Perfil
     * @header X-Tenant-ID string required Public ID do tenant.
     */
    public function index(Request $request): JsonResponse
    {
        $empresas = $request->user()
            ->empresas()
            ->select(['empresas.id', 'empresas.public_id', 'empresas.tenant_id', 'empresas.razao_social', 'empresas.nome_fantasia', 'empresas.cnpj'])
            ->get()
            ->map(function ($empresa) {
                return [
                    'id' => $empresa->id,
                    'public_id' => $empresa->public_id,
                    'razao_social' => $empresa->razao_social,
                    'nome_fantasia' => $empresa->nome_fantasia,
                    'cnpj' => $empresa->cnpj,
                    'perfil' => $empresa->pivot->perfil ?? null,
                ];
            });

        return response()->json(['data' => $empresas]);
    }
}
