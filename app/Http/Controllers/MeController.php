<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class MeController extends Controller
{
    /**
     * Perfil autenticado (dados + empresas + roles/permissões).
     *
     * @group Perfil
     */
    public function show(Request $request): JsonResponse
    {
        $user = $request->user()->load([
            'tenant:id,public_id,nome,slug',
            'empresas:id,public_id,tenant_id,razao_social,nome_fantasia,cnpj',
            'roles:id,name,tenant_id',
            'permissions:id,name,tenant_id',
        ]);

        $empresas = $user->empresas->map(function ($empresa) {
            return [
                'id' => $empresa->id,
                'public_id' => $empresa->public_id,
                'razao_social' => $empresa->razao_social,
                'nome_fantasia' => $empresa->nome_fantasia,
                'cnpj' => $empresa->cnpj,
                'perfil' => $empresa->pivot->perfil ?? null,
            ];
        });

        return response()->json([
            'user' => [
                'id' => $user->id,
                'public_id' => $user->public_id,
                'name' => $user->name,
                'email' => $user->email,
                'tenant' => $user->tenant ? [
                    'id' => $user->tenant->id,
                    'public_id' => $user->tenant->public_id,
                    'nome' => $user->tenant->nome,
                    'slug' => $user->tenant->slug,
                ] : null,
                'roles' => $user->getRoleNames(),
                'permissions' => $user->getPermissionNames(),
            ],
            'empresas' => $empresas,
        ]);
    }
}
